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
$json = json_decode($data, true);

try {
	$params = array(
		'firstname' => $json['firstname'],
		'lastname' => $json['lastname'],
		'email' => $json['email'],
		'gender' => $json['gender'],
		'age' => $json['age'],
	);
} catch (Error $e) {
	echo $e;
	$app->error_plain('Missing parameters', 401);
}
if ($params['age'] < 18 || $params['age'] > 150) {
	$app->error_plain('Age is out of range (18-150)', 401);
}

$users->setUser($user['id'], $params);
