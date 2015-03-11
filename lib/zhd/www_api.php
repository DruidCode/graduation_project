<?php
/*
 +----------------------------------------------------------------------+
 | api.zhisland.com                                                     |
 +----------------------------------------------------------------------+
 | author: 雷果国(leiguoguo@zhisland.com)                               |
 +----------------------------------------------------------------------+
 | api 接口调用侧web_api                                                |
 +----------------------------------------------------------------------+
 */
define('API_PATH',                      dirname(__FILE__));
if(!defined('IC')) define('IC',                           '_');
if(!defined('SS')) define('SS',                           '.');
if(!defined('DS')) define('DS',                           '/');

define('API_FUNC_PATH',                API_PATH.DS."function");

if ( defined('LIBCONFIG_API_FUNC_PRE') )define('API_FUNC_PRE', LIBCONFIG_API_FUNC_PRE);
else define('API_FUNC_PRE',                 "api_www_");

if ( defined('LIBCONFIG_AUTH_KEY') )define('AUTH_KEY', LIBCONFIG_AUTH_KEY);
else define('AUTH_KEY',						'a14%xubEd#s%sdA$');

define('FROM_SIGN_FLAG',				'sign');

if ( defined('LIBCONFIG_API_WWW_HOST') )define('API_WWW_HOST', LIBCONFIG_API_WWW_HOST);
else define('API_WWW_HOST',					'http://denglianwen.api.deving.zhisland.com/web'); //开发
// define('API_WWW_HOST',					'http://capi.zhisland.com/web');       //测试

      
//define('API_WWW_HOST',					'https://www.zhisland.com/uniapi/web');  //线上

define('KEY_UID',						'uid');

define('API_WWW_UA',					'www-api of www.zhisland.com/curl extend of php/libcurl');

define('API_WWW_ERROR_HANDLER',			'__api_www_error_handler');

define('API_WWW_ERR_CURL_NO',			-1);	#curl发生错误
define('API_WWW_ERR_CURL_MSG',          "curl发生错误");

define('API_WWW_ERR_SERVER_NO',				 -2);	#服务端HTTP状态码不正常
define('API_WWW_ERR_SERVER_MSG',       		 "服务端HTTP状态码不正常");

define('API_WWW_ERR_FILE_NO_EXISTS',    	 -3);
define('API_WWW_ERR_FILE_NO_EXISTS_MSG', 	'对应的API文件不存在');

define('API_WWW_ERR_FUN_NO_EXISTS',      	-4);
define('API_WWW_ERR_FUN_NO_EXISTS_MSG',		'对应的api函数不存在');

define('API_WWW_ERROR_INTERFACE_NOT_DEFINED_NO',  -5);
define('API_WWW_ERROR_INTERFACE_NOT_DEFINED_MSG', "接口未定义");

define('API_WWW_ERR_ARGUMENT',		      	-6);
define('API_WWW_ERR_ARGUMENT_MSG',		'默认api接口参数错误');

define('API_WWW_DEFAULT_MID',	  		0);

define('API_WWW_ERRNO',						'__api_www_errno');

$GLOBALS[API_WWW_ERRNO]		= FALSE;

require_once API_PATH.DS.'api_common.php';

#www侧api错误处理
function __api_www_error_handler($curl, $url, $errno, $errstr) {
	api_www_errno($errno);
}
#www侧错误信息设置
function api_www_errno($errno = NULL) {
	if ( !is_null($errno) )
		$GLOBALS[API_WWW_ERRNO]	= $errno;
	return $GLOBALS[API_WWW_ERRNO];
}

function api_www_url_build($interface) {
	return API_WWW_HOST.$interface;
}

function api_www_common_input(&$data) {
	$data[FROM_SIGN_FLAG]	= auth_encode_key($data, AUTH_KEY); 
}

function api_www_set_mid($mid = ''){
	return api_www_deal_mid($mid);
}

function api_www_get_mid(){
	return api_www_deal_mid();
}

function api_www_deal_mid($mid=NULL){
	static $current_uid;
	if(is_null($current_uid)) $current_uid = isset($_SESSION['mid']) ? $_SESSION['mid'] : API_WWW_DEFAULT_MID;
	if(!is_null($mid)) $current_uid = intval($mid);
	return $current_uid;
}

function api_www_set_user_info(&$data){
      $data['mid'] = api_www_get_mid();
}

