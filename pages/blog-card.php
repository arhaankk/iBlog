<?php
require_once('../../util/IB.php');
$app = IB::app();
$db = $app->getClass('IB\Db');
$page = $app->getClass('IB\Page');
$pdo = $db->connect();

// Function to generate HTML for a single post
function generatePostHtml($post, $pdo, $full=true)
{
    global $page;
    //$pdo = databaseConnection();

    // Fetch basic info for all posts in one query
    $stmt = $pdo->prepare("
        SELECT * FROM blog WHERE id IN (:postId)
    ");
    $stmt->execute([':postId' => $post['id']]);
    $posts = $stmt->fetchColumn();

    // If no posts are found, exit
    if (empty($posts)) {
        die("No posts found.");
    }

    // Fetch images for the post
    $imageStmt = $pdo->prepare("
        SELECT imageData FROM postImages WHERE postId = :postId
    ");
    $imageStmt->execute([':postId' => $post['id']]);
    $images = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch author's username
    $authorStmt = $pdo->prepare("
        SELECT username FROM users WHERE id = :userId
    ");
    $authorStmt->execute([':userId' => $post['userId']]);
    $authorUserName = $authorStmt->fetchColumn();

    // Start generating HTML
    $html = '<section class="card--medium">';
    $html .= '<div class="blog-content">';
    $html .= '<img src="../../actions/avatar.php?user='.$post['userId'].'" alt="User Profile Picture" class="post-avatar">';
    $html .= '<strong class="post-user-name">' . htmlspecialchars($authorUserName) . ': </strong>';
    $html .= '<h2>' . htmlspecialchars($post['title']) . '</h2>';
    $html .= '<div class="blog-content-text">';
    $html .= '<p>' . htmlspecialchars($post['content']) . '</p>';
    $html .= '</div>';

    // Add images section
    if ($full) {
        $html .= '<div class="blog-content-image">';
        if (!empty($images)) {
            $html .= '<h3>Images:</h3>';
            foreach ($images as $image) {
                $imageData = base64_encode($image['imageData']);
                $imageSrc = 'data:image/jpeg;base64,' . $imageData;
                $html .= '<img src="' . $imageSrc . '" alt="Blog Content Image" class="blog-content-img">';
            }
        } else {
            $html .= '<p>No images available for this blog post.</p>';
        }
        $html .= '</div>';
    } else {
        $html .= '<div><a href="'.$page->data('pages').'/single-post-view.php?id='.$post['id'].'" class="button">Full Post</a></div>';

    }
    $html .= '</div>';
    $html .= '</section>';

    return $html;
}

?>
