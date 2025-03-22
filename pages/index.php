<?php
require_once('../util/IB.php');
$app = IB::app();
$session = $app->getClass('IB\Session');
$page = $app->getClass('IB\Page');
$page->setTitle('Home');
$page->preamble();
?>

<main>
    <h1>iBlog</h1>
    <p>Welcome to my iBlog!</p>

    <?php if (!$session->isAuthenticated()) { ?>
    <a href="<?php echo $page->data('pages'); ?>/signin.html" class="button">Log In</a>
    <?php } else { ?>
    <a href="<?php echo $page->data('pages'); ?>blog-write.html" class="button">Write Post</a>
    <?php } ?>
    <hr>
    <!-- TODO: Replace placeholder -->
    <h2>Recent activity</h2>
        <div class="card--mini">
            <img src="../images/profilePic.jpg" alt="User1 Profile Picture" class="post-avatar">
            <strong class="post-user-name">Cynthia: </strong>
            <h2>Blog Title 1</h2>
            <div class="blog-content-text">
                <p class="text--preview">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                </p>
            </div>
            <div>
				<a href="blogCard.html" class="button">Full Post</a>
            </div>
        </div>
</main>

<?php $page->epilogue(); ?>
