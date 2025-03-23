<?php
require_once('../util/IB.php');
$app = IB::app();
$users = $app->getClass('IB\Users');
$session = $app->getClass('IB\Session');
if (!$session->isAdmin())
	$app->redirect('/');
$page = $app->getClass('IB\Page');
$page->setTitle('Administration');
$page->setDescription('The administrator page for iBlog.');
$page->preamble();
?>

<main>
	<h1>Administration</h1>
	<p>Use this page to manage user accounts and site settings.
	<div class="panes">
	<section class="pane">
		<h2>Options</h2>
		<ul>
			<li><a href="#">Settings</a>
			<li class="active"><a href="#">Accounts</a>
		</ul>
	</section>
	<section class="pane">
		<h2>Accounts</h2>
		<ul>
			<li><a href="#">Arhaan</a>
			<li><a href="#">Bradan</a>
			<li class="active"><a href="#">Germain</a>
		</ul>
	</section>
	<section class="pane">
		<h2>Manage account</h2>
		<form method="POST">
			<label for="acct-name">Name:</label>
			<input type="text" name="name" id="acct-name" maxlength="64" placeholder="John Doe" content="Germain" required>
			<label for="acct-email">Email:</label>
			<input type="email" name="email" id="acct-email" maxlength="64" placeholder="john.doe@example.com" required>
			<label for="acct-pass">Password: </label>
			<input type="text" name="pass" id="acct-pass" maxlength="64">
			<hr>
			<div class="actions">
				<input type="submit" name="update" value="Update">
				<input type="submit" name="delete" value="Delete">
				<input type="reset" value="Reset">
			</div>
		</form>
	</section>
	</div>
</main>

<?php $page->epilogue(); ?>
