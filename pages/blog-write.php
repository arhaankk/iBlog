<?php
require_once('../util/IB.php');
$app = IB::app();
$users = $app->getClass('IB\Users');
$db = $app->getClass('IB\Db');
$session = $app->getClass('IB\Session');
if (!$session->isAuthenticated())
	$app->redirect('/signin.php');

/* Handle form submission */
$userId = $session->getUser()['id'];
$pdo = $db->connect();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    // Validate inputs
    if (empty($title) || empty($content)) {
        die("Title and content are required.");
    }

    // Handle image uploads
    $imageBlobs = []; // Array to store multiple images as BLOBs
    if (isset($_FILES['images']) && $_FILES['images']['error'][0] !== UPLOAD_ERR_NO_FILE) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            // Check for upload errors
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                // Enforce maximum file size (10 MB)
                $maxFileSize = 10 * 1024 * 1024; // 10 MB in bytes
                if ($_FILES['images']['size'][$key] > $maxFileSize) {
                    die("File too large: " . htmlspecialchars($_FILES['images']['name'][$key]) . ". Maximum allowed size is 10 MB.");
                }

                // Verify if the file is a valid image
                $imageInfo = getimagesize($tmpName); // Returns false if not a valid image
                if ($imageInfo === false) {
                    die("Invalid image file: " . htmlspecialchars($_FILES['images']['name'][$key]));
                }

                // Optionally, restrict allowed image types (e.g., JPEG, PNG, GIF)
                $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
                if (!in_array($imageInfo[2], $allowedTypes)) {
                    die("Unsupported image type: " . htmlspecialchars($_FILES['images']['name'][$key]));
                }

                // Read the uploaded file into a BLOB
                $imageBlobs[] = file_get_contents($tmpName);
            } else {
                // Handle other upload errors
                die("Error uploading file: " . htmlspecialchars($_FILES['images']['name'][$key]));
            }
        }
    }

    // Insert blog post into the database
    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Insert the blog post
        $stmt = $pdo->prepare("
            INSERT INTO blog (title, content, userId)
            VALUES (:title, :content, :userId)
        ");
        $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':userId' => $userId,
        ]);

        // Get the last inserted ID
        $postId = $pdo->lastInsertId();

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
        // Display success message and redirect after 3 seconds
        echo "Blog post created successfully! Redirecting in 1.5 seconds...";

        // Use JavaScript for client-side redirection
        echo <<<HTML
            <script>
                setTimeout(function() {
                    window.location.href = "single-post-view.php?id=$postId";
                }, 1500); // Redirect after 1.5 seconds
            </script>
HTML;

        exit(); // Ensure no further code is executed
    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        die("Error creating blog post: " . $e->getMessage());
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

        <label for="content">Content:</label>
        <textarea id="content" class="writer" name="content" rows="10" required></textarea>

        <label for="image">Upload Image:</label>
		<input type="file" id="image" name="images[]" accept="image/*" multiple>
        <hr>
        <button type="submit" class="button--active">Publish</button>
    </form>
</main>
<script src="scripts/blog-write.js"></script>

<?php $page->epilogue(); ?>
