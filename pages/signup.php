<?php
require_once('../util/IB.php');
$app = IB::app();
$users = $app->getClass('IB\Users');
$session = $app->getClass('IB\Session');
if ($session->isAuthenticated())
	$app->redirect('/');
$page = $app->getClass('IB\Page');
$page->setTitle('Sign up');
$page->addCrumb('Sign up', '{{PAGES}}/signup.php');
$page->preamble();
?>
<link rel="stylesheet" href="../styles/signin-signup.css">

<main>
	<h1>Welcome to iBlog</h1>
	<p>Please register with the form below.</p>
	<div class="login">
		<form action="../actions/signup.php" method="POST" class="login__form" enctype="multipart/form-data">
			<div class="login__content">
				<div style="display: flex; gap: 10px;">
					<div class="login__box" style="flex: 1;">
						<i class="fas fa-user login__icon"></i>
						<div class="login__box-input">
							<input type="text" id="firstname" name="firstname" required class="login__input" placeholder=" ">
							<label for="firstname" class="login__label">First Name</label>
						</div>
					</div>

					<div class="login__box" style="flex: 1;">
						<i class="fas fa-user login__icon"></i>
						<div class="login__box-input">
							<input type="text" id="lastname" name="lastname" required class="login__input" placeholder=" ">
							<label for="lastname" class="login__label">Last Name</label>
						</div>
					</div>
				</div>
				<div class="login__box">
					<i class="fas fa-envelope login__icon"></i>
					<div class="login__box-input">
						<input type="username" id="username" name="username" required class="login__input" placeholder=" ">
						<label for="username" class="login__label">Username</label>
					</div>
				</div>
				<div class="login__box">
					<i class="fas fa-envelope login__icon"></i>
					<div class="login__box-input">
						<input type="email" id="email" name="email" required class="login__input" placeholder=" ">
						<label for="email" class="login__label">Email</label>
					</div>
				</div>

				<div style="display: flex; gap: 10px;">
					<div class="login__box" style="flex: 1;">
						<i class="fas fa-lock login__icon"></i>
						<div class="login__box-input">
							<input type="password" id="password" name="password" required class="login__input" placeholder=" ">
							<label for="password" class="login__label">Password</label>
						</div>
					</div>

					<div class="login__box" style="flex: 1;">
						<i class="fas fa-lock login__icon"></i>
						<div class="login__box-input">
							<input type="password" id="confirm-password" name="confirm-password" required class="login__input" placeholder=" ">
							<label for="confirm-password" class="login__label">Confirm</label>
						</div>
					</div>
				</div>

				<div class="login__box">
					<i class="fas fa-venus-mars login__icon"></i>
					<div class="login__box-input" style="position: relative;">
						<select id="gender" name="gender" required class="login__input" style="appearance: none; background: transparent; border: none; width: 100%; padding: 10px; font-size: 16px; cursor: pointer;">
							<option value="" disabled selected></option>
							<option value="male">Male</option>
							<option value="female">Female</option>
							<option value="other">Other</option>
						</select>
						<label for="gender" class="login__label">Gender</label>
						<i class="fas fa-chevron-down" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none;"></i>
					</div>
				</div>

				<div class="login__box">
					<i class="fas fa-birthday-cake login__icon"></i>
					<div class="login__box-input">
						<input type="number" id="age" name="age" required class="login__input" placeholder=" " min="1">
						<label for="age" class="login__label">Age</label>
					</div>
				</div>
				<div class="login__box">
					<i class="fas fa-user login__icon"></i>
					<div class="login__box-input">
						<input type="file" id="avatar" name="avatar" class="login__input" accept="image/png, image/gif, image/jpeg">
						<label for="avatar" class="login__label">Profile Picture</label>
					</div>
				</div>
			</div>
		<button type="submit" class="login__button">Submit</button>

		<p class="login__register">
			Already have an account? <a href="signin.php">Login</a>
		</p>
	</form></div>
</main>

<script>
/* Temporary validation for the sign-up form */
document.querySelector('.login > form').addEventListener('submit', (e) => {
	const f = e.target.elements;
	if (f['password'].value !== f['confirm-password'].value) {
		e.preventDefault();
		f['password'].classList.add('aria-invalid');
		f['confirm-password'].classList.add('aria-invalid');
		alert('Password confirmation must match password');
		return;
	} else {
		f['password'].classList.remove('aria-invalid');
		f['confirm-password'].classList.remove('aria-invalid');
	}
	if (parseInt(f['age'].value) < 18 || parseInt(f['age'].value) > 150) {
		e.preventDefault();
		f['age'].classList.add('aria-invalid');
		alert('Age must be within 18 and 150');
	} else {
		f['age'].classList.remove('aria-invalid');
	}
});
</script>

<?php $page->epilogue(); ?>
