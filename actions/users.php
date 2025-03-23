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
	$conditions = [];
	if (isset($_GET['username'])) {
		$conditions['username'] = '%'.$_GET['username'].'%';
	}
	if (isset($_GET['email'])) {
		$conditions['email'] = '%'.$_GET['email'].'%';
	}
	/* Return filtered results */
	$results = array();
	foreach ($users->get($conditions, search: true) as $v) {
		$results[] = array(
			'userId' => $v['id'],
			'username' => $v['username'],
			'displayname' => $v['displayname'],
			'email' => $v['email'],
			'disabled' => $v['disabled'],
		);
	}
	header('Content-Type: application/json');
	echo json_encode($results);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
	/* Perform update (TODO) */
	$app->error('Not yet implemented');
} else {
	$app->error('Request type is unsupported.', 'Bad request', 'Type: '.$_SERVER['REQUEST_METHOD'], 401);
}
