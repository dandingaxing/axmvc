<?php

namespace common\error;

class notFoundHandler{

    public function __invoke($request, $response) {
        // 获取当前请求url
        // 根据当前请求的url 可以分发到不同的报错方法 还可以结合 配置文件 debug 情况来自定义
        return $response
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('Page not found -- by notFoundHandler');
    }



}


