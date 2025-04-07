<?php
/**
 * Admin endpoint for information.
 * This could be extended to users, but I like the domain separation.
 */

require_once('../util/IB.php');
$app = IB::app();
$session = $app->getClass('IB\Session');
$db = $app->getClass('IB\Db');
$data = file_get_contents('php://input');

if (!$session->isAdmin())
	$app->error_plain('Unauthorized request — you must be an admin', 401);
if ($_SERVER['REQUEST_METHOD'] !== 'GET')
	$app->error_plain('Unsupported request type', 400);
if (!isset($_GET['query']))
	$app->error_plain('Unspecified query parameter', 400);

switch ($_GET['query']) {
case 'stats':
	/* Get stats */
	$stats = array();
	$r = $db->select('COUNT(1)', 'users', []);
	$stats['User count'] = $r[0][0];
	$r = $db->select('COUNT(1)', 'blog', []);
	$stats['Post count'] = $r[0][0];
	$r = $db->select('COUNT(1)', 'postComments', []);
	$stats['Comment count'] = $r[0][0];
	$r = $db->select('COUNT(1)', 'postImages', []);
	$stats['Images uploaded'] = $r[0][0];
	/* Return */
	header('Content-Type: application/json');
	echo json_encode($stats);
	break;
case 'config':
	/* Flatten results */
	$db_config = $app->config('db');
	$results = array(
		'hostname' => $app->config('web')['hostname'],
		'tls' => $app->config('web')['tls'],
		'debug' => $app->config('debug'),
		'db_uri' => $db_config['host'],
		'db_port' => $db_config['port'],
		'db_user' => $db_config['user'],
		'db_name' => $db_config['database'],
	);
	/* Return */
	header('Content-Type: application/json');
	echo json_encode($results);
	break;
}
