<?php

namespace common\lib\file;
use common\base;

// 和类 upyun 重名了 所以不能直接引用 use Upyun\Upyun;
// use Upyun\Upyun;
// use Upyun\Config;


// 又拍云文件管理
class upyun{

    // 对外输出唯一实例
    public static $_instance;

    // 配置文件
    private $_serviceName;
    private $_operatorName;
    private $_operatorPwd;

    // 允许最大长度
    private $_allowedMaxSize;
    // 允许的后缀
    private $_allowedSuffix;

    // 初始化又拍云服务
    private $_client;

    // 待上传文件
    private $_file;


    // 设置配置文件
    public function setConfig($config){
        $this->_serviceName = $config['serviceName'];
        $this->_operatorName = $config['operatorName'];
        $this->_operatorPwd = $config['operatorPwd'];
        return $this;
    }

    // 初始化
    public function setClient(){
        $serviceConfig = new \Upyun\Config($this->_serviceName, $this->_operatorName, $this->_operatorPwd);
        $client = new \Upyun\Upyun($serviceConfig);
        $this->_client = $client;
        return $this;
    }

    // 字符串写入文件 fopen的文件流在这里就会自动被关闭掉，所以不用再去写fclose 了
    public function write($savePath, $content, $params = array(), $withAsyncProcess = false){
        return $this->_client->write($savePath, $content, $params, $withAsyncProcess);
    }

    // 文件上传至又拍云 文件流写入又拍云服务器
    public function upfile($InstanceName, $dir='', $filename='', $params = array(), $withAsyncProcess = false){
        if (!isset($_FILES[$InstanceName]) || empty($_FILES[$InstanceName]['tmp_name']) || empty($_FILES[$InstanceName]['name']) ) {
            return false;
        }
        $this->_file = $_FILES[$InstanceName];

        // 判断大小是否可以
        if ( !empty($this->_allowedMaxSize) && $this->_file['size']>$this->_allowedMaxSize ) {
            echo "too big size";
            return false;
        }

        // 获取后缀并判断是否可以
        $extension = $this->getExtension();
        if ( !empty($this->_allowedSuffix) && !empty($extension) && !in_array( $extension, $this->_allowedSuffix ) ) {
            echo "not allowed suffix";
            return false;
        }

        // 判断后缀是否
        $thisFileName = empty($filename) ? md5(time().$this->_file['name'].rand(1000, 9000)).'.'.$extension : $filename;
        $_filePath = empty($dir) ? 'uploads/demo-thumbs/'.date('Y-m-d', time()).'/'.$thisFileName : trim($dir, '/') . '/' . $thisFileName;  // 文件与目录

        $write = $this->_client->write($_filePath, fopen($this->_file['tmp_name'], 'r'), $params, $withAsyncProcess);
        if (!is_null($write)) {
            return $_filePath;
        }else{
            return false;
        }
    }



    // 获取图片文件名称和后缀
    public function imgFileSuffix(){
        $array  = array();
        $array['houzhui'] = substr(strrchr($this->_file['name'], '.'), 1);
        $array['wenjian'] = substr($this->_file['name'], 0, strrpos($this->_file['name'], '.'));
        return $array;
    }

    // 获取后缀，并且默认返回小写字母
    public function getExtension($tolower = true){
        return $tolower ? strtolower($this->imgFileSuffix()['houzhui']) : $this->imgFileSuffix()['houzhui'];
    }

    // 设置可用后缀
    public function setAllowdSuffix($suffix){
        $this->_allowedSuffix = $suffix;
        return $this;
    }

    // 设置单文件最大上传
    public function setAllowedMaxSize($size){
        $this->_allowedMaxSize = $size;
        return $this;
    }






    

}

