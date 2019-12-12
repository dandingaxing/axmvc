<?php

namespace common\lib;
use common\base;

use Medoo\Medoo;

class db{

    private static $_app;

    public static function app($name){
        $name = str_replace(array('.', '/', '\\'), '.', $name);
        return isset(self::$_app[$name]) ? self::$_app[$name] : self::newDB($name);
    }

    // 实例化数据库类
    private static function newDB($name){
        $config = base::conf($name);
        self::$_app[$name] = new \Medoo\Medoo($config);
        return self::$_app[$name];
    }





}


