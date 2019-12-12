<?php

namespace common\middleware;
use common\base;

// use Medoo\Medoo;

class csrf{

    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next){
        $body = $request->getParsedBody();
        $response = $next($request, $response);
        // Validate POST, PUT, DELETE, PATCH requests
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $tokenNameKey = base::conf('http.csrf._tokenNameKey');
            $tokenValueKey = base::conf('http.csrf._tokenValueKey');
            $body = $body ? (array)$body : [];
            $name = isset($body[$tokenNameKey]) ? $body[$tokenNameKey] : false;
            $value = isset($body[$tokenValueKey]) ? $body[$tokenValueKey] : false;
            if (!empty($name) && !empty($value) && !(base::app('http/csrf')->validateToken($name, $value))) {
                // 强制清空错误数据
                base::app('http/csrf')->destroyToken($name);
                return $this->failureCallable($response);
            }
        }
        return $response;
    }

    public function failureCallable($response){
        $errorbody = new \Slim\Http\Body(fopen('php://temp', 'r+'));
        $errorbody->write('Failed CSRF check!');
        return $response->withStatus(400)->withHeader('Content-type', 'text/plain')->withBody($errorbody);
    }



}


