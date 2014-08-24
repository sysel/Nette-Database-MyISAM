<?php
require_once __DIR__ . '/../../../../Nette-2.2.2/Nette/loader.php';
require_once __DIR__ . '/../MySqlMyIsamDriver.php';

$options = array(
	'dsn' => 'mysql:host=127.0.0.1;dbname=test',
	'user' => 'root',
	'password' => '',
	'driverClass' => 'NetteExtras\\Database\\MySqlMyIsamDriver',
);

$connection = new Nette\Database\Connection($options['dsn'], $options['user'], $options['password'], $options);

$cacheMemoryStorage = new Nette\Caching\Storages\MemoryStorage;
$reflection = new Nette\Database\Reflection\DiscoveredReflection($connection, $cacheMemoryStorage);
$context = new Nette\Database\Context($connection, $reflection, $cacheMemoryStorage);

Nette\Database\Helpers::loadFromFile($connection, __DIR__ . "/files/mysql-nette_test1.sql");

$books = $context->table('book');

$result = array();
foreach ($books as $book) {
	$bookResult = array(
		'title' => $book->title,
		'author' => $book->author->name,
		'translator' => null,
		'tags' => array(),
	);
	if ($book->translator) {
		$bookResult['translator'] = $book->translator->name;
	}
	foreach ($book->related('book_tag') as $bookTag) {
		$bookResult['tags'][] = $bookTag->tag->name;
	}
	$result[] = $bookResult;
}

$expected = array(
	array(
		'title' => '1001 tipu a triku pro PHP',
		'author' => 'Jakub Vrana',
		'translator' => 'Jakub Vrana',
		'tags' => array('PHP', 'MySQL'),
	),
	
	array(
		'title' => 'JUSH',
		'author' => 'Jakub Vrana',
		'translator' => null,
		'tags' => array('JavaScript'),
	),
	
	array(
		'title' => 'Nette',
		'author' => 'David Grudl',
		'translator' => 'David Grudl',
		'tags' => array('PHP'),
	),
	
	array(
		'title' => 'Dibi',
		'author' => 'David Grudl',
		'translator' => 'David Grudl',
		'tags' => array('PHP', 'MySQL'),
	),
);

if ($result == $expected) {
	echo "Test ok.\n";
} else {
	echo "Test FAILED\n\n";
	var_dump($result);
	echo "Expected:\n";
	var_dump($expected);
}
