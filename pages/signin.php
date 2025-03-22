<?php
require_once('../util/IB.php');
$app = IB::app();
$session = $app->getClass('IB\Session');
if ($session->isAuthenticated())
	$app->redirect('/');
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
		<form action="login.php" method="POST" class="login__form">
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
					 <input type="password" required class="login__input" id="login-pass" placeholder=" ">
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
