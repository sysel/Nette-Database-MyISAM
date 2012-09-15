Nette Database MySQL MyISAM driver
==================================

Nette Database doesn't support MySQL MyISAM tables, with this driver is possible to use it.

## Requirements

* [Nette](http://nette.org/ "Nette Framework") for PHP 5.3
* tables referenced columns names has to be in format 'table'_'column_name', like author_id or has to have @refs table.column_name in column comment. Schema can look like:

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

## Instalation

Copy driver to libs/NetteExtras dir or somewhere else where robot loader can find it.

## Using

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


