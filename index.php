<?php

require 'vendor/autoload.php';

/**
 * for db session
 * table name = feather_session
 * create table feather_session (
  'id' varchar(50) primary key not null,
  'data' mediumtext,
  'access' int(10) unsigned default null
  )
 *
 */
function dbSession()
{


    $dbconfig = [
        'dsn' => 'mysql:host=localhost;dbname=test',
        'user' => 'root',
        'password' => ''
    ];

    $driver = new \Feather\Session\Drivers\DatabaseDriver($dbconfig);
    $driver->start();
}

function fileSession()
{
    $driver = new \Feather\Session\Drivers\FileDriver(dirname(__FILE__));
    $driver->start();
}

function redisSession()
{
    $driver = new \Feather\Session\Drivers\RedisDriver('localhost');
    $driver->start();
}

//fileSession();
//or
//dbSession();
//or
redisSession();

Feather\Session\Session::set('test', 'steve');

var_dump(Feather\Session\Session::get('test'));

Feather\Session\Session::flush();
