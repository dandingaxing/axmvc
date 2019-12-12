<?php

namespace modules\home\controller;


class errorHandler {


   public function __invoke($request, $response, $exception) {
        echo "<pre>";
        echo "__invoke";
        print_r($this->d());
        print_r($exception->getMessage());
        echo "<pre>";

        // 获取当前请求url
        print_r($request->getUri());
        print_r($request->getUri()->getPath());
        // 根据当前请求的url 可以分发到不同的报错方法 还可以结合 配置文件 debug 情况来自定义
        if ($path 正则 /[]/) {
            $this->
        }

        print_r($request->getServerParams());
        print_r($request->getQueryParams());
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write('Something went wrong!');
   }

   public function d(){
    echo "sdfsdfsafas";
    echo "ddddddddddddddd";
   }


   public function test($request, $response, $exception) {
    print_r($this);
    // print_r($request);
    print_r($exception->getMessage());
    exit();
        // echo "__invoke";
        // print_r($exception->getMessage());
        // return $response
        //     ->withStatus(500)
        //     ->withHeader('Content-Type', 'text/html')
        //     ->write('Something went wrong!');
   }




}

