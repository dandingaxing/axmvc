<?php

return array(
  'cache' => array(
    'redis' => array(
      'type' => 'redis',
      'options' => array(
        'scheme' => 'tcp',
        'host'   => '127.0.0.1',
        'port'   => 6379,
      ),
    ),
    'file' => array(
      'type' => 'file',
      'options' => array(
        'dir' => dirname( dirname(__FILE__) ) . '/fileCache',
      ),
    ),
    // 'mysql' => array(
    //   'type' => 'mysql',
    //   'options' => array(
    //   ),
    //   // '_alias' => '@db/mysql',
    // ),
    // 'memcached' => array(
    //   "host"=> "127.0.0.1",
    //   "port"=> "11211"
    // ),
    // 'mysql' => array(
    // ),
    // 'session' => array(
    //   '_alias' => '@cache/redis',
    // ),


  ),
);


