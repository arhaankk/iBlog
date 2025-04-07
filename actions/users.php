<?php
/**
 * Admin endpoint for user management.
 */

require_once('../util/IB.php');
$app = IB::app();
$session = $app->getClass('IB\Session');
$users = $app->getClass('IB\Users');
$data = file_get_contents('php://input');

if (!$session->isAdmin())
	$app->redirect('/');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	/* Perform search */
	$search = true;
	$conditions = [];
	if (isset($_GET['id'])) {
		$conditions['id'] = $_GET['id'];
		$search = false; /* Disable search if ID specified */
	}
	if (isset($_GET['username'])) {
		$conditions['username'] = '%'.$_GET['username'].'%';
	}
	if (isset($_GET['email'])) {
		$conditions['email'] = '%'.$_GET['email'].'%';
	}
	/* Return filtered results */
	$results = array();
	foreach ($users->get($conditions, search: $search) as $v) {
		$results[] = array(
			'id' => $v['id'],
			'username' => $v['username'],
			'displayname' => $v['displayname'],
			'email' => $v['email'],
			'admin' => $v['admin'],
			'disabled' => $v['disabled'],
		);
	}
	header('Content-Type: application/json');
	echo json_encode($results);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
	/* Perform update */
	$uid = 0;
	$password = null;
	if (isset($_REQUEST['id']) && filter_var($_REQUEST['id'], FILTER_VALIDATE_INT))
		$uid = intval($_REQUEST['id']);
	/* Read params */
	/* Proper sanity checks should be added at some point */
	$json = json_decode($data, true);
	$params = array();
	if (isset($json['disabled']))
		$params[] = 'disabled = '.($json['disabled'] === 1 ? 1 : 0);
	if (isset($json['password']) && !empty($json['password']))
		$params['password'] = $users->hashPassword($json['password']);
	/* This is an admin endpoint, so data gets fed directly into the database */
	$users->setUser($uid, $params);
	header('Content-Type: text/plain');
	echo 'Success';
} else {
	$app->error('Request type is unsupported.', 'Bad request', 'Type: '.$_SERVER['REQUEST_METHOD'], 401);
}
