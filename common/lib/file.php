<?php


namespace common\lib;
use common\base;
use common\lib\file\native;
use common\lib\file\aliyun;
use common\lib\file\upyun;



class file{

    private static $_app;

    public static function app($name){
        $name = str_replace(array('.', '/', '\\'), '.', $name);
        $nameArr = explode('.', $name);
        if ($nameArr[1]=='native') {
            $native = new native();
            $native->setConfig(base::conf('file.native'));
            // $native->setBasePath(base::conf('file.native.path'));
            // $native->setHost(base::conf('file.native.host'));
            return $native;
        }elseif ($nameArr[1]=='upyun') {
            if(! (upyun::$_instance ) )
            {
                upyun::$_instance = new upyun();
                upyun::$_instance->_config = upyun::$_instance->setConfig(base::conf('file.upyun'))->setClient();
            }
            return upyun::$_instance;
        }elseif ($nameArr[1]=='aliyun') {
            if ( ! (aliyun::$_instance) ) {
                aliyun::$_instance = new aliyun();
                aliyun::$_instance->_config = upyun::$_instance->setConfig(base::conf('file.upyun'))->setClient();
            }
            return aliyun::$_instance;
        }

    }


}




