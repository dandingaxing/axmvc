<?php

return array(
  'main' => array(
    'host' => 'localhost',
    'port' => 80,
    'timezone' => 'Asia/Shanghai',

    // 模块路径
    'modulesPath' => dirname( dirname(__FILE__) ) . '/modules',


  ),


  'http' => array(
    'cookie' => array(
    ),
    'session' => array(
    ),
    'csrf' => array(
      // 'type' => '@http/session',
      '_type' => '@cache/redis',
      '_prefix' => '_csrf',
      '_tokenNameKey' => '_csrfkey',
      '_tokenValueKey' => '_csrf',
      'middleware' => '\common\middleware\csrf',
    ),
  ),

  // 文件操作与管理 -- 上传配置 - 支持 本地上传，阿里云，又拍云
  'file' => array(
    // 本地文件操作与管理
    'native' => array(
      'hostName' => 'http://img.uploadhost.com/uploads',
      'path' => dirname( dirname(__FILE__) ) . '/web/uploads/',
      'maxsize' => 20000,
      'usedtype' => array('jpg', 'jpeg', 'png', 'gif', 'zip', 'gz'),
    ),


    'aliyun' => array(
      'hostName' => 'http://img.aliyun.uploadhost.com',
      'accessKeyId' => '',
      'accessKeySecret' => '',
      'endpoint' => '',
      'bucket' => '',
      'isCName' => false,
      'securityToken' => NULL,
    ),

    // 又拍云文件操作与管理
    'upyun' => array(
      'hostName' => 'http://img.upaiyun.com.host.com',    // youlipinpic.b0.upaiyun.com
      'serviceName' => 'bucketname',
      'operatorName' => 'upyunusername',
      'operatorPwd' => 'upyunpasswd',
    ),

  ),
  

);

