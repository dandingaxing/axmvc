<?php

return array(

  // 路由
  'router' => array(
    
    // 配置
    'config'=>array(
        // 整体设置
        'settings' => [
            // 是否显示 debug 报错信息
            'displayErrorDetails' => false,

            // debug 配置
            'debug' => array(
                'middleware' => '\common\middleware\debug',
                'page' => array(
                    'setting' => true,
                ),
                'file' => array(
                    'setting' => true,
                    'path' => dirname(dirname(__FILE__)) . '/logs/' . date('Y-m-d') . '/debug.log',                    
                ),
            ),

            // csrf 过滤
            'csrf' => array(
                'middleware' => '\common\middleware\csrf',
            ),

        ],
    ),

    // handder 报错
    'handders' => array(
        // slim 内部调用 error
        'errorHandler' => '\common\error\errorHandler',
        // php error 只兼容 php7+
        'phpErrorHandler' => '\common\error\phpErrorHandler',
        // 404 error
        'notFoundHandler' => '\common\error\notFoundHandler',
        // 405 error
        'notAllowedHandler' => '\common\error\notAllowedHandler',
    ),

    // 日志
    'logger' => array(
        'name' => 'rout-logger',
        'path' => dirname(dirname(__FILE__)) . '/logs/' . date('Y-m-d') . '/app.log',
    ),

    // 路由设置
    'routes' => require(dirname(__FILE__).'/routes.php'),

  ),

);

