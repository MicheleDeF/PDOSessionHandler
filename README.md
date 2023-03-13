# PDOSessionHandler
Store data in a database using PDO and the SessionHandlerInterface interface


>Create session table

```sh
CREATE TABLE session ( 
`id` varchar(256) NOT NULL, 
`name` varchar(256) NOT NULL,
`value` longtext, 
`last_update` int(11) NOT NULL, 
PRIMARY KEY (`id`,`name`) ) ENGINE = INNODB;

```

## Install via Composer

