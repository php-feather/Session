<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

function dbSession(){
    

    $dbconfig=[
        'dsn'=>'mysql:host=localhost;dbname=test',
        'user'=>'root',
        'password'=>''
    ];
    
    $driver = new \Feather\Session\Drivers\DatabaseDriver($dbconfig);
    session_start();
    
}

function fileSession(){
    $driver = new \Feather\Session\Drivers\FileDriver(dirname(__FILE__));
    session_start();
}

function redisSession(){
    $driver = new \Feather\Session\Drivers\RedisDriver('localhost');
    session_start();
}

//fileSession();
//or
//dbSession();
//or
redisSession();

Feather\Session\Session::set('test','steve');

var_dump(Feather\Session\Session::get('test'));

Feather\Session\Session::flush();