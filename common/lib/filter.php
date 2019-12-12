<?php
namespace common\lib;




/**
 *       $data = array(
 *           'name' => 'username46555',
 *           'pwd' => '123465手机1231@qq.com',
 *           'email' => 'email@qq.com',
 *           'tags' => 'a',
 *       );
 *       $vald = array('name'=>'required');
 *       $vald = array(
 *           'name' => array(
 *               'required',
 *               'maxlen,200',
 *               'minlen,10',
 *           ),
 *           'pwd{#1}' => array('required'),
 *           'pwd{#2}' => array(
 *               '__LOGIC' => 'OR',
 *               'AND' => array(
 *                   'AND{#1}' => array(
 *                       'minlen,10',
 *                       'maxlen,100',
 *                       'numeric',
 *                       '/^[0-9]+$/',
 *                       'email',
 *                       ),
 *                   'AND{#2}' => array(
 *                       'minlen,10',
 *                       'maxlen,100',
 *                       ),
 *                   ),
 *               'AND{#1}' => array(
 *                   'required',
 *                   ),
 *               ),
 *           'email' => 'email',
 *           'tags' => 'contains,a b 0',
 *       );
 *       $errorMessage = array(
 *           'name' => '用户名不可为空',
 *           'pwd{#1}' => '密码不可为空',
 *           'pwd{#2}' => '10-100为字符，包含大小写包含特殊字符或者数字',
 *           'email' => 'email格式错误',
 *           'tags' => 'have not',
 *           );
 *
 ** 
 */
class filter{

    // 验证函数前缀
    const PREPIX = 'validate_';

    // 每一整组的结合
    const LOGIC = '__LOGIC';

    private $_success;
    private $_error;
    private $_erroField;

    // 
    public function is_valid(array $data, array $validators, array $errorMessage = array(), array $successMessage = array()){
        foreach ($validators as $k => $v) {
            // 判断 $k 是否包含{#.....}
            $dataName = strstr($k, "{#", true)===false ? $k : strstr($k, "{#", true);
            $error = isset($errorMessage[$k]) ? $errorMessage[$k] : "";
            $success = isset($successMessage[$k]) ? $successMessage[$k] : "";
            $validate = $this->validate($data[$dataName], $v, '', '');
            if ($validate===false) {
                $this->_error = $error;
                $this->_erroField = $dataName;
                return false;
            }
        }
        $this->_success = $success;
        return true;
    }

    // 每一组匹配，每一组最多为二维数组
    public function validate($data, $validators, $error='', $success=''){
        if (is_string($validators)) {
            return $this->strOneValidate($data, $validators, $error, $success);
        }elseif (is_array($validators)) {
            $result = array();
            $param = null;
            foreach ($validators as $key => $validator) {
                if (is_array($validator)) {
                    foreach ($validator as $k => $v) {
                        if (is_array($v)) {
                            foreach ($v as $fk => $fv) {
                                $result[$key][$k][$fk] = $this->strOneValidate($data, $fv, $error, $success);
                            }
                        }else{
                            $result[$key][$k] = $this->strOneValidate($data, $v, $error, $success);
                        }
                    }
                }else{
                    if (strtoupper($key)===self::LOGIC) {
                        $result[self::LOGIC] = (strtoupper(trim($validator))==='OR') ? 'OR' : 'AND';
                    }else{
                        $result[$key] = $this->strOneValidate($data, $validator, $error, $success);
                    }
                }
            }
            return $this->groupResult($result, $error, $success);
        }else{
            throw new Exception("must be string or array", 1);
        }
    }

    // 单条验证
    public function strOneValidate($data, $validate, $error='', $success=''){
        $param = null;
        if( substr($validate, 0, 1)==='/' ){
            $method = self::PREPIX.'regex';
            return $this->$method($data, $validate, $error, $success);
        }else{
            $roule = explode(',', $validate);
            $method = self::PREPIX.$roule[0];   // 方法名称
            $param = isset($roule[1]) ? $roule[1] : null;   // 方法参数
            if (is_callable(array($this, $method))) {
                return $this->$method($data, $param, $error, $success);
            }else{
                throw new Exception("have no this validate method", 1);
            }

        }
    }


