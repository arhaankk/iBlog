<?php
require_once('../util/IB.php');
$app = IB::app();
$session = $app->getClass('IB\Session');
$users = $app->getClass('IB\Users');
$db = $app->getClass('IB\Db');

/* Get target user */
$uid = 0;
if (isset($_REQUEST['user']) && filter_var($_REQUEST['user'], FILTER_VALIDATE_INT))
	$uid = intval($_REQUEST['user']);
$target = $users->get(['id' => $uid]);
if (count($target) < 1)
	$app->error('This user does not exist.', 'User not found', "ID: $uid");
$target = $target[0];
$you = ($session->isAuthenticated() && $session->getUser()['id'] === $uid);

$page = $app->getClass('IB\Page');
$page->setTitle($target['displayname'].'\'s Posts');
$page->setDescription('View blog posts from '.$target['displayname'].' on iBlog!');
$page->addCrumb($target['displayname'].'\'s Posts', '{{PAGES}}/posts.php?user='.$target['id']);
$page->preamble();
?>

<main>
	<h1><?php echo $page->data('title'); ?></h1>
	<p>Welcome to my blog!</p>
	<?php
		if ($you) {
			echo '<a href="'.$page->data('pages').'/blog-write.php" class="button">Write Post</a>';
			echo '<a href="'.$page->data('pages').'/profile.php" class="button">Update Profile</a>';
		}
	?>
	<hr>
	<?php
		require_once('../util/blog-card.php');
		$posts = $app->getClass('IB\Posts');
		$cards = $posts->get(['userId' => $target['id']]);
		foreach ($cards as $card)
			echo generatePostHtml($card, null, false);
		if (count($cards) < 1) {
			if ($you)
				echo '<p>You have not posted yet.<br>Use the button above to write your first post!</p>';
			else
				echo '<p>This user has not posted yet.</p>';
		}
	?>
</main>

<?php $page->epilogue(); ?>

