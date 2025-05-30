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

/* Log page views */
if ($session->isAuthenticated())
    $posts->addView($post['id'], $session->getUser()['id']);

/* Write page */
$page = $app->getClass('IB\Page');
$page->setTitle($post['title']);
$page->setTitle($post['title']);
$page->addCrumb($author['displayname'].'\'s Posts', '{{PAGES}}/posts.php?user='.$author['id']);
$page->addCrumb($post['title'], '{{PAGES}}/single-post-view.php?id='.$post['id']);
$page->preamble();
?>

<main>
    <script src="../scripts/blogCard.js"></script>
    <?php
		require_once('../util/blog-card.php');
		echo generatePostHtml($post, null, true);
    ?>
    <?php if ($session->isAdmin()) {
        echo '<h2>Options</h2>';
        echo '<a href="'.$page->data('actions').'/delete.php?post='.$post['id'].'" class="button">Delete Post</a>';
    } ?>
</main>

<?php $page->epilogue(); ?>
