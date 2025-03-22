<?php
// Include database configuration
require '../database/db_config.php';

// Start session to retrieve the logged-in user (assuming session is used for login)
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.html"); // Redirect to sign-in page if the user is not logged in
    exit();
}

// Retrieve current user's ID from session
$user_id = $_SESSION['user_id'];

// Fetch current user profile data from the database
$query = "SELECT first_name, last_name, email, gender, age, profile_img FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first-name"]);
    $last_name = trim($_POST["last-name"]);
    $email = trim($_POST["email"]);
    $gender = $_POST["gender"];
    $age = intval($_POST["age"]);

    // Hash the password if it was changed (optional, only if password needs to be updated)
    $password = $_POST["password"];
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    }

    // Handle profile image upload
    $profile_img = NULL;
    if (isset($_FILES["avatar"]) && $_FILES["avatar"]["size"] > 0) {
        $profile_img = file_get_contents($_FILES["avatar"]["tmp_name"]);
    } else {
        // If no new profile image, use the existing one
        $profile_img = $user['profile_img'];
    }

    // Prepare SQL statement to update the profile information
    $query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, gender = ?, age = ?, profile_img = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssisi", $first_name, $last_name, $email, $gender, $age, $profile_img, $user_id);

    if ($stmt->execute()) {
        // Redirect to the profile page after successful update
        header("Location: profile.html");
        exit();
    } else {
        die("Error: " . $stmt->error); // Handle error
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile — iBlog</title>
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="../styles/signin-signup.css">
    <meta name="description" content="Edit your profile on iBlog">
</head>
<body>
    <nav>
        <span class="header-brand"><a href="index.html">iBlog</a></span>
        <ul class="header-nav">
            <li><a href="index.html">Home</a></li>
            <li><a href="blogCard.html">My Blogs</a></li>
            <li><a href="blog-write.html">Write</a></li>
        </ul>
        <div class="header-acct">
            <div class="dropdown"><a href="profile.html">Profile</a>
                <ul class="dropdown">
                    <li><a href="admin.html">Admin panel</a></li>
                    <li><a href="signin.html">Sign in</a></li>
                    <li><a href="signup.html">Sign up</a></li>
                    <li><a href="#">Log out</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <h1>Edit Your Profile</h1>
        <form action="editProfile.php" method="POST" enctype="multipart/form-data" class="login__form">
            <div class="login__content">
                <div style="display: flex; gap: 10px;">
                    <div class="login__box" style="flex: 1;">
                        <i class="fas fa-user login__icon"></i>
                        <div class="login__box-input">
                            <input type="text" id="first-name" name="first-name" required class="login__input" value="<?= $user['first_name'] ?>">
                            <label for="first-name" class="login__label">First Name</label>
                        </div>
                    </div>

                    <div class="login__box" style="flex: 1;">
                        <i class="fas fa-user login__icon"></i>
                        <div class="login__box-input">
                            <input type="text" id="last-name" name="last-name" required class="login__input" value="<?= $user['last_name'] ?>">
                            <label for="last-name" class="login__label">Last Name</label>
                        </div>
                    </div>
                </div>

                <div class="login__box">
                    <i class="fas fa-envelope login__icon"></i>
                    <div class="login__box-input">
                        <input type="email" id="email" name="email" required class="login__input" value="<?= $user['email'] ?>">
                        <label for="email" class="login__label">Email</label>
                    </div>
                </div>

                <div class="login__box">
                    <i class="fas fa-venus-mars login__icon"></i>
                    <div class="login__box-input">
                        <input type="text" id="gender" name="gender" required class="login__input" value="<?= $user['gender'] ?>">
                        <label for="gender" class="login__label">Gender</label>
                    </div>
                </div>

                <div class="login__box">
                    <i class="fas fa-birthday-cake login__icon"></i>
                    <div class="login__box-input">
                        <input type="number" id="age" name="age" required class="login__input" value="<?= $user['age'] ?>">
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
            <button type="submit" class="login__button">Save Changes</button>
        </form>
    </main>

    <footer>
        <small>&copy; iBlog 2025</small>
    </footer>
</body>
</html>
