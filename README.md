# PDOSessionHandler
Store session data in a database using PDO and the SessionHandlerInterface interface

## Install via Composer
```sh
composer require micheledef/pdo-session-handler
```


Create session table

```sh
CREATE TABLE session ( 
`id` varchar(256) NOT NULL, 
`name` varchar(256) NOT NULL,
`value` longtext, 
`last_update` int(11) NOT NULL, 
PRIMARY KEY (`id`,`name`) ) ENGINE = INNODB;

```

To use the PDOSessionHandler session handler it is necessary to use the session_set_save_handler() function which accepts as an input parameter a class that implements the SessionHandlerInterface interface, so in our case we will proceed as follows

## Quick Start 
```sh
<?php

require 'vendor/autoload.php';

use Micheledef\PdoSessionHandler\PDOSessionHandler;

$username = "username";
$password = "password";
$databasename = "databasename";

$pdo = new PDO(
    "mysql:dbname=$databasename;host=localhost;",
    $username,
    $password
);
session_set_save_handler(new PDOSessionHandler($pdo));
```

To start using this data handler in session just execute the session_start() function

```sh
<?php

session_start();
```
in this way, each modification or reading of the $_SESSION global array will result in a modification of the session data stored in the session table that we have seen previously, this mode can be used to share session data between multiple servers.


