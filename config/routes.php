<?php

// 路由文件
return array(

    // 模块统一路由管理
    'setName' => array(
        'type'=>['GET'],
        'pattern' => '/',
        'callback' => '\modules\home\controller\indexController:index',
    ),

    'admin' => array(
        'type' => ['GET', 'POST', 'PUT', 'DELETE'],
        'pattern' => '/admin.do[/r={r:[a-zA-Z0-9\-._/?]+}[&{params:[a-zA-Z0-9\-._%&=/?]*}]]',
        'callback' => '\common\controller\mvcbaseController:admin',
    ),



);

