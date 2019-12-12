<?php

namespace common;


// 定义路由 与路由日志 与路由报错
class base{

    // 当前配置文件
    private static $_config;

    // 组件
    private static $_app;

    // 请求与响应信息
    private static $_request;
    private static $_response;
    private static $_args;

    // 获取主配置
    public static function conf($name){
        return self::$_config->get($name);
    }

    // 获取组件
    public static function app($name){
        $nameArr = explode('/', str_replace(array('.', '/', '\\'), '/', $name) );
        // 不知为何 使用绝对路径\common\lib\db 作为 $lib 可以。当 $lib = 'lib\db' , 文件头加上 use common\lib 却不可以用
        $lib = sprintf('\common\lib\%s', $nameArr[0]);
        return $lib::app($name);
    }

    public static function getRequest(){
        return self::$_request;
    }

    public static function getResponse(){
        return self::$_response;
    }

    public static function getArgs(){
        return self::$_args;
    }

    // 启动
    public function run($conf){
        session_start();
        $config = $conf->all();
        self::$_config = $conf;
        // 路由
        $config['router'] && $this->newRouter($name='router');
    }

    // 实例化路由
    public function newRouter($name='router'){
        $c = new \Slim\Container(self::conf('router.config'));

        // 固定为 实例化 类的方式使用 handler
        // errorHandler
        if (self::conf('router.handders.errorHandler')) {
            $c['errorHandler'] = function($c){
                $errorHandler = self::conf('router.handders.errorHandler');
                return new $errorHandler;
            };
        }

        if (self::conf('router.handders.phpErrorHandler')) {
            $c['phpErrorHandler'] = function($c){
                $phpErrorHandler = self::conf('router.handders.phpErrorHandler');
                return new $phpErrorHandler;
            };
        }

        if (self::conf('router.handders.notFoundHandler')) {
            $c['notFoundHandler'] = function($c){
                $notFoundHandler = self::conf('router.handders.notFoundHandler');
                return new $notFoundHandler;
            };
        }

        if (self::conf('router.handders.notAllowedHandler')) {
            $c['notAllowedHandler'] = function($c){
                $notAllowedHandler = self::conf('router.handders.notAllowedHandler');
                return new $notAllowedHandler;
            };
        }

        // 实例化日志
        $logConf = self::conf('router.logger');
        if ($logConf) {
            $c['logger'] = function($c){
                $loggerName = self::conf('router.logger.name');
                $loggerFile = self::conf('router.logger.path');

                $logger = new \Monolog\Logger( $loggerName );
                $file_handler = new \Monolog\Handler\StreamHandler( $loggerFile );
                $logger->pushHandler($file_handler);
                return $logger;
            };
        }

        self::$_app[$name] = new \Slim\App($c);
        
        // 使用中间件做页面 debug 在 after 后，即执行的最后添加debug信息到页面，注：与原 slim 框架的debug 的区别和使用策略
        if ( self::conf('router.config.settings.debug.page.setting') || self::conf('router.config.settings.file.setting') ) {
            $middleware = self::conf('router.config.settings.debug.middleware');
            self::$_app[$name]->add( new $middleware );
        }

        if ( self::conf('http.csrf') ) {
            $middleware = self::conf('http.csrf.middleware');
            self::$_app[$name]->add( new $middleware );
        }

        $routesConf = self::conf('router.routes');
        foreach ($routesConf as $key => $route) {
            $this->setRoute($name, $key, $route);
        }
        self::$_app[$name]->run();

    }



    public function setRoute($name, $routeKey, $route){
        self::$_app[$name]->map($route['type'], $route['pattern'], function($request, $response, $args) use ($route) {

            self::$_request = $request;
            self::$_response = $response;
            self::$_args = $args;

            // route 日志记录
            if (isset($this->logger)) {
                $this->logger->addInfo("request", array(
                    'type' => $request->getMethod(),
                    'route' => $route,
                    //     'uri' => $request->getUri(),
                    'server' => $request->getServerParams(),
                    'cookie' => $request->getCookieParams(),
                    'args'=>$args,
                    )
                );
            }
            


            // 统一调用 mvc 
            $callback = explode(':', $route['callback']);
            // 类名
            $controller = $callback[0];
            // 方法名
            $action = $callback[1];

            // 返回
            return (new $controller)->$action($request, $response, $args);

        })->setName($routeKey);
    }







}



