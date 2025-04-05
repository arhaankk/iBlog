<?php
require_once('../util/IB.php');
$app = IB::app();
$session = $app->getClass('IB\Session');
$users = $app->getClass('IB\Users');
if (!$session->isAuthenticated())
	$app->redirect('/signin.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	$app->error('Request type is unsupported.', 'Bad request', 'Type: '.$_SERVER['REQUEST_METHOD'], 401);
}
$user = $session->getUser();
$data = file_get_contents('php://input');

$mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($data);
if (strncmp($mime, "image/", 6) !== 0) {
	$app->error_plain('Invalid image data', 401);
}
$users->setImage($user['id'], $data);
