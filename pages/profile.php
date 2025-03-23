<?php
require_once('../util/IB.php');
$app = IB::app();
$session = $app->getClass('IB\Session');
if (!$session->isAuthenticated())
	$app->redirect('/signin.php');
$user = $session->getUser();
$page = $app->getClass('IB\Page');
$page->setTitle('Profile');
$page->setDescription('Manage your profile.');
$page->preamble();
?>
<link rel="stylesheet" href="../styles/signin-signup.css">

<main>
	<h1>Your Profile</h1>
	<p>Use the button below to edit these fields.</p>
	<div class="login" style="padding: 20px; border-radius: 10px;">
		<form action="profile.php" method="POST" class="login__form" class="pane">
				<div class="login__content">
					<div style="display: flex; gap: 10px;">
						<div class="login__box" style="flex: 1;">
							<i class="fas fa-user login__icon"></i>
							<div class="login__box-input">
								<input type="text" id="first-name" name="first-name" required class="login__input" placeholder=" " value="<?php echo $user['firstname'] ?>" disabled>
								<label for="first-name" class="login__label">First Name</label>
							</div>
						</div>

						<div class="login__box" style="flex: 1;">
							<i class="fas fa-user login__icon"></i>
							<div class="login__box-input">
								<input type="text" id="last-name" name="last-name" required class="login__input" placeholder=" " value="<?php echo $user['lastname'] ?>" disabled>
								<label for="last-name" class="login__label">Last Name</label>
							</div>
						</div>
					</div>

					<div class="login__box">
						<i class="fas fa-envelope login__icon"></i>
						<div class="login__box-input">
							<input type="email" id="email" name="email" required class="login__input" placeholder=" " value="<?php echo $user['email'] ?>" disabled>
							<label for="email" class="login__label">Email</label>
						</div>
					</div>

					<div class="login__box">
						<i class="fas fa-venus-mars login__icon"></i>
						<div class="login__box-input">
							<input style="text-transform: capitalize;" type="text" id="gender" name="gender" required class="login__input" placeholder=" " value="<?php echo $user['gender'] ?>" disabled>
							<label for="gender" class="login__label">Gender</label>
						</div>
					</div>

					<div class="login__box">
						<i class="fas fa-birthday-cake login__icon"></i>
						<div class="login__box-input">
							<input type="number" id="age" name="age" required class="login__input" placeholder=" " value="<?php echo $user['age'] ?>" disabled>
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
			<button type="button" id="edit-button" class="login__button">Edit Profile</button>
			<button type="button" id="save-button" class="login__button" style="display:none;">Save</button>
		</form>
	</div>
	<script src="../scripts/profile.js"></script>
</main>

<?php $page->epilogue(); ?>
