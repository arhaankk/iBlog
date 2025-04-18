<?php
require_once(__DIR__.'/IB.php');

// Function to generate HTML for a single post
function generatePostHtml($post, $pdo, $full=true)
{
	$app = IB::app();
	$db = $app->getClass('IB\Db');
	$users = $app->getClass('IB\Users');
	$session = $app->getClass('IB\Session');
	$posts = $app->getClass('IB\Posts');
	$pdo = $db->connect();
	$page = $app->getClass('IB\Page');
	//$pdo = databaseConnection();

	// Fetch basic info for all posts in one query
	$r = $posts->get(['id' => $post['id']]);
	if (empty($r)) {
		$pid = $post['id'];
		$app->error("Attempted to load a post that does not exist in the database (ID: $pid)", 'Failed to load post', code: 500);
	}
	$post = $r[0];

	// Fetch images for the post
	$imageStmt = $pdo->prepare("
		SELECT imageData FROM postImages WHERE postId = :postId
	");
	$imageStmt->execute([':postId' => $post['id']]);
	$images = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

	// Fetch author's username
	$author = $users->get(['id' => $post['userId']])[0];

	// Start generating HTML
	$html = '<section class="card--medium">';
	$html .= '<div class="blog-content">';
	$html .= '<img src="'.$page->data('actions').'/avatar.php?user='.$post['userId'].'" alt="'.$author['displayname'].'\'s Profile Picture" class="post-avatar">';
	$html .= '<strong class="post-user-name"><a href="'.$page->data('pages').'/posts.php?user='.$author['id'].'">' . htmlspecialchars($author['displayname']) . '</a></strong>';
	$html .= '<h2>' . htmlspecialchars($post['title']) . '</h2>';
	if ($full) {
		$html .= '<span><small>Topic: <a href="'.$page->data('pages').'/search/search.php?topic='.urlencode($post['topic']).'" class="button">' . htmlspecialchars($post['topic']) . '</a></small></span>';
		$html .= '<span>&nbsp;—&nbsp;<small>Views: <b>'.$post['views'].'</b> '.($post['views'] === 1 ? 'view' : 'views').'</small></span>';
	}
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
			//$html .= '<p>No images available for this blog post.</p>';
		}
		$html .= '</div>';
	} else {
		$html .= '<div><a href="'.$page->data('pages').'/single-post-view.php?id='.$post['id'].'" class="button">Full Post</a></div>';

	}
	$html .= '</div>';

	// Add comments
	// These are handled dynamically by the client
	if ($full) {
		$html .= '<div class="comments"><details open><summary><h3 style="display: inline;">Comments</h3></summary>';
		$html .= '<p class="placeholder">Loading comments…</p>';
		$postId = $post['id'];
		$formStyle = !$session->isAuthenticated() ? ' style="display: none;"' : '';
		$html .= <<<form
<div class="comment-form"$formStyle>
	<form id="commentForm" class="form--inline" data-post="$postId">
		<label for="commentInput"></label><input type="text" id="commentInput" placeholder="Add a comment...">
		<button type="button">Submit</button>
	</form>
</div>
form;
		$html .= '</details></div>';
	}
	$html .= '</section>';

	return $html;
}

?>
