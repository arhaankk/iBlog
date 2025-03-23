<?php
require_once('../util/IB.php');
$app = IB::app();
require '../util/db_config.php';

function sanitizeInput($data)
{
    global $connection;
    return mysqli_real_escape_string($connection, trim($data));
}

function hashPassword($password)
{
    return password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = sanitizeInput($_POST["firstname"]);
    $lastname = sanitizeInput($_POST["lastname"]);
    $username = sanitizeInput($_POST["username"]);
    $email = sanitizeInput($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm-password"];
    $gender = strtolower($_POST["gender"]);
    $age = intval($_POST["age"]);

    // Validate inputs
    if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($gender) || empty($age)) {
        die("All fields are required.");
    } elseif ($password !== $confirm_password) {
        die("Passwords do not match.");
    } elseif ($age < 18 || $age > 150) {
        die("Age must be between 18 and 150.");
    } else if (!in_array($gender, ['male', 'female', 'other'])) {
        die("Gender must be male, female, or other.");
    } else {
        $query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "User already exists with this name and/or email. <br>";
            echo "<a href='../pages/signup.php'>Return to user entry</a>";
        } else {
            $profile_img = NULL;
            if (isset($_FILES["avatar"]) && $_FILES["avatar"]["size"] > 0) {
                $profile_img = file_get_contents($_FILES["avatar"]["tmp_name"]);
            }
            $hashed_password = hashPassword($password);
            $insert_query = "INSERT INTO users (firstname, lastname, username, password, email, age, gender, profile_img) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $connection->prepare($insert_query);
            $insert_stmt->bind_param("sssssisb", $firstname, $lastname, $username, $hashed_password, $email, $age, $gender, $profile_img);
            mysqli_stmt_send_long_data($insert_stmt, 7, $profile_img);

            if ($insert_stmt->execute()) {
                echo "An account for the user " . htmlspecialchars($username) . " has been created<br><a href=\"../pages/signin.php\">Click here to log in</a>";
            } else {
                echo "Error: " . $insert_stmt->error;
            }
        }
    }
}
mysqli_close($connection)
?>
