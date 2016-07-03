# Nette Database MySQL MyISAM driver

[![Build Status](https://api.travis-ci.org/sysel/Nette-Database-MyISAM.svg?branch=master)](https://travis-ci.org/sysel/Nette-Database-MyISAM)

Nette Database doesn't support MySQL MyISAM tables by default because this database storage doesn't store information about foreign keys. Unfortunately, there are still servers which support only MyISAM storage tables because they don't consume too many system resources. This driver enables you to use Nette database on those servers.

## Requirements

* [Nette](http://nette.org/ "Nette Framework") for PHP 5.6+ (tested on version 2.4.0)
* table's referenced columns names shall be in format `table_columnName`, like `author_id` or shall have `@refs table.columnName` in column's comment. The schema can look like:

```
|          Category                |
| Column name  | Column comment    |
+----------------------------------+
| id           | Category id       |
| name         | Category name     |

|            Text                                 |
| Column name  | Column comment                   |
+-------------------------------------------------+
| id           | Text id                          |
| category_id  | Some comment                     |
| category     | @refs category.id Some comment :)|
| text         | Text content                     |
```

## Installation

Download and copy driver to `libs/Sysel/Nette-Database-MyISAM` dir or somewhere else where robot loader can find it. Or use composer

```bash
composer require sysel/nette-database-myisam
```

## Use

Create database connection:

```php
$options = [
    'driverClass' => '\\Sysel\\Nette\\Database\\Drivers\\MySqlMyIsamDriver',
];
$connection = new \Nette\Database\Connection(
        'mysql:host='.$servername.';dbname='.$database,
        $user,
        $password,
        $options
    );
```

Or you can add it to you config.neon:

```neon
database:
    default:
        dsn: '%database.driver%:host=%database.host%;dbname=%database.database%'
        user: %database.user%
        password: %database.password%
        conventions: discovered
        autowired: true
        options:
            driverClass: Sysel\Nette\Database\Drivers\MySqlMyIsamDriver
```

## Running tests

Please use [Composer](https://getcomposer.org/ "Composer - Dependency Manager for PHP") to download all dependencies.

```bash
composer update
```

Than run Nette Tester with your `php.ini` file configuration. The configuration is required for correct PDO class use.

```bash
# Linux users
vendor/bin/tester -c tests/php-unix.ini tests
# Windows users
vendor/bin/tester -c tests/php-win.ini tests
```


## Known limitations

Getting relations from information schema is not very effective therefore using cache storage is recommended. Despite of that first run can still take several seconds.
