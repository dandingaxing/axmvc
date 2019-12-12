<?php

namespace modules\home\controller;

class MyAction {
   protected $ci;
   //Constructor
   public function __construct(ContainerInterface $ci) {
       $this->ci = $ci;
   }
   
   public function __invoke($request, $response, $args) {
        //your code
        //to access items in the container... $this->ci->get('');
   }
}