<?php


namespace common\error;

class notAllowedHandler{

    public function __invoke($request, $response, $methods) {
        return $response
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-type', 'text/html')
            ->write('Method must be one of: ' . implode(', ', $methods) . '-- by notAllowedHandler');
    }



}






