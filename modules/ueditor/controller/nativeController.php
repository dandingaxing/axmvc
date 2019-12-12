<?php

namespace modules\ueditor\controller;

use common\base;
use modules\admin\common\Controller;

class nativeController extends Controller{

    private static $_ueditorConfig;

    // 统一对外方法
    public function router(){
        $getArr = $this->getParams();
        // 先检测配置文件并初始化配置文件
        if (!self::$_ueditorConfig) {
            $this->config();
        }
        
        if ($getArr['action']==='config') {
            // 列出配置文件
            $result = $this->config();
        }elseif ($getArr['action']==='uploadimage') {
            // 上传图片
            $result = $this->uploadimage();;
        }elseif ($getArr['action']==='uploadscrawl') {
            // 上传涂鸦
            $result = $this->uploadscrawl();
        }elseif ($getArr['action']==='uploadvideo') {
            // 上传视频
            $result = $this->uploadvideo();
        }elseif ($getArr['action']==='uploadfile') {
            // 上传文件
            $result = $this->uploadfile();
        }elseif ($getArr['action']==='listimage') {
            // 列出图片
            $result = $this->listimage();
        }elseif ($getArr['action']==='listfile') {
            // 列出文件
            $result = $this->listfile();
        }elseif ($getArr['action']==='catchimage') {
            // 抓取远程文件
            $result = $this->catchimage();
        }else{
            throw new Exception("request address error", 1);
        }
        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state'=> 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }

    }

