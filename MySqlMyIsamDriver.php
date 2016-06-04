<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 */

namespace NetteExtras\Database;

use Nette;


/**
 * Supplemental MySQL database driver.
 *
 * @author     David Grudl
 * @author     Vojtech Sysel
 */
class MySqlMyIsamDriver extends Nette\Database\Drivers\MySqlDriver
{
	/** @var Nette\Database\Connection */
	private $connection;

	/**
	 * Driver options:
	 *   - charset => character encoding to set (default is utf8)
	 *   - sqlmode => see http://dev.mysql.com/doc/refman/5.0/en/server-sql-mode.html
	 */
	public function __construct(Nette\Database\Connection $connection, array $options)
	{
		parent::__construct($connection, $options);
		$this->connection = $connection;
	}


	/**
	 * Returns metadata for all foreign keys in a table.
	 */
	public function getForeignKeys($table)
	{
		$keys = array();
		$query = 'SELECT \'CONSTRAINT_NAME\', C.COLUMN_NAME, C2.TABLE_NAME AS `REFERENCED_TABLE_NAME`, C2.COLUMN_NAME AS `REFERENCED_COLUMN_NAME` '
					. 'FROM information_schema.COLUMNS C '
						. 'JOIN information_schema.TABLES T ON T.TABLE_SCHEMA = C.TABLE_SCHEMA '
						. 'JOIN information_schema.COLUMNS C2 ON C2.TABLE_SCHEMA = C.TABLE_SCHEMA '
					. 'WHERE 1 '
						. 'AND C.TABLE_SCHEMA = DATABASE() '
						. 'AND C.TABLE_NAME = ' . $this->connection->quote($table) . ' '
						. 'AND C.COLUMN_KEY != \'\' '
						. 'AND C2.TABLE_NAME = T.TABLE_NAME '
						. 'AND ( '
							. '( '
								. 'C.COLUMN_COMMENT REGEXP \'^\\s*@refs [a-zA-Z_]+\\.[a-zA-Z_]+\' '
								. 'AND C.COLUMN_COMMENT LIKE CONCAT(\'@refs \', C2.TABLE_NAME, \'\\.\', C2.COLUMN_NAME, \'%\') '
							. ') '
							. 'OR ( '
								. 'C.COLUMN_NAME LIKE CONCAT(T.TABLE_NAME, \'\\_%\') '
								. 'AND REPLACE(C.COLUMN_NAME, CONCAT(T.TABLE_NAME, \'_\'), \'\') = C2.COLUMN_NAME'
							. ') '
						. ')';

		foreach ($this->connection->query($query) as $id => $row) {
			$keys[$id]['name'] = 'FK_'.$table.'_'.$row['REFERENCED_TABLE_NAME'].'_'.$row['REFERENCED_COLUMN_NAME']; // foreign key name
			$keys[$id]['local'] = $row['COLUMN_NAME']; // local columns
			$keys[$id]['table'] = $row['REFERENCED_TABLE_NAME']; // referenced table
			$keys[$id]['foreign'] = $row['REFERENCED_COLUMN_NAME']; // referenced columns
		}

		return array_values($keys);
	}
}
