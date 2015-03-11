<?php
/*
 +----------------------------------------------------------------------+
 | api.zhisland.com                                                     |
 +----------------------------------------------------------------------+
 | author: 雷果国(leiguoguo@zhisland.com)                               |
 +----------------------------------------------------------------------+
 | api 公共函数库                                                       |
 +----------------------------------------------------------------------+
 */
#从$datas中读取指定字段
function read_field_from_list($field, $datas) {
	$return	= array();
	if ( is_array($datas) ) 
		foreach ( $datas as $data ) 
			$return[]	= $data[$field];
	return array_unique($return);
}
#应用到array_walk等类似场景
function array_walk_call(&$ele, $key, $func) {
	$args	= func_get_args();
	$args	= array_slice($args, 3);
	array_unshift($args, $ele);
	$ele	= call_user_func_array($func, $args);
}
#将$input看作key的集合, 读取$mapping中的值, 主要用作预定义值的映射取值
function array_mapping_filter($input, $mapping) {
	$keys	= is_array($input) ? $input : explode(',', strval($input));
	array_walk($keys, 'array_walk_call', 'trim');
	$values	= array();
	foreach ( $keys as $key ) 
		if ( array_key_exists($key, $mapping) )
			$values[$key]	= $mapping[$key];
	return $values;
}
function array_mapping_filter_bit($input, $mapping) {
	$return	= array();
	$input	= str_split(decbin($input));
	$l		= count($input);
	foreach ( $input as $k => $v )
		if ( $v == 1 && array_key_exists(($mk = pow(2, $l - $k - 1)), $mapping) ) 
			$return[]	= $mapping[$mk];
	return $return;
}
#将list 数组变成map
function array_list2map($input, $key, $value) {
    $return  = array();
    foreach ( $input as $row ) {
        if ( !empty($row) && isset($row[$key]) ) {
            $return[$row[$key]] = $row[$value];
        }
    }
    return $return;
}
#html实体转换
function html_convert_entities($string) {
	return preg_replace_callback('/&([a-zA-Z][a-zA-Z0-9]+);/S',
			'convert_entity', $string);
}
#html实体转换映射
function convert_entity($matches) {
	static $table = array(
		'quot'		=> '"',
		'amp'		=> '&',
		'lt'		=> '<',
		'gt'		=> '>',
		'nbsp'		=> ' ',
	);
	return isset($table[$matches[1]]) ? $table[$matches[1]] : $matches[0];
}
#取得数组中的最大字段
function array_field_max($array, $field) {
	if ( !is_array($array) || empty($array) ) return FALSE;
	reset($array);
	$first	= current($array);
	$max	= $first[$field];
	while ( $ele = next($array) ) 
		$max	= max($max, $ele[$field]);
	return $max;
}
#取得数组中的最小字段
function array_field_min($array, $field) {
	if ( !is_array($array) || empty($array) ) return FALSE;
	reset($array);
	$first	= current($array);
	$min	= $first[$field];
	while ( $ele = next($array) ) 
		$min	= min($min, $ele[$field]);
	return $min;
}
#获取初始向量
function m_des_get_iv($cipher, $mode) {
	$size	= mcrypt_get_iv_size($cipher, $mode);
	$iv		= mcrypt_create_iv($size, MCRYPT_RAND);
	return $iv;
}
#加密
function m_des_encrypt($input, $key) {
	$key	= substr(md5($key), 0, 8);
	$iv		= m_des_get_iv(MCRYPT_DES, MCRYPT_MODE_ECB);
	$encrypted_data	= mcrypt_encrypt(MCRYPT_DES, $key, $input, MCRYPT_MODE_ECB, $iv);
	return trim(chop(base64_encode($encrypted_data)));
}
#解密
function m_des_decrypt($input, $key) {
	$input = trim(chop(base64_decode($input)));
	$key = substr(md5($key), 0, 8);
	$iv		= m_des_get_iv(MCRYPT_DES, MCRYPT_MODE_ECB);
	$decrypted_data	= mcrypt_decrypt(MCRYPT_DES, $key, $input, MCRYPT_MODE_ECB, $iv);
	return trim(chop($decrypted_data));
}
#获取文件更新时间
function fctime($path) {
	$stat	= stat($path);
	return $stat['ctime'];
}
#获取来源ip
function get_ip(){
	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
	{
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}elseif (isset($_SERVER["HTTP_CLIENT_IP"])){
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	}else{
		$ip = $_SERVER["REMOTE_ADDR"];
	}
	return $ip;
}
