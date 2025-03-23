<?php
require_once('../util/IB.php');
$app = IB::app();
$users = $app->getClass('IB\Users');

$uid = 0;
if (isset($_REQUEST['user']) && filter_var($_REQUEST['user'], FILTER_VALIDATE_INT))
	$uid = intval($_REQUEST['user']);

/* Attempt to display an image from the database */
$pfp = $users->getImage($uid);
if (!empty($pfp)) {
	header('Content-Type: '.$pfp['mime']);
	echo $pfp['data'];
	exit();
}

/* Default to the default user */
$default_path = IB::getRootDir().'/images/default.jpg';
if (!file_exists($default_path)) {
	$app->error('Default avatar not found.', 'Failed to display avatar', $default_path);
}
header('Content-Type: image/jpeg');
echo file_get_contents($default_path);
exit();
