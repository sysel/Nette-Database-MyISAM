Nette Database MySQL MyISAM driver
==================================

Nette Database doesn't support MySQL MyISAM tables by default because thi database storage doesn't store informations about foreign keys. Unfortunately, there are still servers which does support only MyISAM storage tables because they consume more system resources. This driver enables you to used Nette database on those servers.

## Requirements

* [Nette](http://nette.org/ "Nette Framework") for PHP 5.3 (tested on version 2.2.2)
* table's referenced columns names shall be in format 'table'_'column_name', like author_id or shall have @refs table.column_name in column's comment. The schema can look like:

        |  Category                        |
        | Column name  | Column comment    |
        +----------------------------------+
        | id           | Category id       |
        | name         | Category name     |
    
        |  Text                                           |
        | Column name  | Column comment                   |
        +-------------------------------------------------+
        | id           | Text id                          |
        | category_id  | Some comment                     |
        | category     | @refs category.id Some comment :)|
        | text         | Text content                     |

## Installation

Copy driver to libs/NetteExtras dir or somewhere else where robot loader can find it.

## Use

Create database connection:

    $connection = new \Nette\Database\Connection(
                            'mysql:host='.$servername.';dbname='.$database,
                            $user, $password, NULL,
                            'NetteExtras\Database\MySqlMyIsamDriver'
                        );

Or you can add it to you config.neon:

    services:
        database:
            class: Nette\Database\Connection(
                        '%database.driver%:host=%database.host%;dbname=%database.dbname%',
                        %database.user%, %database.password%, NULL,
                        NetteExtras\Database\MySqlMyIsamDriver
                    )
            setup:
                - setCacheStorage(@cacheStorage)


