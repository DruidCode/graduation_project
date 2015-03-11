<?php
/*
   +----------------------------------------------------------------------+
   | MS_PLATFORM (信息平台)                                               |
   +----------------------------------------------------------------------+
   | Copyright (c) 2008-2010   一三九移动互联 (http://shequ.10086.cn)     |
   +----------------------------------------------------------------------+
   | 此文件是所有MS_PLATFORM项目的头文件.                                 |
   | 简要说明文件的结构和功能,作者等信息.                                 |
   +----------------------------------------------------------------------+
   | Authors: 雷果国 <leiguoguo@aspirehld.com>                            |
   | MLIST:                                                               |
   +----------------------------------------------------------------------+
*/
/* $Id: header,v 0.1 2011-07-08 雷果国 Exp $ */

#客户端类型
define('MS_CLIENT_TYPE_WWW',					'www');						#客户端类型: www
define('MS_CLIENT_TYPE_WAP',					'wap');						#客户端类型: wap

#MS平台信息配置
define('MS_PLATFORM_PORT',						8001);						#MS平台端口地址
define('MS_PLATFORM_HOST',						'api.sm.zhisland.com');		#MS平台域名
define('MS_PLATFORM_PATH_SMS',					'/sms_in.php');				#MS平台短信输入页面
define('MS_PLATFORM_PATH_MMS',					'/mms_in.php');				#MS平台彩信输入页面
define('MS_PLATFORM_PATH_O_SMS',				'/sms_in.php');				#MS平台异网短信输入页面
define('MS_PLATFORM_IP',						'ms_platform_ip');			#MS平台IP地址
$GLOBALS[MS_PLATFORM_IP]	= array(
	MS_CLIENT_TYPE_WWW		=> array('211.151.0.68'), 
	MS_CLIENT_TYPE_WAP		=> array('192.168.13.71'), 
);

define('MS_CLIENT_LOG_PATH',					'/www/web/www.ms-platform.com/log/');	#客户端日志路径(记录连接平台失败)
define('MS_CLIENT_LOG_FILE_PREFIX',				'log_');						#客户端日志文件名前缀
define('MS_CLIENT_LOG_FILE_TIME_FMT',			'Y-m-d-H');						#客户端日志文件名时间格式

#默认值设置
define('MS_DEFAULT_PRIORITY',					200);			#默认优先级
define('HTTP_DEFAULT_CONNECT_TIMEOUT',			3);				#默认HTTP连接超时时间
define('HTTP_DEFAULT_READ_TIMEOUT',				3);				#默认HTTP读超时时间
define('HTTP_DEFAULT_READ_BUFFSIZE',			2048);			#默认HTTP读缓冲区大小

