<?php
require_once('../util/IB.php');
$app = IB::app();
$db = $app->getClass('IB\Db');
$users = $app->getClass('IB\Users');
$posts = $app->getClass('IB\Posts');
$session = $app->getClass('IB\Session');

$pdo = $db->connect();

if (!isset($_GET['id'])) {
    die("id required");
} else {
    //Fetch post basic info
    $postId = $_GET['id'];
    $post = $posts->get(['id' => $postId]);

    // Fetch additional info associated with the blog post
    if (count($post) < 1) {
        $app->error("Blog post not found.", code: 404);
    } else {
        $post = $post[0];
        //get post images
        $stmt = $pdo->prepare("
            SELECT imageData FROM postImages WHERE postId = :postId
        ");
        $stmt->execute([':postId' => $postId]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $author = $users->get(['id' => $post['userId']])[0];
}

/* Write page */
$page = $app->getClass('IB\Page');
$page->setTitle($post['title']);
$page->preamble();
?>

<main>
    <script src="../scripts/blogCard.js"></script>
    <?php
		require_once('../util/blog-card.php');
		echo generatePostHtml($post, null, true);
    ?>
</main>

<?php $page->epilogue(); ?>