    // 获取某一组的true或false false OR true
    // 与或非运算基本原则：
    // false OR （任何值） = 任何值
    // true AND （任何值） = 任何值
    // groupBooles 必须为数组类型
    public function groupResult($groupBooles){
        $baseOneType = 'AND';
        $baseOneArr = array();
        // 第一次循环
        foreach ($groupBooles as $key => $groupboole) {
            if (is_array($groupboole)) {
                foreach ($groupboole as $k => $v) {
                    if (is_array($v)) {
                        $keyName = strstr($k, "{#", true)===false ? $k : strstr($k, "{#", true);
                        if (strtoupper($keyName)=='OR') {
                            $baseOneArr[$key][$k] = in_array(true, $v) ? true : false;
                        }else{
                            $baseOneArr[$key][$k] = in_array(false, $v) ? false : true;
                        }
                    }else{
                        $baseOneArr[$key][$k] = $v;
                    }
                }
            }else{
                if ($key===self::LOGIC) {
                    $baseOneType = (strtoupper(trim($groupboole))==='OR') ? 'OR' : 'AND';
                }else{
                    $baseOneArr[$key] = $groupboole;
                }
            }
        }



        foreach ($baseOneArr as $key => $groupboole) {
            if (is_array($groupboole)) {
                $keyName = strstr($key, "{#", true)===false ? $key : strstr($key, "{#", true);
                if (strtoupper($keyName)=='OR') {
                    $baseOneArr[$key] = in_array(true, $groupboole) ? true : false;
                }else{
                    $baseOneArr[$key] = in_array(false, $groupboole) ? false : true;
                }
            }
        }

        // 判断是否为一维数组
        if (count($baseOneArr)===count($baseOneArr, 1)) {
            if ($baseOneType==='OR') {
                $return = in_array(true, $baseOneArr) ? true : false;
            }else{
                $return = in_array(false, $baseOneArr) ? false : true;
            }
            return $return;
        }else{
            throw new Exception("array types deep error", 1);
        }
    }

    public function getError(){
        return $this->_error;
    }

    public function getErrorField(){
        return $this->_erroField;
    }

    public function getSuccess(){
        return $this->_success;
    }





    // ** ------------------------- Validators ------------------------------------ ** //
    
