<?php

namespace common\lib;

use common\base;
use common\lib\http\CDbSession;
use common\lib\http\CHttpCookie;
use common\lib\http\CHttpSession;
use common\lib\http\CMemcacheSession;
use common\lib\http\CRedisSession;
use common\lib\http\Csrf;

class http{

    private static $_app;

    public static function app($name){
        $name = str_replace(array('.', '/', '\\'), '.', $name);
        $nameArr = explode('.', $name);

        if (strtolower($nameArr[1])=='cookie') {
            return new CHttpCookie();
        }elseif (strtolower($nameArr[1])=='session') {
            return new CDbSession();
        }elseif (strtolower($nameArr[1])=='redissession') {
            return new CRedisSession();
        }elseif (strtolower($nameArr[1])=='dbsession') {
            return new CDbSession();
        }elseif (strtolower($nameArr[1])=='memcachesession'){
            return new CMemcacheSession();
        }elseif (strtolower($nameArr[1])=='csrf') {
            $csrf = new Csrf();
            foreach (base::conf('http.csrf') as $k => $v) {
                $csrf->set($k, $v);
            }
            return $csrf;
        }else{
            throw new Exception("have no this component", 1);
        }
    }

}

