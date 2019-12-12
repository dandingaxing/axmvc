<?php

namespace common\lib\file;
use common\base;

class native{

    // host配置
    private $_hostName;

    // 基础总目录
    private $_basePath;

    // 允许最大长度
    private $_allowedMaxSize;
    // 允许的后缀
    private $_allowedSuffix;
    // 允许的host 域名或 ip
    private $_allowedHost;
    // 不允许的host 域名或 ip
    private $_notallowedHost;

    // 上传文件信息
    private $_file;

    // 上传后返回信息
    private $_info;

    // 设置基本配置
    public function setConfig($config){
        $this->_basePath = $config['path'];
        $this->_hostName = $config['hostName'];
        return $this;
    }

    // 设置文件基础路径
    public function setBasePath($path){
        $this->_basePath = $path;
        return $this;
    }

    // 获取上传文件信息
    public function getupFileInfo(){
        return $this->_file;
    }

    // 文件上传
    public function upfile($InstanceName, $dir='', $filename=''){
        if (!isset($_FILES[$InstanceName]) || empty($_FILES[$InstanceName]['tmp_name']) || empty($_FILES[$InstanceName]['name']) || !file_exists($_FILES[$InstanceName]['tmp_name']) || !is_uploaded_file($_FILES[$InstanceName]['tmp_name']) ) {
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
        $_filePath = empty($dir) ? 'uploads/thumbs/'.date('Y-m-d', time()).'/'.$thisFileName : trim($dir, '/') . '/' . $thisFileName;  // 文件与目录
        $filePath = $this->_basePath . '/' . $_filePath ;  // 要上传的绝对路径

        // 创建目录
        if ( !is_dir(dirname($filePath)) && !$this->mkdirss(dirname($filePath)) ) {
            echo "file path error";
            return false;
        }
        // 上传文件到本地
        if (move_uploaded_file($this->_file["tmp_name"], $filePath)) {
            return $this->getFileInfo($_filePath);
        }else{
            return false;
        }
    }

    // 将字符串写入文件
    public function putObject($content, $conPath){
        $this->_file['size'] = strlen($content);
        $this->_file['name'] = $conPath;


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
        $filePath = $this->_basePath . '/' . $conPath;

        // 创建目录
        if ( !is_dir(dirname($filePath)) && !$this->mkdirss(dirname($filePath)) ) {
            echo "file path error";
            return false;
        }

        // 写入文件
        if (!(file_put_contents($filePath, $content))) { // 写入失败
            echo "file put error";
            return false;
        } else { // 写入成功
            return $conPath;
        }

    }



    // 查看 文件/目录 是否存在
    public function has($file){
        return file_exists($file);
    }

    // 查看文件信息
    public function getMimetype($file){
        if ($this->has($file)) {
            return mime_content_type($file);
        }else{
            return false;
        }
    }

    // 查看文件基本信息 需要 安装 exif 扩展 详见：http://php.net/manual/zh/exif.setup.php
    public function info($file){
        return exif_read_data($file);
    }


    // 递归创建目录
    public function mkdirss($dirs, $mode = 0777) {
        if (!is_dir($dirs)) {
            $this->mkdirss(dirname($dirs), $mode);
            return @mkdir($dirs, $mode);
        }
        return true;
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
        $suffixArr = array();
        if (is_string($suffix)) {
            $suffix = explode(',', $suffix);
        }
        foreach ($suffix as $k => $v) {
            $suffixArr[] = ltrim(trim($v), '.');
        }
        $this->_allowedSuffix = $suffixArr;
        return $this;
    }

    // 设置单文件最大上传
    public function setAllowedMaxSize($size){
        $this->_allowedMaxSize = $size;
        return $this;
    }
    
    // 可用 host 与IP
    public function setAllowedHost($host){
        $this->_allowedHost = $host;
        return $this;
    }

    // 不可用的host与IP
    public function setNotAllowedHost($host){
        $this->_notallowedHost = $host;
        return $this;
    }

    // 获取文件列表
    public function fileList($path, $allPath = false){
        if (empty($allPath)) {
            $thisPath = $this->_basePath . '/' . trim($path, '/') . '/';
        }else{
            $thisPath = rtrim($path, '/') . '/';
        }
        if (!is_dir($thisPath)) {
            return false;
        }
        $fileList = array();
        $d = dir($thisPath);
        while(($fileName = $d->read()) !== false)
        {
            if ($fileName==='.' || $fileName==='..') {
                continue;
            }
            $data['type'] = is_file($thisPath . $fileName) ? 'file' : 'dir';
            $data['file'] = $path . $fileName;
            $fileList[] = $data;
        }
        $d->close();
        return $fileList;
    }

    // 删除文件
    public function delete($filePath, $allPath=false){
        return $allPath ? unlink($allPath) : unlink($this->_basePath . '/' . ltrim($filePath, '/'));
    }

    /**
     * 获取当前上传成功文件的各项信息
     * @return array
     */
    public function getFileInfo($filePath){
        return array(
            "state" => 'SUCCESS',
            "url" => rtrim($this->_hostName, '/') . '/' . ltrim($filePath, '/'),
            "title" => basename($filePath),
            "original" => $this->_file['name'],
            "type" => $this->getExtension(),
            "size" => $this->_file['size'],
        );
    }



}




