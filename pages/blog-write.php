<?php
session_start();

// Hardcoded user ID (todo replace this with dynamic user ID after implementing authentication)
$userId = mt_rand(1, 5); //random 1-5

include 'databaseConnection.php';
$pdo = databaseConnection(); // Use '127.0.0.1' if localhost fails

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['content'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        die("Title and content are required.");
    }

    // Handle image uploads
    $imageBlobs = [];
    if (isset($_FILES['images']) && $_FILES['images']['error'][0] !== UPLOAD_ERR_NO_FILE) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $maxFileSize = 10 * 1024 * 1024; // 10 MB in bytes
                if ($_FILES['images']['size'][$key] > $maxFileSize) {
                    die("File too large: " . htmlspecialchars($_FILES['images']['name'][$key]) . ". Maximum allowed size is 10 MB.");
                }

                $imageInfo = getimagesize($tmpName);
                if ($imageInfo === false) {
                    die("Invalid image file: " . htmlspecialchars($_FILES['images']['name'][$key]));
                }

                // Restrict allowed image types (JPEG, PNG, GIF)
                $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
                if (!in_array($imageInfo[2], $allowedTypes)) {
                    die("Unsupported image type: " . htmlspecialchars($_FILES['images']['name'][$key]));
                }

                // Read the uploaded file into a BLOB
                $imageBlobs[] = file_get_contents($tmpName);
            } else {
                die("Error uploading file: " . htmlspecialchars($_FILES['images']['name'][$key]));
            }
        }
    }

    try {
        // Begin transaction
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO blog (title, content, userId)
            VALUES (:title, :content, :userId)
        ");
        $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':userId' => $userId,
        ]);

        $postId = $pdo->lastInsertId();

        // Insert images into the imageDB table
        if (!empty($imageBlobs)) {
            $stmt = $pdo->prepare("
                INSERT INTO postImages (postId, imageData)
                VALUES (:postId, :imageData)
            ");
            foreach ($imageBlobs as $imageBlob) {
                $stmt->execute([
                    ':postId' => $postId,
                    ':imageData' => $imageBlob,
                ]);
            }
        }

        // Commit transaction
        $pdo->commit();
        echo "Blog post created successfully! Redirecting in 1.5 seconds...";

        // Use JavaScript for client-side redirection
        echo <<<HTML
            <script>
                setTimeout(function() {
                    window.location.href = "single-post-view.php?id=$postId";
                }, 1500); // Redirect after 1.5 seconds
            </script>
HTML;

        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error creating blog post: " . $e->getMessage());
    }
}