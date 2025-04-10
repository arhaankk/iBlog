<?php
require_once('../util/IB.php');
$app = IB::app();
$users = $app->getClass('IB\Users');
$posts = $app->getClass('IB\Posts');
$db = $app->getClass('IB\Db');
$session = $app->getClass('IB\Session');
if (!$session->isAuthenticated())
	$app->redirect('/signin.php');

/* Handle form submission */
$userId = $session->getUser()['id'];
$pdo = $db->connect();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['title', 'topic', 'content'] as $field)
        if (!isset($_POST[$field]) || empty(trim($_POST[$field])))
            $app->error("Missing field: $field", 'Invalid request', code: 401);

    $title = trim($_POST['title']);
    $topic = trim($_POST['topic']);
    $content = trim($_POST['content']);

    // Handle image uploads
    $imageBlobs = [];
    if (isset($_FILES['images']) && $_FILES['images']['error'][0] !== UPLOAD_ERR_NO_FILE) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            // Check for upload errors
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $maxFileSize = 10 * 1024 * 1024; // 10 MB in bytes
                if ($_FILES['images']['size'][$key] > $maxFileSize) {
                    die("File too large: " . htmlspecialchars($_FILES['images']['name'][$key]) . ". Maximum allowed size is 10 MB.");
                }

                // Verify if the file is a valid image
                $imageInfo = getimagesize($tmpName); // Returns false if not a valid image
                if ($imageInfo === false) {
                    die("Invalid image file: " . htmlspecialchars($_FILES['images']['name'][$key]));
                }

                $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
                if (!in_array($imageInfo[2], $allowedTypes)) {
                    die("Unsupported image type: " . htmlspecialchars($_FILES['images']['name'][$key]));
                }

                $imageBlobs[] = file_get_contents($tmpName);
            } else {
                die("Error uploading file: " . htmlspecialchars($_FILES['images']['name'][$key]));
            }
        }
    }

    // Insert blog post into the database
    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Insert the blog post
        $post = array(
            'title' => $title,
            'topic' => $topic,
            'content' => $content,
            'userId' => $userId);
        $err = $posts->validate($post);
        if ($err !== null)
            $app->error($err, 'Invalid post', '', code: 401);
        $postId = $posts->add($post);

        // Insert images into the imageDB table
        if (!empty($imageBlobs)) {
            $stmt = $pdo->prepare("
                INSERT INTO postImages (postId, imageData)
                VALUES (:postId, :imageData)
            ");
            foreach ($imageBlobs as $imageBlob) {
                $stmt->execute([
                    ':postId' => $postId,
                    ':imageData' => $imageBlob,
                ]);
            }
        }

        // Commit transaction
        $pdo->commit();
        echo "Blog post created successfully! Redirecting in 1.5 seconds...";

        // Use JavaScript for client-side redirection
        echo <<<HTML
            <script>
                setTimeout(function() {
                    window.location.href = "single-post-view.php?id=$postId";
                }, 1500); // Redirect after 1.5 seconds
            </script>
HTML;

        exit();
    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        $app->error('An SQL error occurred while creating the blog post.', 'Internal error', $e->getMessage(), code: 500);
    }
}

/* Write page */
$page = $app->getClass('IB\Page');
$page->setTitle('Write Post');
$page->setDescription('The writing page for iBlog.');
$page->addCrumb('Write Post', '{{PAGES}}/blog-write.php');
$page->preamble();
?>

<main>
    <h1>Write Your Blog Post</h1>
    <p>Get typing!</p>
    <form id="blogForm" class="card" action="<?php echo $page->data('pages'); ?>/blog-write.php" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="topic">Topic:</label>
        <select id="topic" name="topic" required>
<?php
    $topics = $app->config('topics');
    foreach ($topics as $topic)
        echo "<option>$topic</option>";
?>
        </select>

        <label for="content">Content:</label>
        <textarea id="content" class="writer" name="content" rows="10" required></textarea>

        <label for="image">Upload Image:</label>
		<input type="file" id="image" name="images[]" accept="image/*" multiple>
        <hr>
        <button type="submit" class="button--active">Publish</button>
    </form>
</main>
<script src="../scripts/blog-write.js"></script>

<?php $page->epilogue(); ?>