    /**
     * [validate_preg 正则验证 ]
     * @param  [type] $data    [ 待验证数据 ]
     * @param  [type] $preg    [ 正则表达式 ]
     * @param  string $error   [ 错误信息 ]
     * @param  string $success [ 成功信息 ]
     * @return [type]          [ true|false ]
     */
    public function validate_regex($data, $preg, $error='', $success=''){
        if (preg_match($preg, $data)) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    /**
     * [validate_namechart 以字母开头的字母数字下划线 -. 组成的用户名]
     * @param  [type] $data    [description]
     * @param  [type] $param   [description]
     * @param  string $error   [description]
     * @param  string $success [description]
     * @return [type]          [description]
     */
    public function validate_namechart($data, $param=null, $error='', $success=''){
        if (preg_match('/^[a-zA-Z][a-zA-Z0-9_-.]+$/', $data)) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }
    // 缺一个密码验证正则 8 - 30 个字符，必须同时包含三项（大写字母、小写字母、数字、 ()`~!@#$%^&*-+=|{}[]:;'<>,.?/ 中的特殊符号）。



    /**
     * [validate_contains 数组中是否包含 Usage: '<index>' => 'contains,value value value' 包含返回 true 不包含返回 false]
     * @param  [type] $data    [description]
     * @param  [type] $param   [description]
     * @param  string $error   [description]
     * @param  string $success [description]
     * @return [type]          [description]
     */
    public function validate_contains($data, $param, $error='', $success=''){
        $param = explode(chr(32), $param);
        if (in_array($data, $param)) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    /**
     * [validate_notcontains 数组中是否不包含 Usage: '<index>' => 'contains,value value value' 包含返回 false 不包含返回 true]
     * @param  [type] $data    [description]
     * @param  [type] $param   [description]
     * @param  string $error   [description]
     * @param  string $success [description]
     * @return [type]          [description]
     */
    public function validate_notcontains($data, $param, $error='', $success=''){
        $param = explode(chr(32), $param);
        if (in_array($data, $param)) {
            $this->_error = $error;
            return false;
        }else{
            $this->_success = $success;
            return true;
        }
    }


    /**
     * Check if the specified key is present and not empty.
     *
     * Usage: '<index>' => 'required'
     *
     * @param string $field
     * @param array  $input
     * @param null   $param
     *
     * @return mixed
     */
    public function validate_required($data, $param=null, $error='', $success=''){
        if (isset($data) && !empty($data)) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    /**
     * Determine if the provided email is valid.
     *
     * Usage: '<index>' => 'valid_email'
     *
     * @param string $field
     * @param array  $input
     * @param null   $param
     *
     * @return mixed
     */
    public function validate_email($data, $param=null, $error='', $success=''){
        if (filter_var($data, FILTER_VALIDATE_EMAIL)===false) {
            $this->_error = $error;
            return false;
        }else{
            $this->_success = $success;
            return true;
        }
    }

    /**
     * Determine if the provided value length is less or equal to a specific value.
     *
     * Usage: '<index>' => 'maxlen,240'
     *
     * @param string $field
     * @param array  $input
     * @param null   $param
     *
     * @return mixed
     */
    public function validate_maxlen($data, $param, $error='', $success=''){
        if (!$this->validate_required($data)) {
            $this->_error = $error;
            return false;
        }

        if (function_exists('mb_strlen')) {
            if (mb_strlen($data) <= (int) $param) {
                $this->_success = $success;
                return true;
            }else{
                $this->_error;
                return false;
            }
        } else {
            if (strlen($data) <= (int) $param) {
                $this->_success = $success;
                return true;
            }else{
                $this->_error = $error;
                return false;
            }
        }
    }

    /**
     * Determine if the provided value length is more or equal to a specific value.
     *
     * Usage: '<index>' => 'minlen,4'
     *
     * @param string $field
     * @param array  $input
     * @param null   $param
     *
     * @return mixed
     */
    public function validate_minlen($data, $param, $error='', $success=''){
        if(!$this->validate_required($data)){
            $this->_error = $error;
            return false;
        }
        if (function_exists('mb_strlen')) {
            if (mb_strlen($data) >= (int) $param) {
                $this->_success = $success;
                return true;
            }else{
                $this->_error = $error;
                return false;
            }
        } else {
            if (strlen($data) >= (int) $param) {
                $this->_success = $success;
                return true;
            }else{
                $this->_error = $error;
                return false;
            }
        }

    }

    /**
     * Determine if the provided value length matches a specific value.
     *
     * Usage: '<index>' => 'eqlen,5'
     *
     * @param string $field
     * @param array  $input
     * @param null   $param
     *
     * @return mixed
     */
    public function validate_eqlen($data, $param, $error='', $success=''){
        if(!$this->validate_required($data)){
            $this->_error = $error;
            return false;
        }
        if (function_exists('mb_strlen')) {
            if (mb_strlen($data) == (int) $param) {
                $this->_success = $success;
                return true;
            }else{
                $this->_error = $error;
                return false;
            }
        } else {
            if (strlen($data) == (int) $param) {
                $this->_success = $success;
                return true;
            }else{
                $this->_error = $error;
                return false;
            }
        }
    }

    /**
     * Determine if the provided value is a valid number or numeric string.
     *
     * Usage: '<index>' => 'numeric'
     *
     * @param string $field
     * @param array  $input
     * @param null   $param
     *
     * @return mixed
     */
    public function validate_numeric($data, $param=null, $error='', $success='')
    {
        if (is_numeric($data)) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    /**
     * Determine if the provided value is a valid integer.
     *
     * Usage: '<index>' => 'integer'
     *
     * @param string $field
     * @param array  $input
     * @param null   $param
     *
     * @return mixed
     */
    public function validate_integer($data, $param, $error='', $success='')
    {
        if (filter_var($data, FILTER_VALIDATE_INT) === false) {
            $this->_error = $error;
            return false;
        }else{
            $this->_success = $success;
            return true;
        }
    }

    /**
     * Determine if the provided value is a PHP accepted boolean.
     *
     * Usage: '<index>' => 'boolean'
     *
     * @param string $field
     * @param array  $input
     * @param null   $param
     *
     * @return mixed
     */
    public function validate_boolean($data, $param=null, $error='', $success=''){
        if (filter_var($data, FILTER_VALIDATE_BOOLEAN)===true) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    /**
     * Determine if the provided value is a valid float.
     *
     * Usage: '<index>' => 'float'
     *
     * @param string $field
     * @param array  $input
     * @param null   $param
     *
     * @return mixed
     */
    public function validate_float($data, $param=null, $error='', $success=''){
        if (filter_var($data, FILTER_VALIDATE_FLOAT)===true) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    /**
     * Determine if the provided value is a valid URL.
     *
     * Usage: '<index>' => 'valid_url'
     *
     * @param string $field
     * @param array  $input
     * @param null   $param
     *
     * @return mixed
     */
    public function validate_valid_url($data, $param, $error='', $success=''){
        if (filter_var($data, FILTER_VALIDATE_URL)===true) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    /**
     * 验证域名 或者 host 是否被解析 ( A记录解析, 不管解析到的ip服务器是否可用) 域名是否可用
     *
     * Usage: '<index>' => 'url_exists,www.163.com'
     *
     * @param string $field
     * @param array  $input
     * @param null   $param
     *
     * @return mixed
     */
    public function validate_hostexists($data, $param, $error='', $success=''){
        $url = parse_url(strtolower($data));
        if (isset($url['host'])) {
            $url = $url['host'];
        }
        if (function_exists('checkdnsrr')) {
            if (checkdnsrr(idn_to_ascii($url), 'A') === false) {
                $this->_error = $error;
                return false;
            }else{
                $this->_success = $success;
                return true;
            }
        } else {
            if (gethostbyname($url) == $url) {
                $this->_error = $error;
                return false;
            }else{
                $this->_success = $success;
                return true;
            }
        }
    }

    /**
     * Determine if the provided value is a valid IP address.
     *
     * Usage: '<index>' => 'valid_ip'
     *
     * @param string $field
     * @param array  $input
     *
     * @return mixed
     */
    public function validate_valid_ip($data, $param, $error='', $success='')
    {
        if (filter_var($data, FILTER_VALIDATE_IP) === false) {
            $this->_error = $error;
            return false;
        }else{
            $this->_success = $success;
            return true;
        }
    }

    /**
     * Determine if the provided value is a valid IPv4 address.
     *
     * Usage: '<index>' => 'valid_ipv4'
     *
     * @param string $field
     * @param array  $input
     *
     * @return mixed
     *
     * @see http://pastebin.com/UvUPPYK0
     */

    /*
     * What about private networks? http://en.wikipedia.org/wiki/Private_network
     * What about loop-back address? 127.0.0.1
     */
    public function validate_valid_ipv4($data, $param, $error='', $success='')
    {
        if (filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)===false) {
            $this->_error = $error;
            return false;
        }else{
            $this->_success = $success;
            return true;
        }
    }

    /**
     * Determine if the provided value is a valid IPv6 address.
     *
     * Usage: '<index>' => 'valid_ipv6'
     *
     * @param string $field
     * @param array  $input
     *
     * @return mixed
     */
    public function validate_valid_ipv6($data, $param, $error='', $success='')
    {
        if (filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)===false) {
            $this->_error = $error;
            return false;
        }else{
            $this->_success = $success;
            return true;
        }
    }

    /**
     * 银行卡正则
     *
     * See: http://stackoverflow.com/questions/174730/what-is-the-best-way-to-validate-a-credit-card-in-php
     * Usage: '<index>' => 'valid_cc'
     *
     * @param string $field
     * @param array  $input
     *
     * @return mixed
     */
    public function validate_valid_cc($data, $param, $error='', $success='')
    {
    }


    /**
     * IBAN 国际银行帐户号码校验
     *
     * Usage: '<index>' => 'iban'
     *
     * @param string $field
     * @param array  $input
     *
     * @return mixed
     */
    public function validate_iban($data, $param, $error='', $success=''){
        if(!$this->validate_required($data)){
            $this->_error = $error;
            return false;
        }
        static $character = array(
            'A' => 10, 'C' => 12, 'D' => 13, 'E' => 14, 'F' => 15, 'G' => 16,
            'H' => 17, 'I' => 18, 'J' => 19, 'K' => 20, 'L' => 21, 'M' => 22,
            'N' => 23, 'O' => 24, 'P' => 25, 'Q' => 26, 'R' => 27, 'S' => 28,
            'T' => 29, 'U' => 30, 'V' => 31, 'W' => 32, 'X' => 33, 'Y' => 34,
            'Z' => 35, 'B' => 11
        );

        if (!preg_match("/\A[A-Z]{2}\d{2} ?[A-Z\d]{4}( ?\d{4}){1,} ?\d{1,4}\z/", $data)) {
            $this->_error = $error;
            return false;
        }else{
            $iban = str_replace(' ', '', $data);
            $iban = substr($iban, 4).substr($iban, 0, 4);
            $iban = strtr($iban, $character);

            if (bcmod($iban, 97) != 1) {
                $this->_error = $error;
                return false;
            }else{
                $this->_success = $success;
                return true;
            }

        }

    }

    public function validate_date($data, $param, $error='', $success=''){
        $totime = strtotime($data);
        if ( $totime && (date('Y-m-d', $totime)==$data || date('Y-m-d H:i:s', $totime)==$data) ) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }


    /**
     * Determine if the provided numeric value is lower or equal to a specific value.
     *
     * Usage: '<index>' => 'max_numeric,50'
     *
     * @param string $field
     * @param array  $input
     * @param null   $param
     *
     * @return mixed
     */
    public function validate_maxnumeric($data, $param, $error='', $success='')
    {
        if (is_numeric($data) && is_numeric($data) && ($data <= $param)) {
            $this->$_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    /**
     * Determine if the provided numeric value is higher or equal to a specific value.
     *
     * Usage: '<index>' => 'min_numeric,1'
     *
     * @param string $field
     * @param array  $input
     * @param null   $param
     * @return mixed
     */
    public function validate_minnumeric($data, $param, $error='', $success='')
    {
        if (is_numeric($data) && is_numeric($param) && ($data >= $param)) {
            $this->$_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    /**
     * Determine if the provided value starts with param.
     *
     * Usage: '<index>' => 'starts,Z'
     *
     * @param string $field
     * @param array  $input
     *
     * @return mixed
     */
    public function validate_starts($data, $param, $error='', $success='')
    {
        if(!$this->validate_required($data)){
            $this->_error = $error;
            return false;
        }        
        if (strpos($input[$field], $param) !== 0) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    /**
     * Checks if a file was uploaded.
     *
     * Usage: '<index>' => 'required_file'
     *
     * @param  string $field
     * @param  array $input
     *
     * @return mixed
     */
    public function validate_requiredFile($data, $param=null, $error='', $success='')
    {
        if(!$this->validate_required($data)){
            $this->_error = $error;
            return false;
        }

        if (is_array($data) && $data['error'] !== 4) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    /**
     * Check the uploaded file for extension for now
     * checks only the ext should add mime type check.
     *
     * Usage: '<index>' => 'extension,png;jpg;gif
     *
     * @param string $field
     * @param array  $input
     *
     * @return mixed
     */
    public function validate_extension($data, $param, $error='', $success='')
    {
        if(!$this->validate_required($data)){
            $this->_error = $error;
            return false;
        }

        if (is_array($data) && $data['error'] !== 4) {
            $param = trim(strtolower($param));
            $allowed_extensions = explode(';', $param);

            $path_info = pathinfo($data['name']);
            $extension = isset($path_info['extension']) ? $path_info['extension'] : false;

            if ($extension && in_array(strtolower($extension), $allowed_extensions)) {
                $this->_success = $success;
                return true;
            }else{
                $this->_error = $error;
                return false;
            }
        }
    }

    /**
     * Determine if the provided field value equals current field value.
     *
     *
     * Usage: '<index>' => 'equalsfield,Z'
     *
     * @param string $field
     * @param string $input
     * @param string $param field to compare with
     *
     * @return mixed
     */
    public function validate_equalsfield($data, $param, $error='', $success='')
    {
        if(!$this->validate_required($data)){
            $this->_error = $error;
            return false;
        }
        if ($data == $param) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    /**
     * Determine if the provided field value is a valid GUID (v4)
     *
     * Usage: '<index>' => 'guidv4'
     *
     * @param string $field
     * @param string $input
     * @param string $param field to compare with
     * @return mixed
     */
    public function validate_guidv4($data, $param=null, $error='', $success='')
    {
        if(!$this->validate_required($data)){
            $this->_error = $error;
            return false;
        }

        if (preg_match("/\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/", $data)) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }

    }

    /**
     * Trims whitespace only when the value is a scalar.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function validate_trimScalar($data, $param=null, $error='', $success='')
    {
        if (is_scalar($data)) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    /**
     * Determine if the provided value is a valid phone number.
     *
     * Usage: '<index>' => 'phone'
     */
    public function validate_phone($data, $param=null, $error='', $success='')
    {
        if ( preg_match('/^((\+86)|(86))?(1)[3456789]{1}\d{9}$/', $data) ) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    // 电话
    public function validate_telephone($data, $param=null, $error='', $success=''){
        if ( preg_match('/^([0-9]{3,4}-)?[0-9]{7,8}$/', $data) ) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    // 身份证
    public function validate_idcard($data, $param=null, $error='', $success=''){
        if ( preg_match('/(^d{15}$)|(^d{17}(d|X|x)$)/', $data) ) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    // QQ
    public function validate_qq($data, $param=null, $error='', $success=''){
        if ( preg_match('/^[1-9][\d]{4,10}$/', $data) ) {
            $this->_success = $success;
            return true;
        }else{
            $this->_error = $error;
            return false;
        }
    }

    /**
     * JSON validator.
     *
     * Usage: '<index>' => 'valid_json_string'
     *
     * @param string $field
     * @param array  $input
     *
     * @return mixed
     */
    public function validate_valid_json_string($data, $param, $error='', $success='')
    {
        if(!$this->validate_required($data)){
            $this->_error = $error;
            return false;
        }
        if (!is_string($data) || !is_object(json_decode($data))) {
            $this->_error = $error;
            return false;
        }else{
            $this->_success = $success;
            return true;
        }
    }

    /**
     * Check if an input is an array and if the size is more or equal to a specific value.
     *
     * Usage: '<index>' => 'valid_array_size_greater,1'
     *
     * @param string $field
     * @param array  $input
     *
     * @return mixed
     */
    public function validate_valid_array_size_greater($data, $param, $error='', $success='')
    {
        if(!$this->validate_required($data)){
            $this->_error = $error;
            return false;
        }

        if (!is_array($data) || count($data) < (int)$param) {
            $this->_error = $error;
            return false;
        }else{
            $this->_success = $success;
            return true;
        }
    }

    /**
     * Check if an input is an array and if the size is less or equal to a specific value.
     *
     * Usage: '<index>' => 'valid_array_size_lesser,1'
     *
     * @param string $field
     * @param array $input
     *
     * @return mixed
     */
    public function validate_valid_array_size_lesser($data, $param, $error='', $success='')
    {
        if(!$this->validate_required($data)){
            $this->_error = $error;
            return false;
        }

        if (!is_array($data) || count($data) > (int)$param) {
            $this->_error = $error;
            return false;
        }else{
            $this->_success = $success;
            return true;            
        }
    }

    /**
     * Check if an input is an array and if the size is equal to a specific value.
     *
     * Usage: '<index>' => 'valid_array_size_equal,1'
     *
     * @param string $field
     * @param array $input
     *
     * @return mixed
     */
    public function validate_valid_array_size_equal($data, $param, $error='', $success='')
    {
        if(!$this->validate_required($data)){
            $this->_error = $error;
            return false;
        }

        if (!is_array($data) || sizeof($data) == (int)$param) {
            $this->_error = $error;
            return false;
        }else{
            $this->_success = $success;
            return true;             
        }
    }
    
    







    


}









