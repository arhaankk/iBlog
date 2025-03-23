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
	<a href="<?php echo $page->data('pages'); ?>/signup.php" class="button">Sign up</a>
	<a href="<?php echo $page->data('pages'); ?>/signin.php" class="button">Sign in</a>
	<?php } else { ?>
	<a href="<?php echo $page->data('pages'); ?>/posts.php?user=<?php echo $session->getUser()['id'] ?>" class="button">My Posts</a>
	<a href="<?php echo $page->data('pages'); ?>/blog-write.php" class="button">Write Post</a>
	<?php } ?>
	<a href="<?php echo $page->data('pages'); ?>/search/search.php" class="button">Search Posts</a>
	<hr>
	<!-- TODO: Replace placeholder -->
	<h2>Recent activity</h2>
	<p>Look at all these posts happening on the platform!</p>
	<?php
		require_once('../util/blog-card.php');
		$posts = $app->getClass('IB\Posts');
		$cards = $posts->get([], limit: 10);
		foreach ($cards as $card)
			echo generatePostHtml($card, null, false);
	?>
</main>

<?php $page->epilogue(); ?>
