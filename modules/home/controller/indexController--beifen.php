<?php

namespace modules\home\controller;
use common\controller\Controller;
use Psr\Container\ContainerInterface;

class indexController extends Controller {

    protected $ci;
    //Constructor
    public function __construct(ContainerInterface $ci) {
       $this->ci = $ci;
    }

    public function index($request, $response, $args)
    {
        print_r($args);
        echo "<pre>";
        print_r($this);
        // print_r($this->ci->get('request')->getAttribute('route'));
        // print_r($this->ci->get('request'));
        // $route = $request->getAttribute('route');
        // var_dump($route);
        exit();
        // $route = $request->getAttribute('router');
        $courseId = $route->getArgument('name');
        $courseId = $route->getArguments();
        var_dump($courseId);
        var_dump($route);
        exit();
        print_r($args);
        // print_r($this->ci);
        // $route = $request->getAttribute('route');
        // var_dump($this->ci->get('request'));
        // print_r($this->ci->get('response'));
        print_r($this->ci->get('router'));
        echo "home/ indexController / someMethod";
    }

    public function homec(){
        echo "<pre>";
        print_r($this);
        // print_r( $this->ci->get('request')->getQueryParams() );
        // var_dump( $this->ci->get('request')->getRequestBody() );
    }


}
