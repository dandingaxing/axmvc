<?php

namespace common\error;

class phpErrorHandler{

    public function __invoke($request, $response, $error) {
        echo "<pre>";
        print_r($error->getMessage());
        echo "<br>";
        print_r($error->getPrevious());
        echo "<br>";
        print_r($error->getCode());
        echo "<br>";
        print_r($error->getFile());
        echo "<br>";
        print_r($error->getLine());
        echo "<br>";
        // print_r($error->getTrace());
        print_r($error->getTraceAsString());


        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write('php error hander');
    }



}


