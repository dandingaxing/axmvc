<?php

namespace common\controller;
use common\base;

// 各类 mvc 调度中控
class mvcbaseController{

    // 控制器文件后缀
    const CPREX = 'Controller';

    public function app($name){
        return base::app($name);
    }

    // mvc 
    public function admin($request, $response, $args){

        // // 获取mvc名称参数
        $getMvc = $this->adminbasemvc($args['r']);
        $c = sprintf('\modules\%s\controller\%s', $getMvc['_mName'], $getMvc['_cName'].self::CPREX);
        // 实例化mvc 中的Controller 并且给Controller赋初始值
        $thisController = new $c;

        foreach ($getMvc as $name => $mvc) {
            $thisController->set($name, $mvc);
        }

        // get参数赋值
        $_getParams = array();
        if (isset($args['params']) && !empty($args['params'])) {
            parse_str($args['params'],$_getParams);
        }
        (!empty($_getParams)) && $thisController->set('_getParams', $_getParams);

        // request response args 赋值
        $thisController->set('_request', $request);
        $thisController->set('_response', $response);
        $thisController->set('_args', $args);
        $v = $getMvc['_vName'];

        // 赋值模块路径
        $thisController->set('_mPath', rtrim(base::conf('main.modulesPath'), '/').'/'.$getMvc['_mName']);
        $thisController->set('_cFile', rtrim(base::conf('main.modulesPath'), '/').'/'.$getMvc['_mName'].'/controller/'.$getMvc['_cName'].self::CPREX.'.php');
        $thisController->set('_vPath', rtrim(base::conf('main.modulesPath'), '/').'/'.$getMvc['_mName'].'/view/'.$getMvc['_cName']);

        // 调用Controller中的方法
        return $thisController->$v();
    }

    public function adminbasemvc($r=''){
        $defaultMvc = array(
            '_mName' => 'admin',
            '_vName' => 'index',
            '_cName' => 'index',
            );
        $mvcArr =  explode('/', $r);
        $count = count($mvcArr);
        if ($count===0) {
            return $defaultMvc;
        }elseif ($count===1) {
            return array(
                '_mName' => $mvcArr[0],
                '_cName' => $defaultMvc['_cName'],
                '_vName' => $defaultMvc['_vName'],
            );
        }elseif ($count===2) {
            return array(
                '_mName' => $mvcArr[0],
                '_vName' => $defaultMvc['_vName'],
                '_cName' => $mvcArr[1],
            );
        }elseif ($count===3) {
            return array(
                '_mName' => $mvcArr[0],
                '_vName' => $mvcArr[2],
                '_cName' => $mvcArr[1],
            );
        }else{
            echo "error";
        }
    }





}

