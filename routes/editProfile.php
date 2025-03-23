<?php
// Start the session
session_start();

// Include database configuration
require '../database/db_config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.html"); // Redirect to sign-in page if not logged in
    exit();
}

// Get the user ID from session
$user_id = $_SESSION['user_id'];

// Fetch user data from the database
$query = "SELECT first_name, last_name, email, gender, age, profile_img FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get updated data from form
    $first_name = trim($_POST["first-name"]);
    $last_name = trim($_POST["last-name"]);
    $email = trim($_POST["email"]);
    $gender = $_POST["gender"];
    $age = intval($_POST["age"]);

    // Handle profile image upload
    $profile_img = $user['profile_img']; // Keep old image if no new one is uploaded
    if (isset($_FILES["avatar"]) && $_FILES["avatar"]["size"] > 0) {
        $profile_img = file_get_contents($_FILES["avatar"]["tmp_name"]);
    }

    // Update user information in the database
    $update_query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, gender = ?, age = ?, profile_img = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssisi", $first_name, $last_name, $email, $gender, $age, $profile_img, $user_id);

    if ($stmt->execute()) {
        // Redirect back to profile page after successful update
        header("Location: profile.html");
        exit();
    } else {
        die("Error updating profile: " . $stmt->error);
    }

    $stmt->close();
}
?>
