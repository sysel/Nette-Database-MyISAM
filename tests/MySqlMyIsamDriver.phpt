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
 * @testCase
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

	/**
	 * Test driver functionality
	 */
	public function testRelatedColumns() {
		// arrange
		$connection = new Nette\Database\Connection($this->options['dsn'], $this->options['user'], $this->options['password'], $this->options);
		$cacheMemoryStorage = new Nette\Caching\Storages\MemoryStorage;
		$structure = new Nette\Database\Structure($connection, $cacheMemoryStorage);
		$conventions = new Nette\Database\Conventions\DiscoveredConventions($structure);
		$context = new Nette\Database\Context($connection, $structure, $conventions, $cacheMemoryStorage);

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

	/**
	 * Test driver intergration
	 */
	public function testExtensionIntegration() {
		// arrange
		$compiler = new Nette\DI\Compiler;
		$extension = new Nette\Bridges\DatabaseDI\DatabaseExtension;
		$extension->setCompiler($compiler, 'test');
		$extension->setConfig(array(
			'dsn' => $this->options['dsn'],
			'user' => $this->options['user'],
			'password' => $this->options['password'],
			'conventions' => 'discovered',
			'options' => array(
				'driverClass' => '\\NetteExtras\\Database\\MySqlMyIsamDriver',
			),
		));
		$extension->loadConfiguration();

		// act
		$definition = $extension->getContainerBuilder()->getDefinition('test.default');

		// assert
		Assert::truthy($definition);
	}
}

$testCase = new MyIsamDriverTest($options);
$testCase->run();
