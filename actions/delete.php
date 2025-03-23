<?php
/**
 * Delete posts.
 * In the future, this may support more objects (e.g., if ?user=1 is passed).
 */

require_once('../util/IB.php');
$app = IB::app();
$session = $app->getClass('IB\Session');
$posts = $app->getClass('IB\Posts');
$postComments = $app->getClass('IB\PostComments');
$users = $app->getClass('IB\Users');
$data = file_get_contents('php://input');

/* Validation */
if (!$session->isAdmin())
	$app->redirect('/');
if ($_SERVER['REQUEST_METHOD'] !== 'GET')
	$app->error('Request type is unsupported.', 'Bad request', 'Type: '.$_SERVER['REQUEST_METHOD'], 401);

$pid = 0;
if (isset($_REQUEST['post']) && filter_var($_REQUEST['post'], FILTER_VALIDATE_INT))
	$pid = intval($_REQUEST['post']);
$post = $posts->get(['id' => $pid]);
$post = count($post) < 1 ? null : $post[0];
if (!$post)
	$app->error_plain('The specified post does not exist.');

/* Deletion */
$posts->delete(['id' => $pid]);
$app->redirect('/posts.php?user='.$post['userId']);