#api侧请求基础接口
function api_www_request($data=NULL,$interface="") {
	// api_www请求相关初始工作 
	api_www_errno(FALSE);
	if($interface === ""){
		if(NewApi::$interface){
            $interface = NewApi::$interface;
		}else{
		    return api_resp_status(API_WWW_ERROR_INTERFACE_NOT_DEFINED_NO,API_WWW_ERROR_INTERFACE_NOT_DEFINED_MSG);
		}
	}
	api_behavior(array(
		REQUEST_BEHAVIOR_USER_AGENT, 
		REQUEST_BEHAVIOR_ERROR_HANDLER
	), array(
		API_WWW_UA, 
		API_WWW_ERROR_HANDLER, 
	));
    
	api_www_set_user_info($data);
	#请求参数组织
	api_www_common_input($data);
	$url  = api_www_url_build($interface);;
	#实际的请求发送
	$result		= api_post($url, $data);
	if ( api_www_errno() ) return api_resp_status(API_WWW_ERR_CURL_NO,API_WWW_ERR_CURL_MSG);
	$resp_code	= api_resp_code($result);
	$resp_body	= api_resp_body($result);
	if ( $resp_code >= 400 ) return api_resp_status(API_WWW_ERR_SERVER_NO,API_WWW_ERR_SERVER_MSG);
	return json_decode($resp_body, TRUE);
}

#整理返回结果
function tidy_resp($resp){
   	if($resp['status_code'] == 0){
	    return array("code" => $resp['status_code'],"data"=>'');
	}else{
	    return array("code" => $resp['status_code'],"data"=>$resp["status_desc"]);
	}
}

#错误返回
function api_resp_status($errno,$erromsg){
      return array("status_code" => $errno, "status_desc" => $erromsg);
}

#构造API类
function api($filename = "",$version = "",$type = "json"){
	   $new_api = new NewApi;
	   $new_api->setFunPre(API_FUNC_PRE);
	   $new_api->setFileName($filename);
	   $new_api->setType($type);
	   $new_api->setVersion($version);
       return $new_api;
}

#api类
class NewApi{
	private $filename  = "";
	private $version = ""; 
	private $type      = "";
	private $fun_pre = ""; 
	static  $interface = "";

	function __call($method,$argument){
		$this->fun_pre = $this->fun_pre.$this->filename.IC;
		$interface_version = "";
		if($this->version != ""){
			$method_name = $this->fun_pre.$method.IC.str_replace(SS,IC,$this->version);
			$interface_version = DS.$this->version ;
		}else{
			$method_name = $this->fun_pre.$method ;
		}
		self::$interface =$interface_version.DS.$this->filename.DS.$method.SS.$this->type;//拼装接口
		if($this->loadFile() && is_callable($method_name)){ 
			return call_user_func_array($method_name,$argument); 
		}else {
			return $this->defaultApiInterface($argument);
		}
	}

	private function defaultApiInterface($argument){
		if(count($argument) == 0) $argument = NULL;
		else{
			$argumet_is_right = true;//检查输入参数是否正确
			if(count($argument) >1 || !is_array($argument[0])){
				$argumet_is_right = false;
			}else{
				foreach($argument[0] as $key=>$val){
					if(is_numeric($key) || !is_scalar($val)){
						$argumet_is_right = false;
					}
				}
			}
			if($argumet_is_right === false) return api_resp_status(API_WWW_ERR_ARGUMENT,API_WWW_ERR_ARGUMENT_MSG);
			$argument = $argument[0];
		}
		return tidy_resp(api_www_request($argument));
	}

	#加载文件，正确返回true，错误返回false
	private function loadFile(){
		if($this->version !="" && is_numeric($this->version) && strpos($this->version,".")!==false){
			$fun_arr = explode(".",$this->version);
			$file_path = API_FUNC_PATH.DS.$this->filename.SS."fun".SS.$fun_arr[0].SS."php";
		}else{
			$file_path = API_FUNC_PATH.DS.$this->filename.".fun.php";
		}
		if(file_exists($file_path)) {
			include_once $file_path;
			return true;
		}else{
			return false;
		}
	}

	function setVersion($version){
		$this->version = $version;
	}

	function setFunPre($fun_pre){
		$this->fun_pre = $fun_pre;
	}

	function setFileName($filename){
		$this->filename = $filename;
	}

	function setType($type){
		$this->type = $type;
	}
}