#协议数据Key
define('MS_PROT_K_VERSION',						'v');				#协议版本
define('MS_PROT_K_DATA',						'data');			#要发送的信息的数据
define('MS_PROT_K_SIG',							'sig');				#签名 = md5(json_encode($data) . $passwd)
#MS_PROT_K_DATA内的协议Key
define('MS_PROT_K_DATA_K_ID',					'id');				#客户端ID(MS内部分配, 同时对应分配source/passwd)
define('MS_PROT_K_DATA_K_SN',					'sn');				#随机序号
define('MS_PROT_K_DATA_K_STYPE',				'stype');			#源类型
define('MS_PROT_K_DATA_K_SOURCE',				'source');			#源对象, 在分配客户端ID时分配
define('MS_PROT_K_DATA_K_OBJ',					'o');				#目标对象, 比如用户ID
define('MS_PROT_K_DATA_K_OTYPE',				'ot');				#目标对象类型
define('MS_PROT_K_DATA_K_OMOBILE',				'omobile');			#要发送的手机号. 群发协议时为逗号分隔的手机号. 最大支持一次100个
define('MS_PROT_K_DATA_K_MSG',					'msg');				#要发送的消息
define('MS_PROT_K_DATA_K_SNUMBER',				'snumber');			#下行号码(1065 8103 分配的client_id使用的通道号 snumber四项组成最终下行号)
define('MS_PROT_K_DATA_K_SENDTIME',				'sendtime');		#发送时间
define('MS_PROT_K_DATA_K_CALLBACK',				'callback');		#回调
define('MS_PROT_K_DATA_K_PRIORITY',				'priority');		#优先级(不设置使用ms平台根据client_id设置的默认优先级)
#彩信内容协议Key
define('MS_PROT_MMS_K_TITLE',					'm_title');			#彩信标题
define('MS_PROT_MMS_K_HEIGHT',					'height');			#彩信总高度
define('MS_PROT_MMS_K_WIDTH',					'width');			#彩信总宽度
define('MS_PROT_MMS_K_IMG_HEIGHT',				'img_h');			#图片部分高度
define('MS_PROT_MMS_K_IMG_WIDTH',				'img_w');			#图片部分宽度
define('MS_PROT_MMS_K_IMG_OFFSET_LEFT',			'img_l');			#图片部分左偏移
define('MS_PROT_MMS_K_IMG_OFFSET_TOP',			'img_t');			#图片部分上偏移
define('MS_PROT_MMS_K_TEXT_HEIGHT',				'text_h');			#文本部分高度
define('MS_PROT_MMS_K_TEXT_WIDTH',				'text_w');			#文本部分宽度
define('MS_PROT_MMS_K_TEXT_OFFSET_LEFT',		'text_l');			#文本部分左偏移
define('MS_PROT_MMS_K_TEXT_OFFSET_TOP',			'text_t');			#文本部分上偏移
define('MS_PROT_MMS_K_FRAME',					'frame');			#彩信内容信息列表(每个元素代表彩信的一帧)
define('MS_PROT_MMS_K_FRAME_K_TITLE',			'title');			#当前帧文本部分内容
define('MS_PROT_MMS_K_FRAME_K_IMG_SRC',			'img_src');			#当前帧图片部分地址
define('MS_PROT_MMS_K_FRAME_K_DUR',				'dur');				#当前帧持续时间

#HTTP请求配置信息
define('COLON',									':');
define('SP',									' ');
define('CRLF',									"\r\n");
define('PLACEHOLDER_PATH',						'{path}');
define('PLACEHOLDER_HOST',						'{host}');
define('PLACEHOLDER_CONTENT_LENGTH',			'{content_length}');
define('PLACEHOLDER_CONTENT',					'{content}');
define('HTTP_REQUEST_HEADER',					'POST' . SP . PLACEHOLDER_PATH . SP . 'HTTP/1.1' . CRLF . 
												'Accept' . COLON . SP . '*/*' . CRLF . 
												'Host' . COLON . SP . PLACEHOLDER_HOST . CRLF . 
												'Content-type' . COLON . SP . 'application/x-www-form-urlencoded' . CRLF . 
												'Content-length' . COLON . SP . PLACEHOLDER_CONTENT_LENGTH . CRLF . 
												'Connection' . COLON . SP . 'close' . CRLF . CRLF);
define('HTTP_REQUEST_BODY',						PLACEHOLDER_CONTENT . CRLF . CRLF);

#协议中使用的常量定义: 协议版本
define('MS_CONST_VER_SMS',						1);				#单条短信协议
define('MS_CONST_VER_MMS',						1.1);			#单条彩信协议
define('MS_CONST_VER_O_SMS',					1.2);			#单条异网短信协议
define('MS_CONST_VER_BATCH_SMS',				2);				#批量短信协议
define('MS_CONST_VER_BATCH_MMS',				2.1);			#批量彩信协议
define('MS_CONST_VER_BATCH_O_SMS',				2.2);			#批量异网短信协议
#关于源类型和目标类型, 目前典型的应用是: 应用->用户, 即应用向用户下行
#协议中使用的常量定义:源类型
define('MS_CONST_STYPE_APP',					0x0);			#应用
define('MS_CONST_STYPE_ID',						0x1);			#用户
define('MS_CONST_STYPE_PHONE',					0x2);			#手机
#协议中使用的常量定义:目标类型
define('MS_CONST_OTYPE_APP',					0x0);			#应用
define('MS_CONST_OTYPE_ID',						0x1);			#用户
define('MS_CONST_OTYPE_PHONE',					0x2);			#手机

