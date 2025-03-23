<?php
require_once('../util/IB.php');
$app = IB::app();
$app->getClass('IB\Session')->destroy();
$app->redirect('/');