    // 配置信息
    protected function config(){
        if (!self::$_ueditorConfig) {
            $configFile = dirname( dirname(__FILE__) ) . '/config/native.config.json';
            if( file_exists($configFile) ){
                $config = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($configFile)), true);
                if (empty($config)) {
                    throw new Exception("ueditor Config is empty", 1);
                }else{
                    self::$_ueditorConfig = $config;
                }
            }else{
                throw new Exception("ueditor Config is NOT Have", 1);
            }
        }
        return json_encode( self::$_ueditorConfig );
    }

    // 文件上传统一返回
    /**
     * [upfilereturn description]
     * array(
     *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
     *     "url" => "",            //返回的地址
     *     "title" => "",          //新文件名
     *     "original" => "",       //原始文件名
     *     "type" => ""            //文件类型
     *     "size" => "",           //文件大小
     * )
     * @param  string $state    [description]
     * @param  string $url      [description]
     * @param  string $title    [description]
     * @param  string $original [description]
     * @param  string $type     [description]
     * @param  string $size     [description]
     * @return [type]           [description]
     */
    private function upfilereturn($state='', $url='', $title='', $original='', $type='', $size=''){
        return array(
                'state' => $state,
                'url' => $url,
                'title' => $title,
                'original' => $original,
                'type' => $type,
                'size' => $size,
            );
    }


    /**
     * 本地上传图片
     * 得到上传文件所对应的各个参数,数组结构
     */
    protected function uploadimage(){
        $config = array(
            "pathFormat" => self::$_ueditorConfig['imagePathFormat'],
            "maxSize" => self::$_ueditorConfig['imageMaxSize'],
            "allowFiles" => self::$_ueditorConfig['imageAllowFiles']
        );
        $fieldName = self::$_ueditorConfig['imageFieldName'];

        // 文件上传
        $upload = base::app('file/native')->setAllowedMaxSize($config['maxSize'])->setAllowdSuffix($config['allowFiles'])->upfile($fieldName);

        // 构建返回值
        $returnArr = array();
        if ($upload) {
            $returnArr = $this->upfilereturn($state='SUCCESS', $url=$upload['url'], $title=$upload['title'], $original=$upload['original'], $type=$upload['type'], $size=$upload['size']);
        }else{
            $returnArr = $this->upfilereturn($state='ERROR', $url='', $title='', $original='', $type='', $size='');
        }

        return json_encode($returnArr);
    }

    // 上传涂鸦
    protected function uploadscrawl(){
        $config = array(
            "pathFormat" => self::$_ueditorConfig['scrawlPathFormat'],
            "maxSize" => self::$_ueditorConfig['scrawlMaxSize'],
            "allowFiles" => self::$_ueditorConfig['scrawlAllowFiles'],
            "oriName" => "scrawl.png"
        );
        $fieldName = self::$_ueditorConfig['scrawlFieldName'];

        $base64Data = $_POST[$fileName];
        $img = base64_decode($base64Data);
        // 判断img是否为空
        
        $uploadPath = base::app('file/native')->setAllowedMaxSize($config['maxSize'])->setAllowdSuffix($config['allowFiles'])->putObject($img, $path);

        // 构建返回值
        $returnArr = array();
        if ($uploadPath) {
            $returnArr = $this->upfilereturn($state='SUCCESS', $url='', $title='', $original='', $type='', $size='');
        }else{
            $returnArr = $this->upfilereturn($state='ERROR', $url='', $title='', $original='', $type='', $size='');
        }
        return json_encode($returnArr);
    }


    // 上传视频
    protected function uploadvideo(){
        $config = array(
            "pathFormat" => self::$_ueditorConfig['videoPathFormat'],
            "maxSize" => self::$_ueditorConfig['videoMaxSize'],
            "allowFiles" => self::$_ueditorConfig['videoAllowFiles']
        );
        $fieldName = self::$_ueditorConfig['videoFieldName'];

        // 文件上传
        $upload = base::app('file/native')->setAllowedMaxSize($config['maxSize'])->setAllowdSuffix($config['allowFiles'])->upfile($fieldName);

        // 构建返回值
        $returnArr = array();
        if ($upload) {
            $returnArr = $this->upfilereturn($state='SUCCESS', $url=$upload['url'], $title=$upload['title'], $original=$upload['original'], $type=$upload['type'], $size=$upload['size']);
        }else{
            $returnArr = $this->upfilereturn($state='ERROR', $url='', $title='', $original='', $type='', $size='');
        }
        return json_encode($returnArr);

    }

    // 上传文件
    protected function uploadfile(){
        $config = array(
            "pathFormat" => self::$_ueditorConfig['filePathFormat'],
            "maxSize" => self::$_ueditorConfig['fileMaxSize'],
            "allowFiles" => self::$_ueditorConfig['fileAllowFiles']
        );
        $fieldName = self::$_ueditorConfig['fileFieldName'];

        // 文件上传
        $upload = base::app('file/native')->setAllowedMaxSize($config['maxSize'])->setAllowdSuffix($config['allowFiles'])->upfile($fieldName);

        // 构建返回值
        $returnArr = array();
        if ($upload) {
            $returnArr = $this->upfilereturn($state='SUCCESS', $url=$upload['url'], $title=$upload['title'], $original=$upload['original'], $type=$upload['type'], $size=$upload['size']);
        }else{
            $returnArr = $this->upfilereturn($state='ERROR', $url='', $title='', $original='', $type='', $size='');
        }
        return json_encode($returnArr);

    }

    // 抓取远程文件
    protected function catchimage(){
        $imgUrl = htmlspecialchars($this->fileField);
        $imgUrl = str_replace("&amp;", "&", $imgUrl);

        //http开头验证
        if (strpos($imgUrl, "http") !== 0) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_LINK");
            return;
        }

        preg_match('/(^https*:\/\/[^:\/]+)/', $imgUrl, $matches);
        $host_with_protocol = count($matches) > 1 ? $matches[1] : '';

        // 判断是否是合法 url
        if (!filter_var($host_with_protocol, FILTER_VALIDATE_URL)) {
            $this->stateInfo = $this->getStateInfo("INVALID_URL");
            return;
        }

        preg_match('/^https*:\/\/(.+)/', $host_with_protocol, $matches);
        $host_without_protocol = count($matches) > 1 ? $matches[1] : '';

        // 此时提取出来的可能是 ip 也有可能是域名，先获取 ip
        $ip = gethostbyname($host_without_protocol);
        // 判断是否是私有 ip
        if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
            $this->stateInfo = $this->getStateInfo("INVALID_IP");
            return;
        }

        //获取请求头并检测死链
        $heads = get_headers($imgUrl, 1);
        if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
            $this->stateInfo = $this->getStateInfo("ERROR_DEAD_LINK");
            return;
        }
        //格式验证(扩展名验证和Content-Type验证)
        $fileType = strtolower(strrchr($imgUrl, '.'));
        if (!in_array($fileType, $this->config['allowFiles']) || !isset($heads['Content-Type']) || !stristr($heads['Content-Type'], "image")) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_CONTENTTYPE");
            return;
        }

        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(
            array('http' => array(
                'follow_location' => false // don't follow redirects
            ))
        );
        readfile($imgUrl, false, $context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);

        $this->oriName = $m ? $m[1]:"";
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->filePath = $this->getFilePath();
        $this->fileName = $this->getFileName();
        $dirname = dirname($this->filePath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }

        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            $this->stateInfo = $this->getStateInfo("ERROR_CREATE_DIR");
            return;
        } else if (!is_writeable($dirname)) {
            $this->stateInfo = $this->getStateInfo("ERROR_DIR_NOT_WRITEABLE");
            return;
        }

        //移动文件
        if (!(file_put_contents($this->filePath, $img) && file_exists($this->filePath))) { //移动失败
            $this->stateInfo = $this->getStateInfo("ERROR_WRITE_CONTENT");
        } else { //移动成功
            $this->stateInfo = $this->stateMap[0];
        }
    }

    // 列出图片
    protected function listimage(){

    }

    // 列出文件
    protected function listfile(){

    }


























}





