<?php
require '../database/db_config.php'; // Ensure database connection is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first-name"]);
    $last_name = trim($_POST["last-name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm-password"]);
    $gender = $_POST["gender"];
    $age = intval($_POST["age"]);
    
    // Validate inputs
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password) || empty($gender) || empty($age)) {
        die("All fields are required.");
    }
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }
    if ($age < 18 || $age > 150) {
        die("Age must be between 18 and 150.");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle profile image upload
    $profile_img = NULL;
    if (isset($_FILES["avatar"]) && $_FILES["avatar"]["size"] > 0) {
        $profile_img = file_get_contents($_FILES["avatar"]["tmp_name"]);
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, gender, age, profile_img) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $first_name, $last_name, $email, $hashed_password, $gender, $age, $profile_img);
    
    if ($stmt->execute()) {
        header("Location: ../pages/signin.html");
        exit();
    } else {
        die("Error: " . $stmt->error);
    }

    $stmt->close();
}
?>
