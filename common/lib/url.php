<?php

namespace common\lib;
use Rize\UriTemplate;

class url{

    private static $_instance;
    private $_uri;

    public static function app($name){
        if(! (self::$_instance instanceof self) )
        {
            self::$_instance = new self();
            self::$_instance->_uri = self::$_instance->makeUriTemplate();
        }
        return self::$_instance;
    }

    public function makeUriTemplate($uri='', $params=array()){
        return new UriTemplate($uri, $params);
    }

    public function to($uri, $params){
        return self::$_uri->expand($uri, $params);
    }

    // 快速url构建
    public function admin($path, $params=array(), $hosts=''){
        $preg = empty($hosts) ? "/admin.do/{+r}{+params*}" : rtrim($hosts, '/') . "/admin.do/{+r}{&params*}";
        $url = $this->_uri->expand($preg, array('r'=>'r='.$path, 'params'=> empty($params) ? null : '&'.urldecode(http_build_query($params))) );
        return $url;
    }


}




