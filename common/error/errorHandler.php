<?php

namespace common\error;

class errorHandler{

    public function __invoke($request, $response, $exception) {
        echo "<pre>";
        print_r($exception);
        // 获取当前请求url
        // 根据当前请求的url 可以分发到不同的报错方法 还可以结合 配置文件 debug 情况来自定义
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write('Something went wrong!--by errorHandler');
    }


}


