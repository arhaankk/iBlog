<?php
require_once('../util/IB.php');
$app = IB::app();
$session = $app->getClass('IB\Session');
$posts = $app->getClass('IB\Posts');
$postComments = $app->getClass('IB\PostComments');
$users = $app->getClass('IB\Users');
$data = file_get_contents('php://input');

$pid = 0;
if (isset($_REQUEST['post']) && filter_var($_REQUEST['post'], FILTER_VALIDATE_INT))
	$pid = intval($_REQUEST['post']);
$post = $posts->get(['id' => $pid]);
$post = count($post) < 1 ? null : $post[0];
if (!$post)
	$app->error_plain('The specified post does not exist.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!$session->isAuthenticated())
		$app->error_plain('Posting a comment requires authentication.');
	if (empty($data))
		$app->error_plain('Your comment may not be empty.');
	if (strlen($data) > 256)
		$app->error_plain('Your comment must be 256 characters or less.');
	$user = $session->getUser();
	$comment = array(
		'content' => $data,
		'postId' => $post['id'],
		'userId' => $user['id'],
	);
	$postComments->add($comment);
	header('Content-Type: text/plain');
	die('Comment posted successfully.');
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$comments = array();
	foreach ($postComments->get(['postId' => $post['id']]) as $v) {
		$author = $users->get(['id' => $v['userId']])[0];
		$comments[] = array(
			'userId' => $author['id'],
			'username' => $author['username'],
			'displayname' => $author['displayname'],
			'content' => $v['content'],
			'created_at' => $v['created_at'],
		);
	}
	header('Content-Type: application/json');
	echo json_encode($comments);
} else {
	$app->error('Request type is unsupported.', 'Bad request', 'Type: '.$_SERVER['REQUEST_METHOD'], 401);
}
