<?php

namespace common\lib\http;

use common\base;

// csrf
class Csrf{

    /**
     * The name of the HTTP header for sending CSRF token.
     */
    const CSRF_HEADER = 'X-CSRF-Token';
    /**
     * The length of the CSRF token mask.
     */
    const CSRF_MASK_LENGTH = 32;

    // 最后返回字符串长度
    private $_length = 44;

    // 存储方式
    private $_type;
    // 统一前缀
    private $_prefix;
    private $_tokenNameKey;
    private $_tokenValueKey;


    public function set($name, $value){
        return $this->$name = $value;
    }

    // 获取token数组
    public function getToken(){
        // Generate new CSRF token
        $tokenName = uniqid($this->_prefix);
        if (function_exists('random_bytes')) {
            $e = bin2hex(random_bytes(self::CSRF_MASK_LENGTH));
        }
        if (function_exists('mcrypt_create_iv')) {
            $e = bin2hex(mcrypt_create_iv(self::CSRF_MASK_LENGTH, MCRYPT_DEV_URANDOM));
        } 
        if (function_exists('openssl_random_pseudo_bytes')) {
            $e = bin2hex(openssl_random_pseudo_bytes(self::CSRF_MASK_LENGTH));
        }
        $tokenValue = substr(strtr(base64_encode(hex2bin($e)), '+', '.'), 0, $this->_length);

        $token = [
            $this->_tokenNameKey => $tokenName,
            $this->_tokenValueKey => $tokenValue,
        ];

        return $token;
    }

    /**
     * Validate CSRF token from current request
     * against token value stored in $_SESSION
     *
     * @param  string $name  CSRF name
     * @param  string $value CSRF token value
     *
     * @return bool
     */
    // 验证token
    // public function validateToken($name, $value){
    public function validateToken($name, $value){
        // 获取 session 内的值
        $token = isset($_SESSION[$name]) ? $_SESSION[$name] : false;
        // 比对
        if (function_exists('hash_equals')) {
            $result = ($token !== false && hash_equals($token, $value));
        } else {
            $result = ($token !== false && $token === $value);
        }
        $this->destroyToken($name);
        return $result;
    }

    public function destroyToken($name){
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }
    }

    // 保存 token 到session 或者 redis 等
    public function saveToken($token){
        $_SESSION[$token[$this->_tokenNameKey]] = $token[$this->_tokenValueKey];
    }

    // 表单 设置csrf 默认保存到
    public function csrf($value=false){
        $token = $this->getToken();
        // var_dump($token);
        $this->saveToken($token);
        // 设置 header 头
        if ($value) {
            return $token[$this->_tokenValueKey];
        }else{
            return '<input type="hidden" name="'.$this->_tokenNameKey.'" value="'.$token[$this->_tokenNameKey].'"> '.' <input type="hidden" name="'.$this->_tokenValueKey.'" value="'.$token[$this->_tokenValueKey].'">';
        }
    }

    // ajax csrf获取
    public function ajaxCsrf(){
        $token = $this->getToken();
        $this->saveToken($token);
        return $token;
    }










    /**
     * Retrieve token name key
     *
     * @return string
     */
    public function getTokenNameKey()
    {
        return $this->_tokenNameKey;
    }

    /**
     * Retrieve token value key
     *
     * @return string
     */
    public function getTokenValueKey()
    {
        return $this->_tokenValueKey;
    }

















}

