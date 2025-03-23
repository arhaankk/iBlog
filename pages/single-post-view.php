<?php
require_once('../util/IB.php');
$app = IB::app();
$db = $app->getClass('IB\Db');
$session = $app->getClass('IB\Session');

$pdo = $db->connect();

if (!isset($_GET['id'])) {
    die("id required");
} else {
    //Fetch post basic info
    $postId = $_GET['id'];
    $stmt = $pdo->prepare("
        SELECT * FROM blog WHERE id = :postId
    ");
    $stmt->execute([':postId' => $postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch additional info associated with the blog post
    if (!$post) {
        die("Blog post not found.");
    } else {
        //get post images
        $stmt = $pdo->prepare("
            SELECT imageData FROM postImages WHERE postId = :postId
        ");
        $stmt->execute([':postId' => $postId]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //get author's username by userid
        $stmt = $pdo->prepare("
            SELECT username FROM users WHERE id = :userId
        ");
        $stmt->execute([':userId' => $post['userId']]);
        $authorUserName = $stmt->fetchColumn();
        //todo retrieve author's image from db

        //todo retrieve comments from db
    }
}

/* Write page */
$page = $app->getClass('IB\Page');
$page->setTitle($post['title']);
$page->preamble();
?>

<main>
    <section class="card--medium">
        <div class="blog-content">
            <img src="<?php echo $page->data('actions')."/avatar.php?id=".$post['userId']; ?>" alt="User1 Profile Picture" class="post-avatar">
            <!-- Placeholder image,todo fetch author's image from db -->
            <strong class="post-user-name"><?php echo $authorUserName; ?>: </strong>
            <h2>
                <?php echo $post['title']; ?>
            </h2>
            <div class="blog-content-text">
                <p>
                    <?php echo $post['content']; ?>
                </p>
            </div>
            <div class="blog-content-image">
                <?php if (!empty($images)) {
                    echo "<h3>Images:</h3>";
                    foreach ($images as $image) {
                        // Encode the binary image data into Base64
                        $imageData = base64_encode($image['imageData']);
                        $imageSrc = 'data:image/jpeg;base64,' . $imageData; // Adjust MIME type as needed

                        // Render the image
                        echo "<img src='$imageSrc' alt='Blog Content Image' class='blog-content-img'>";
                    }
                } else {
                    echo "<p>No images available for this blog post.</p>";
                } ?>
            </div>
        </div>


        <!--        todo handle comments from db-->
<!--        <div class="comments">-->
<!--            <h3>Comments</h3>-->
<!--            <div class="comment">-->
<!--                <img src="../images/profilePic.jpg" alt="User1 Profile Picture" class="comment-avatar">-->
<!--                <strong>Jack: </strong> Great post!-->
<!--            </div>-->
<!--            <div class="comment">-->
<!--                <img src="../images/profilePic2.jpg" alt="User2 Profile Picture" class="comment-avatar">-->
<!--                <strong>Tom: </strong> Thanks for sharing!-->
<!--            </div>-->
<!--            <div class="comment-form">-->
<!--                <form id="commentForm" class="form--inline" action="addComment.php" method="POST">-->
<!--                    <input type="text" id="commentInput" placeholder="Add a comment...">-->
<!--                    <button type="button" onclick="addComment(event)">Submit</button>-->
<!--                </form>-->
<!--            </div>-->
<!--        </div>-->
    </section>
</main>

<?php $page->epilogue(); ?>