#日志编号
define('LOG_NO_WRITE_ERROR',					1);				#写错误/连接超时
define('LOG_NO_READ_TIMEOUT',					2);				#读超时
define('LOG_NO_READ_ERROR',						3);				#读取错误
define('LOG_NO_RESPONSE_ERROR',					4);				#失败的HTTP响应
define('LOG_NO_SEND_ERROR',						5);				#MS平台有错误返回
define('LOG_NO_HTTP_SUCCESS',					6);				#发送成功
define('LOG_NO_CONNECT_ERROR',					7);				#连接失败
/**
 * MS发送短信客户端入口
 * @param mixed $client				客户端类型, 目前有: MS_CLIENT_TYPE_WWW, MS_CLIENT_TYPE_WAP
 * @param mixed $version 			协议版本, 对应MS_CONST_VER_*系列常量
 * @param mixed $client_id			客户端ID, 向MS平台申请(联系leiguoguo@aspirehld.com/shixiangpeng@aspirehld.com)
 * @param mixed $source				源ID, 与$client_id一起申请
 * @param mixed $secret_key			校验密钥, 于$client_id一起申请
 * @param mixed $mobiles			发送手机号码, 如果是多条, 用逗号","分隔, 不允许有其他字符
 * @param mixed $content			信息内容, 如果是彩信, 请使用ms_mms_content()函数生成内容
 * @param string $snumber			下行长号码尾部. 下行长号码组成: 1065 8103 9(申请$client_id时分配) $snumber
 * @param mixed $priority			优先级, 如不设置使用默认优先级
 * @param string $obj				目标对象. 比如: 用户UID
 * @param string $callback			回调.
 * @param mixed $source_type 		源对象类型. 目前通常都是默认的APP(应用)
 * @param mixed $obj_type			目标对象类型. 目前通常都是默认的ID(用户)
 * @return bool						MS平台响应是否成功, 成功返回true, 否则返回false
 */
function ms_send($client, $version, $client_id, $source, $secret_key, $mobiles, $content, $snumber = '', $priority = MS_DEFAULT_PRIORITY, 
		$obj = '', $callback = '', $source_type = MS_CONST_STYPE_APP, $obj_type = MS_CONST_STYPE_ID) {
	$host			= ms_get_host();
	$ips			= ms_get_ip($client);
	$port			= ms_get_port();
	$path			= ms_get_path($version);
	$data			= ms_get_data($client_id, mt_rand(), $source_type, $source, $obj, $obj_type, $mobiles, $content, $snumber, time(), $callback, $priority);
	$post_data		= ms_get_postdata($version, $secret_key, $data);
	$post_length	= strlen($post_data);
	$header			= ms_get_header($host, $path, $post_length);
	$body			= ms_get_body($post_data);
	$begin_time		= microtime(true);
	foreach($ips as $ip) {
		if($fs = @fsockopen($ip, $port, $errno, $errstr, HTTP_DEFAULT_CONNECT_TIMEOUT)) break;
	}
	if(!$fs) {
		return false_log(LOG_NO_CONNECT_ERROR, $ips);
	}
	$request		= $header . $body;
	if(!fwrite($fs, $request, strlen($request))) 
		return fs_false_log($fs, LOG_NO_WRITE_ERROR, array('request' => $request));
	stream_set_timeout($fs, HTTP_DEFAULT_READ_TIMEOUT);
	$response 		= "";
	while ($buff = fread($fs, HTTP_DEFAULT_READ_BUFFSIZE)) {
		$response	.= $buff;
	}
	$stream_info	= stream_get_meta_data($fs);
	if($stream_info['timed_out'])
		return fs_false_log($fs, LOG_NO_READ_TIMEOUT, array('request' => $request));
	else if(!$response)
		return fs_false_log($fs, LOG_NO_READ_ERROR, array('request' => $request));
	$end_time		= microtime(true);
	$resp_time		= $end_time - $begin_time;
	list($response_header, $response_body) = ms_parse_response($response);
	if(!ms_check_response($response_header, $response_body, $errno)) {
		return fs_false_log($fs, $errno, array('req_body' => $body, 'resp_body' => $response_body, 'resp_time' => $resp_time));
	} else {
		return fs_true_log($fs, LOG_NO_HTTP_SUCCESS, array('req_body' => $body, 'resp_body' => $response_body, 'resp_time' => $resp_time));
	}
}
/**
 * 获取MS平台HOST名称 
 * @return string					MS平台HOST名称
 */
