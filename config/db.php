<?php

return array(
  'db' => array(
    'mysql' => array(
      'database_type' => 'mysql',
      'database_name' => 'news',
      'server' => '127.0.0.1',
      'username' => 'root',
      'password' => 'shaoxing',
      'charset' => 'utf8',
      'port' => 3306,
      
      'option' => [
        PDO::ATTR_CASE => PDO::CASE_NATURAL
      ],

      // [optional] Enable logging (Logging is disabled by default for better performance)
      'logging' => true,

      // [optional] Medoo will execute those commands after connected to the database for initialization
      'command' => [
        'SET SQL_MODE=ANSI_QUOTES'
      ]

    ),

    'mysqli' => array(
      'database_type' => 'mysql',
      'database_name' => 'news',
      'server' => '127.0.0.1',
      'username' => 'root',
      'password' => 'shaoxing',
      'charset' => 'utf8',
      'port' => 3306,
      
      'option' => [
        PDO::ATTR_CASE => PDO::CASE_NATURAL
      ],

      // [optional] Enable logging (Logging is disabled by default for better performance)
      'logging' => true,

      // [optional] Medoo will execute those commands after connected to the database for initialization
      'command' => [
        'SET SQL_MODE=ANSI_QUOTES'
      ]      
    ),

  ),




);


/*
// Using Medoo namespace
use Medoo\Medoo;
$database = new Medoo([
  // required
  'database_type' => 'mysql',
  'database_name' => 'name',
  'server' => 'localhost',
  'username' => 'your_username',
  'password' => 'your_password',
 
  // [optional]
  'charset' => 'utf8',
  'port' => 3306,
 
  // [optional] Table prefix
  'prefix' => 'PREFIX_',
 
  // [optional] Enable logging (Logging is disabled by default for better performance)
  'logging' => true,
 
  // [optional] MySQL socket (shouldn't be used with server and port)
  'socket' => '/tmp/mysql.sock',
 
  // [optional] driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
  'option' => [
    PDO::ATTR_CASE => PDO::CASE_NATURAL
  ],
 
  // [optional] Medoo will execute those commands after connected to the database for initialization
  'command' => [
    'SET SQL_MODE=ANSI_QUOTES'
  ]
]);

 */
