<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);


$autoload = require(__DIR__ . '/../vendor/autoload.php');
$autoload->addPsr4("common\\", dirname(dirname(__FILE__))."/common");

// 获取所有Config
use Noodlehaus\Config;
$conf = new Config( dirname(dirname(__FILE__)) . '/config');

// 实例化
(new common\base())->run($conf);