function ms_get_host() {
	return MS_PLATFORM_HOST;
}
/**
 * 获取MS平台端口号
 * @return int						MS平台端口号
 */
function ms_get_port() {
	return MS_PLATFORM_PORT;
}
/**
 * 获取MS平台IP地址
 * @param string client				客户端类型: WWW/WAP
 * @return string					MS平台IP地址
 */
function ms_get_ip($client) {
	return $GLOBALS[MS_PLATFORM_IP][$client];
}
/**
 * 根据协议版本获取请求路径
 * @param mixed $version 			协议版本
 * @return string					请求路径
 */
function ms_get_path($version) {
	$version	= $version * 10 % 10;
	switch($version) {
		case 1:
			$path	= MS_PLATFORM_PATH_MMS;
			break;
		case 2:
			$path	= MS_PLATFORM_PATH_O_SMS;
			break;
		case 0:
		default:
			$path	= MS_PLATFORM_PATH_SMS;
			break;
	}
	return $path;
}
/**
 * 获取一个请求Data
 * @param mixed $id 			client_id
 * @param mixed $sn 			随机序列号
 * @param mixed $stype 			源类型
 * @param mixed $source 		源
 * @param mixed $o 				目标
 * @param mixed $ot 			目标类型
 * @param mixed $omobile 		手机号码
 * @param mixed $msg 			消息内容
 * @param mixed $snumber 		下行尾号
 * @param mixed $sendtime 		下发时间
 * @param mixed $callback 		回调
 * @param mixed $priority 		优先级
 * @return string				json编码的请求data
 */
function ms_get_data($id, $sn, $stype, $source, $o, $ot, $omobile, $msg, $snumber, $sendtime, $callback, $priority) {
	$data = array(
		MS_PROT_K_DATA_K_ID			=>	$id, 
		MS_PROT_K_DATA_K_SN			=>	$sn, 
		MS_PROT_K_DATA_K_STYPE		=>	$stype, 
		MS_PROT_K_DATA_K_SOURCE		=>	$source, 
		MS_PROT_K_DATA_K_OBJ		=>	$o, 
		MS_PROT_K_DATA_K_OTYPE		=>	$ot, 
		MS_PROT_K_DATA_K_OMOBILE	=>	$omobile, 
		MS_PROT_K_DATA_K_MSG		=>	$msg, 
		MS_PROT_K_DATA_K_SNUMBER	=>	$snumber, 
		MS_PROT_K_DATA_K_SENDTIME	=>	$sendtime, 
		MS_PROT_K_DATA_K_CALLBACK	=>	$callback, 
		MS_PROT_K_DATA_K_PRIORITY	=>	$priority, 
	);
	return json_encode($data);
}
/**
 * 组织POST数据
 * @param mixed $version 		协议版本
 * @param mixed $secret_key 	密码
 * @param mixed $data 			请求数据
 * @return string				HTTP的POST数据
 */
function ms_get_postdata($version, $secret_key, $data) {
	$postdata = array(
		MS_PROT_K_VERSION		=> $version, 
		MS_PROT_K_DATA			=> $data, 
		MS_PROT_K_SIG			=> md5($data . $secret_key), 
	);
	return http_build_query($postdata);
}
/**
 * 组织HTTP头
 * @param mixed $host 				主机名
 * @param mixed $path 				路径
 * @param mixed $content_length 	内容长度
 * @return string					HTTP的头
 */
