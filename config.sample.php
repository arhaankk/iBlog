<?php
/**
 * The sample configuration for iBlog.
 * Values in config.sample.php will be overwritten by values in config.php.
 */

$config['web']['hostname'] = 'localhost:8080';
$config['web']['tls'] = false;
$config['web']['path'] = '/';

$config['db']['host'] = 'localhost';
$config['db']['port'] = 3306;
$config['db']['database'] = 'app';
$config['db']['user'] = 'AzureDiamond';
$config['db']['pass'] = 'hunter2';

$config['topics'] = ['Personal', 'Food', 'Travel'];

$config['debug'] = false;
