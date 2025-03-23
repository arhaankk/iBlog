<?php
require_once('../util/IB.php');
$app = IB::app();
$users = $app->getClass('IB\Users');
$session = $app->getClass('IB\Session');
if ($session->isAuthenticated())
	$app->redirect('/');

/* Handle login */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (empty($_POST['user']) || empty($_POST['pass'])) {
		$app->error('The username and password are required');
	}
	$user = $_POST['user'];

	$user = $users->get(['username' => $user]);
	if (count($user) < 1) {
		$app->error('This user was not found');
	}
	$user = $user[0];
	$pass = $_POST['pass'];
	if (!password_verify($pass, $user['password'])) {
		$app->error('The password was incorrect.');
	}
	/* If all is good, log them in! */
	$session->setUser($user['id']);
	$app->redirect('/');
}

/* Generate page */
$page = $app->getClass('IB\Page');
$page->setTitle('Sign in');
$page->preamble();
?>
<link rel="stylesheet" href="<?php echo $page->data('root'); ?>/styles/signin-signup.css">
<video autoplay muted loop class="body__video"
		src="<?php echo $page->data('root'); ?>/assets/bg.mp4"
		poster="<?php echo $page->data('root'); ?>/assets/bg.jpeg" type="video/mp4">
</video>

<main>
	<h1>Welcome to iBlog</h1>
	<p>Please sign in with the form below.</p>
	<div class="login">
		<form action="signin.php" method="POST" class="login__form">
			<div class="login__content">
				<div class="login__box">
					<i class="fas fa-envelope login__icon"></i>
					<div class="login__box-input">
						<input type="text" id="user" name="user" required class="login__input" placeholder=" ">
						<label for="user" class="login__label">Username</label>
					</div>
				</div>

				<div class="login__box">
					<i class="fas fa-lock login__icon"></i>
					<div class="login__box-input">
					 <input type="password" name="pass" required class="login__input" id="login-pass" placeholder=" ">
					 <label for="login-pass" class="login__label">Password</label>
				  </div>
				</div>
			</div>

			<div class="login__check">
				<div class="login__check-group">
					<input type="checkbox" id="remember" class="login__check-input">
					<label for="remember" class="login__check-label">Remember me</label>
				</div>
				<a href="#" class="login__forgot">Forgot Password?</a>
			</div>

			<button type="submit" class="login__button">Login</button>

			<p class="login__register">
				Don't have an account? <a href="../pages/signup.html">Register</a>
			</p>
		</form>
	</div>
</main>

<?php $page->epilogue(); ?>
