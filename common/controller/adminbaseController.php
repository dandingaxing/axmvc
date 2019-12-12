<?php

namespace common\controller;
use common\base;
use common\controller\baseController;

class adminbaseController extends baseController{

    private $_app;

    private $_mName;
    private $_mPath;

    private $_cName;
    private $_cFile;

    private $_vName;
    private $_vPath;
    private $_vFile;

    private $_getParams;

    private $_request;
    private $_response;
    private $_args;

    public $layout;

    public function init(){
        parent::init();
    }

    // 获取组件
    public static function app($name){
        return base::app($name);
    }

    // 设置参数
    public function set($name, $value){
        return $this->$name = $value;
    }

    // 获取参数
    public function get($name){
        return $this->$name;
    }

    // 获取 GET 参数
    public function getParams(){
        return $this->_getParams;
    }

    // 设置 GET 参数
    public function setParams($name, $value){
        $this->_getParams[$name] = $value;
        return $this->_getParams;
    }

    // 钩子 beforeAction 暂时不用
    public function beforeAction(){
    }

    // 钩子 afterAction 暂时不用
    public function afterAction(){
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


    /**
     * [ renderout 使用layout的方式渲染视图 对 render 的一个小的升级，将头尾 layout 到另一个框架布局模版中 ]
     * @param  [type] $view   [ 视图渲染文件参数 ]
     * @param  [type] $params [ 向视图传递的参数 ]
     * @return [type]         []
     */
    public function renderout($view,$params=null){
        $viewFile = $this->getVieFile($view);
        if (isset($this->layout) && !empty($this->layout) ) {
            $content = $this->renderFile($viewFile, $params, true);
            $this->render($this->layout, array('content'=>$content));
        }else{
            $this->renderFile($viewFile, $params);
        }
    }

    /**
     * [render 视图渲染与输出 ]
     * @param  [type] $view   [ 视图渲染文件参数。$view格式详见 getVieFile ]
     * @param  [type] $params [ 向视图传递的参数 ]
     * @return [type]         []
     */
    public function render($view, $params = null){
        $viewFile = $this->getVieFile($view);
        $this->renderFile($viewFile, $params);
    }

    /**
     * [getVieFile 获取要加载的视图文件路径 ]
     * @param  [type] $view [ 视图渲染文件参数 ]
     * @return [type]       []
     */
    public function getVieFile($view){
        $view = trim($view);
        if( substr($view, 0, 1) === '/' || strstr($view, '/') ){
            $this->_vFile = $this->_mPath . '/view/' . trim($view, '/') . '.php';
        }else{
            $this->_vFile = $this->_vPath . '/' . $view . '.php';
        }
        return $this->_vFile;
    }


    /**
     * [renderFile 加载视图文件渲染视图核心方法 ]
     * @param  string  $viewFile [ 视图文件系统绝对路径 ]
     * @param  array  $params   [ 向视图文件中传入的参数 ]
     * @param  boolean $return   [ 是否以变量的方式返回字符串 ]
     * @return string            [ 如果 $return 为true 则以字符串的方式返回视图内容 ]
     */
    public function renderFile($viewFile, $params=null, $return=false){
        if( !empty($params) && is_array($params) ){
            extract($params,EXTR_PREFIX_SAME,'data');
        }
        if ($return) {
            ob_start();
            // ob_implicit_flush(false);
            require($viewFile);
            return ob_get_clean();
        }else{
            require($viewFile);
        }
    }






}