function ms_get_header($host, $path, $content_length) {
	$header	= HTTP_REQUEST_HEADER;
	$header = str_replace(array(PLACEHOLDER_PATH, PLACEHOLDER_HOST, PLACEHOLDER_CONTENT_LENGTH), array($path, $host, $content_length), $header);
	return $header;
}
/**
 * 组织HTTP体
 * @param mixed $content 	内容
 * @return string			HTTP的body
 */
function ms_get_body($content) {
	return str_replace(PLACEHOLDER_CONTENT, $content, HTTP_REQUEST_BODY);
}
/**
 * 解析响应
 * @param mixed $response 	响应字符串
 * @return array			包含响应头和响应体的字符串数组
 */
function ms_parse_response($response) {
	list($header, $body) = explode(CRLF . CRLF, $response, 2);
	return array($header, $body);
}
/**
 * 检查响应是否正确
 * @param mixed $header 	响应头
 * @param mixed $body 		响应体
 * @param mixed $errno 		用来回写的错误号
 * @return bool				响应是否是成功的
 */
function ms_check_response($header, $body, &$errno) {
	if(!preg_match('/^HTTP\/1\.\d\s2/', $header)) {
		$errno	= LOG_NO_RESPONSE_ERROR;
		return false;
	} else if(!preg_match('/result=0\D/', $body)) {
		$errno	= LOG_NO_SEND_ERROR;
		return false;
	} else {
		return true;
	}
}
/**
 * 关闭fs, 返回false, 记录日志的便利函数
 * @param mixed $fs 		fsock句柄
 * @param mixed $logno 		日志编号
 * @param mixed ...			日志内容
 * @return bool				false
 */
function fs_false_log($fs, $logno) {
	fclose($fs);
	$args	= func_get_args();
	$args	= array_slice($args, 2);
	$log	= '';
	foreach($args as $msg) {
		if(!is_string($msg))
			$msg = json_encode($msg);
		$log .= ' ' . $msg;
	}
	//error_log(date('H:i:s') . ' ' . $logno . ' ' . $log . "\n", 3, MS_CLIENT_LOG_PATH . MS_CLIENT_LOG_FILE_PREFIX . date(MS_CLIENT_LOG_FILE_TIME_FMT));
	return false;
}
/**
 * 关闭fs, 返回true, 记录日志的便利函数
 * @param mixed $fs 		fsock句柄
 * @param mixed $logno 		日志编号
 * @param mixed ...			日志内容
 * @return bool				true
 */
function fs_true_log($fs, $logno) {
	fclose($fs);
	$args	= func_get_args();
	$args	= array_slice($args, 2);
	$log	= '';
	foreach($args as $msg) {
		if(!is_string($msg))
			$msg = json_encode($msg);
		$log .= ' ' . $msg;
	}
	//error_log(date('H:i:s') . ' ' . $logno . ' ' . $log . "\n", 3, MS_CLIENT_LOG_PATH . MS_CLIENT_LOG_FILE_PREFIX . date(MS_CLIENT_LOG_FILE_TIME_FMT));
	return true;
}
/**
 * 返回false的便利日志函数
 * @param mixed $logno 		日志号
 * @param mixed ...			日志内容
 * @return bool				false
 */
function false_log($logno) {
	$args	= func_get_args();
	$args	= array_slice($args, 1);
	$log	= '';
	foreach($args as $msg) {
		if(!is_string($msg))
			$msg = json_encode($msg);
		$log .= ' ' . $msg;
	}
	error_log(date('H:i:s') . ' ' . $logno . ' ' . $log . "\n", 3, MS_CLIENT_LOG_PATH . MS_CLIENT_LOG_FILE_PREFIX . date(MS_CLIENT_LOG_FILE_TIME_FMT));
	return false;
}
