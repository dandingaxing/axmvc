<?php

namespace modules\home\controller;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Container\ContainerInterface;

class homeController{

    protected $ci;
    //Constructor
    public function __construct(ContainerInterface $ci) {
       $this->ci = $ci;
    }

    // public function index($request, $response, $args)
    // {
    //     print_r($args);
    //     echo "home/ indexController / someMethod";
    // }

    public function home(){
      echo "heheheh";
      echo "home index";

      // $loggerSettings = $this->get('settings')['logger'];

      $this->render();

    }


    public function errorhandler($request, $response, $exception) {
        echo "<br>";
        echo "errorHandler";
        echo "<br>";
        print_r($request);
        // print_r($response);
        exit();
        // print_r($exception->getMessage());
      //   return $response->withStatus(500)
      //                        ->withHeader('Content-Type', 'text/html')
      //                        ->write('Something went wrong!');
      // echo "errorhandler";
    }

    public function phperror(){
      echo "php error";
    }




}
