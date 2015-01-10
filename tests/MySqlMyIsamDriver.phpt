<?php
/**
 * TEST: MySQL MyISAM Driver test
 *
 * @author Vojtech Sysel
 */

namespace NetteExtras\Database\Test;

use Nette,
	NetteExtras\Database,
	Tester,
	Tester\Assert;

require_once __DIR__ . '/bootstrap.php';

/**
 * @testcase
 */
class MyIsamDriverTest extends Tester\TestCase
{
	/** @var array $options Test options */
	private $options;

	/**
	 * @param array Test options
	 */
	public function __construct($options) {
		$this->options = $options;
	}

	public function testRelatedColumns() {
		// arrange
		$connection = new Nette\Database\Connection($this->options['dsn'], $this->options['user'], $this->options['password'], $this->options);
		$cacheMemoryStorage = new Nette\Caching\Storages\MemoryStorage;
		$reflection = new Nette\Database\Reflection\DiscoveredReflection($connection, $cacheMemoryStorage);
		$context = new Nette\Database\Context($connection, $reflection, $cacheMemoryStorage);

		Nette\Database\Helpers::loadFromFile($connection, __DIR__ . "/files/mysql-nette_test1.sql");

		// act
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

		// assert
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
		Assert::same($expected, $result);
	}
}

$testCase = new MyIsamDriverTest($options);
$testCase->run();
