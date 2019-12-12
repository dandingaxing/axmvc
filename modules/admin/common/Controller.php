<?php

namespace modules\admin\common;

use common\base;
use common\controller\adminbaseController;

class Controller extends adminbaseController{

    public $layout = '/include/main';

    // 初始实例化
    public function init(){
        parent::init();
    }

    public function createUrl($url, $params=array(), $host=''){
        // print_r($this);
        // exit();
        $urlArr = explode('/', trim(trim($url), '/'));
        $mArr = array();
        if (count($urlArr)===1) {
            $mArr['m'] = $this->get('_mName');
            $mArr['c'] = $this->get('_cName');
            $mArr['v'] = $urlArr[0];
        }elseif (count($urlArr)===2) {
            $mArr['m'] = $this->get('_mName');
            $mArr['c'] = $urlArr[0];
            $mArr['v'] = $urlArr[1];
        }elseif (count($urlArr)===3) {
            $mArr['m'] = $urlArr[0];
            $mArr['c'] = $urlArr[1];
            $mArr['v'] = $urlArr[2];
        }else{
            $mArr['m'] = $urlArr[0];
            $mArr['c'] = $urlArr[1];
            $mArr['v'] = $urlArr[2];
        }
        return base::app('url')->admin($mArr['m'].'/'.$mArr['c'].'/'.$mArr['v'], $params, $host);
    }

    /**
     * URL重定向
     * @param string $url 重定向的URL地址
     * @param integer $time 重定向的等待时间（秒）
     * @param string $msg 重定向前的提示信息
     * @return void
     */
    public function redirect($url, $time = 0, $msg = '')
    {
        //多行URL地址支持
        $url = str_replace(array("\n", "\r"), '', $url);
        if (empty($msg)) {
            $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
        }

        if (!headers_sent()) {
            // redirect
            if (0 === $time) {
                header('Location: ' . $url);
            } else {
                header("refresh:{$time};url={$url}");
                echo ($msg);
            }
            exit();
        } else {
            $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
            if (0 != $time) {
                $str .= $msg;
            }

            exit($str);
        }
    }

    // 成功跳转
    public function success(){
        
    }

    // 失败跳转
    public function error(){

    }




}

