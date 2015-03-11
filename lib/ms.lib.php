<?php
/*
 +----------------------------------------------------------------------+
 | mini-system-0.3                                                      |
 +----------------------------------------------------------------------+
 | author: 雷果国(leiguoguo@zhisland.com)                               |
 +----------------------------------------------------------------------+
 | mini-system 主库                                                     |
 +----------------------------------------------------------------------+
 */
define('VERSION',							'mini-system-0.3');
define('DS',								DIRECTORY_SEPARATOR);		#目录分隔符
define('PS',								',');						#路径分隔符
define('SS',								'.');						#后缀分隔符
define('IC',								'_');						#标识连接符
define('URL_PC',							'://');						#URL协议连接符
define('URL_DS',							'/');						#URL路径分隔符
define('URL_QC',							'?');						#URL查询字符串连接符

define('D_LIB',								'lib');						#公共库目录名
define('D_CFG',								'config');					#配置目录名
define('D_FUN',								'function');				#业务函数目录名
define('D_WEB',								'webroot');					#web服务目录
define('D_COMM',							'common');					#公共函数库目录名

define('S_LIB',								'.lib.php');				#公共库后缀名
define('S_FUN',								'.fun.php');				#业务函数文件后缀名
define('S_CFG',								'.cfg.php');				#配置后缀名
define('S_PHP',								'.php');					#普通PHP后缀名

define('P_ROOT',							dirname(dirname(__FILE__)));	#项目根路径
define('P_LIB',								P_ROOT . DS . D_LIB);			#公共库路径
define('P_CFG',								P_ROOT . DS . D_CFG);			#配置路径
define('P_FUN',								P_ROOT . DS . D_FUN);			#业务函数文件路径
define('P_WEBROOT',							P_ROOT . DS . D_WEB);			#web服务路径

define('PHP_SAPI_CLI',						'cli');
define('PHP_SAPI_FPM_FCGI',					'fpm-fcgi');

define('U_PROT_HTTPS',						'https');
define('U_PROT_HTTP',						'http');

#协议和主机名的处理目前从简, 暂不考虑命令行模式
define('U_PROT',							array_key_exists('host', $_SERVER) && strtolower($_SERVER['host']) == 'on' ? U_PROT_HTTPS : U_PROT_HTTP);
define('U_HOST',							PHP_SAPI != PHP_SAPI_CLI ? $_SERVER['HTTP_HOST'] : 'localhost');

define('E_INVALID_PATH',					'强制加载[%s]且文件不存在');

define('MS_PLUGINS',						'_ms_plugins');
$GLOBALS[MS_PLUGINS]	= array();

#自定义错误触发器
function ms_trigger_error($level, $format) {
	$args		= func_get_args();
	$fmtargs	= array_slice($args, 2);
	$msg		= vsprintf($format, $fmtargs);
	return trigger_error($msg, $level);
}

#加载$prefix/$dir目录下的名为$name, 后缀名为$suffix的文件
function ms_load($name, $dir, $suffix, $prefix = P_ROOT, $force = TRUE) {
	$path	= $prefix . DS . $dir . DS . $name . $suffix;
	$valid	= is_file($path);
	if ( $valid ) return require_once $path;
	else if ( $force ) return ms_trigger_error(E_USER_ERROR, E_INVALID_PATH, $path);
}

#一次加载多个文件的基础封装
function ms_loads($names, $dir, $suffix, $force = TRUE) {
	if ( is_string($names) ) $names = explode(PS, $names);
	foreach ( $names as $name ) 
		ms_load(trim($name), $dir, $suffix, P_ROOT, $force);
}

#加载P_ROOT . DS . D_LIB下的库文件
function ms_load_lib($names, $force = TRUE, $dir = '') {
	ms_loads($names, D_LIB . ($dir ? DS . $dir : ''), S_LIB, $force);
}

#加载P_ROOT . DS . D_CFG下的配置文件
function ms_load_cfg($names, $force = TRUE, $dir = '') {
	ms_loads($names, D_CFG . ($dir ? DS . $dir : ''), S_CFG, $force);
}

#加载P_ROOT . DS . D_FUN下的业务文件
function ms_load_fun($names, $force = TRUE, $dir = '') {
	ms_loads($names, D_FUN . ($dir ? DS . $dir : ''), S_FUN, $force);
}

#加载P_ROOT . DS . D_FUN . DS . D_COMM下的公共函数
function ms_load_common($name, $force = TRUE) {
	ms_load_fun($name, $force, D_COMM);
}

#插件安装(执行)
function ms_plugin_install($plugin) {
	if ( !array_key_exists($plugin, $GLOBALS[MS_PLUGINS]) ) return ;
	$args		= func_get_args();
	$args		= array_slice($args, 1);
	$plugins	= $GLOBALS[MS_PLUGINS][$plugin];
	if ( is_array($plugins) ) 
		foreach ( $plugins as $callable ) 
			call_user_func_array($callable, $args);
}
#插件注册
function ms_plugin_register($plugin, $callable) {
	$GLOBALS[MS_PLUGINS][$plugin][]	= $callable;
}

#获取$_GET中的数据
function ms_get($name, $default = NULL) {
	return filter_has_var(INPUT_GET, $name) ? filter_input(INPUT_GET, $name) : $default;
}
#获取$_POST中的数据
function ms_post($name, $default = NULL) {
	return filter_has_var(INPUT_POST, $name) ? filter_input(INPUT_POST, $name) : $default;
}
#获取$_GET和$_POST中的数据
function ms_request($name, $default = NULL) {
	return filter_has_var(INPUT_GET, $name) ? filter_input(INPUT_GET, $name) : (filter_has_var(INPUT_POST, $name) ? filter_input(INPUT_POST, $name) : $default);
}
#获取$_FILES中的数据
function ms_files($name) {
	return $_FILES[$name];
}
#检查$_GET中是否存在变量
function ms_exists_get($name) {
	return filter_has_var(INPUT_GET, $name);
}
#检查$_POST中是否存在变量
function ms_exists_post($name) {
	return filter_has_var(INPUT_POST, $name);
}
#检查$_GET和$_POST中是否存在变量
function ms_exists_request($name) {
	return ms_exists_get($name) || ms_exists_post($name);
}
#检查$_FILES中是否存在上传文件
function ms_exists_file($name) {
	return array_key_exists($name, $_FILES);
}
