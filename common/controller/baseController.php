<?php

namespace common\controller;

use Psr\Container\ContainerInterface;

class baseController{

    public function __construct(){
        $this->init();
    }

    // 全局
    public function init(){
    }

}

