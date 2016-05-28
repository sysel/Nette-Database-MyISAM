<?php
/**
 * Tests bootstrap using Nette Tester (http://tester.nette.org/)
 */

$options = array(
	'dsn' => 'mysql:host=127.0.0.1',
	'user' => 'root',
	'password' => '',
	'driverClass' => '\\Sysel\\Nette\\Database\\Drivers\\MySqlMyIsamDriver',
);

require_once __DIR__ . '/../vendor/autoload.php';

// Task files
require_once __DIR__ . '/../MySqlMyIsamDriver.php';
