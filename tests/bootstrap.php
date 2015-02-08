<?php
/**
 * Tests bootstrap using Nette Tester (http://tester.nette.org/)
 */

$options = array(
	'dsn' => 'mysql:host=127.0.0.1;dbname=test',
	'user' => 'root',
	'password' => '',
	'driverClass' => 'NetteExtras\\Database\\MySqlMyIsamDriver',
);

require_once __DIR__ . '/../vendor/autoload.php';

// Task files
require_once __DIR__ . '/../MySqlMyIsamDriver.php';
