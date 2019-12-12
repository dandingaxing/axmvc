<?php

namespace common\lib\file;
use common\base;

use OSS\OssClient;
use OSS\Core\OssException;

class aliyun{

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

    // 初始化阿里云服务
    private $_client;

    // 待上传文件
    private $_file;


    // 设置配置文件
    public function setConfig($config){
        $this->_accessKeyId = $config['accessKeyId'];
        $this->_accessKeySecret = $config['accessKeySecret'];
        $this->_endpoint = $config['endpoint'];
        $this->_bucket = $config['bucket'];
        $this->_isCName = $config['isCName'];
        $this->_securityToken = $config['securityToken'];
        return $this;
    }

    // 设置 bucket
    public function setBucket($bucket){
        $this->_bucket = $bucket;
        return $this;
    }

    // 初始化
    public function setClient(){
        $this->_client = new OssClient($this->_accessKeyId, $this->_accessKeySecret, $this->_endpoint);
        return $this;
    }

    public function putObject($savePath, $content, $options = array()){
        return $this->_client->putObject($this->_bucket, $savePath, $content, $options);
    }

    public function uploadFile($savePath, $filePath, $options = array()){
        return $this->_client->uploadFile($this->_bucket, $savePath, $filePath, $options);
    }

    // 上传文件
    public function upfile($InstanceName, $dir='', $filename='', $options = array()){
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
        $_filePath = empty($dir) ? 'demo-upload/'.date('Y-m-d', time()).'/'.$thisFileName : trim($dir, '/') . '/' . $thisFileName;  // 文件与目录

        return $this->uploadFile( $_filePath, $this->_file['tmp_name'], $options=array() );
    }


    // 删除文件
    public function delete($filePath){
        return $this->_client->deleteObject($this->_bucket, $filePath);
    }

    // 判断文件是否存在
    public function has($filePath){
        return $this->_client->doesObjectExist($this->_bucket, $$filePath);
    }



    // 列出目录下 文件 和目录 ，maxkeys 为最大数量
    public function fileList($filePath, $maxkeys = 10000){
        $prefix = $filePath;
        $delimiter = '/';
        $nextMarker = '';
        $maxkeys = 1000;
        $options = array(
            'delimiter' => $delimiter,
            'prefix' => $prefix,
            'max-keys' => $maxkeys,
            'marker' => $nextMarker,
        );
        $listObjectInfo = $this->_client->listObjects($this->_bucket, $options);
        $objectList = $listObjectInfo->getObjectList(); // 文件列表
        $prefixList = $listObjectInfo->getPrefixList(); // 目录列表
        $fileList = array();
        foreach ($objectList as $objectInfo) {
            $fileList[] = array(
                'type' => 'file',
                'file' => $objectInfo->getKey(),
            );
        }
        foreach ($prefixList as $prefixInfo) {
            $fileList[] = array(
                'type' => 'file',
                'file' => $prefixInfo->getPrefix(),
            );
        }
        return $fileList;
    }

    // 列出目录下所有 文件/目录 不限最大数量
    public function fileListAll($filePath){
        $prefix = $filePath;
        $delimiter = '/';
        $nextMarker = '';
        $maxkeys = 1000;
        $fileList = array();
        while (true) {
            $options = array(
                'delimiter' => $delimiter,
                'prefix' => $prefix,
                'max-keys' => $maxkeys,
                'marker' => $nextMarker,
            );
            $listObjectInfo = $this->_client->listObjects($this->_bucket, $options);
            $nextMarker = $listObjectInfo->getNextMarker();
            $listObject = $listObjectInfo->getObjectList();
            $listPrefix = $listObjectInfo->getPrefixList();
            foreach ($objectList as $objectInfo) {
                $fileList[] = array(
                    'type' => 'file',
                    'file' => $objectInfo->getKey(),
                );
            }
            foreach ($prefixList as $prefixInfo) {
                $fileList[] = array(
                    'type' => 'file',
                    'file' => $prefixInfo->getPrefix(),
                );
            }
            if ($nextMarker === '') {
                break;
            }
        }
        return $fileList;
    }









}





