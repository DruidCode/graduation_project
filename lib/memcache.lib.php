<?php
/*
 +----------------------------------------------------------------------+
 | mini-system 0.3                                                      |
 +----------------------------------------------------------------------+
 | author: 雷果国(leiguoguo@zhisland.com)                               |
 +----------------------------------------------------------------------+
 | mini-system memcache访问库                                           |
 +----------------------------------------------------------------------+
 */

#定义MC相关信息
define('MCS_CFG_K_HOST',						'host');				#配置项中的主机地址
define('MCS_CFG_K_PORT',						'port');				#配置项中的服务端端口
define('MCS_CFG_K_WEIGHT',						'weight');				#配置项中的权重
define('MCS_SEP_SERVERS',						';');					#字符串配置方式中的服务器分隔符
define('MCS_SEP_HOST_PORT',						':');					#字符串配置方式中的主机/端口/权重分隔符

define('MCS_LOCK_KEY_PREFIX',					'__mcs_lock_key_');		#锁前缀

define('MCS_DEFAULT_PORT',						11211);					#默认端口
define('MCS_DEFAULT_WEIGHT',					1);						#默认权重
define('MCS_DEFAULT_PERSIST',					true);					#默认是否持久连接
define('MCS_DEFAULT_TIMEOUT',					1);						#默认连接超时时间
define('MCS_DEFAULT_RETRY_INTERVAL',			15);					#服务端故障重连时间
define('MCS_DEFAULT_STATUS',					true);					#是否为不影响key分布将故障机标记存活
define('MCS_DEFAULT_FAILURE_CALLBACK',			'mc_failure_callback');	#故障回调
define('MCS_DEFAULT_LOCK_TIMEOUT',				3);						#加锁超时

define('MCS_DEFAULT_EXPIRE',					24*60*60);						#默认过期时间
define('MCS_DEFAULT_FLAG',						0);						#默认flag
define('MCS_DEFAULT_INCREMENT_VALUE',			1);						#默认自增单位
define('MCS_DEFAULT_DECREMENT_VALUE',			1);						#默认自减单位
define('MCS_DEFAULT_DEL_TIMEOUT',				0);						#默认删除延时

define('MCS_CFG_PATTERN_CLUSTER',				'/^\w+$/');
define('MCS_CFG_PATTERN_STR',					'/(?>\s*)(?>\d{1,3})(?>\.\d{1,3}){3}(:(?>\d{0,5})(:(?>\d*))?)?/');

#解析配置
function mc_servers_decode($servers) {
	$outputs	= array();
	if ( preg_match(MCS_CFG_PATTERN_STR, $servers) ) 
		$servers = explode(MCS_SEP_SERVERS, $servers);
	else if ( preg_match(MCS_CFG_PATTERN_CLUSTER, $servers) ) 
		$servers = $GLOBALS['CONFIG_MEMCACHE'][$servers];
	else 
		trigger_error('unknown memcache server configuration formate', E_USER_ERROR);
	foreach ( $servers as $server ) {
		if ( !trim($server) ) continue;
		list($ip, $port, $weight) = explode(MCS_SEP_HOST_PORT, $server);
		if ( !$ip ) continue;
		if ( !$port ) $port = MCS_DEFAULT_PORT;
		if ( !$weight ) $weight = MCS_DEFAULT_WEIGHT;
		$outputs[]	= array(MCS_CFG_K_HOST => $ip, MCS_CFG_K_PORT => $port, MCS_CFG_K_WEIGHT => $weight);
	}
	return $outputs;
}
#实例化
function mc_instance($servers) {
	if ( is_string($servers) ) $servers	= mc_servers_decode($servers);
	if ( !is_array($servers) || empty($servers) ) return false;
	$mc			= new Memcache;
	foreach ( $servers as $server ) {
		if ( !$server[MCS_CFG_K_PORT] ) $server[MCS_CFG_K_PORT] = MCS_DEFAULT_PORT;
		if ( !$server[MCS_CFG_K_WEIGHT] ) $server[MCS_CFG_K_WEIGHT] = MCS_DEFAULT_WEIGHT;
		$mc->addServer($server[MCS_CFG_K_HOST], $server[MCS_CFG_K_PORT], MCS_DEFAULT_PERSIST, $server[MCS_CFG_K_WEIGHT], 
			MCS_DEFAULT_TIMEOUT, MCS_DEFAULT_RETRY_INTERVAL, MCS_DEFAULT_STATUS, MCS_DEFAULT_FAILURE_CALLBACK);
	}
	$mc->setCompressThreshold(5000);
	return $mc;
}
#失败回调
function mc_failure_callback($ip, $port){
	error_log( 'connect memcache fail: '.$ip.':'.$port );
}
#读取
function mc_get($mc, $key) {
	return $mc->get($key);
}
#增加数据
function mc_add($mc, $key, $value, $flag = MCS_DEFAULT_FLAG, $expire = MCS_DEFAULT_EXPIRE) {
	return $mc->add($key, $value, $flag, $etime);
}
#自减
function mc_dec($mc, $key, $value = MCS_DEFAULT_DECREMENT_VALUE){
	return $mc->decrement($key, $value);
}
#自增
function mc_inc($mc, $key, $value = MCS_DEFAULT_INCREMENT_VALUE){
	return $mc->increment($key, $value);
}
#替换
function mc_replace($mc, $key, $value, $flag = MCS_DEFAULT_FLAG, $expire = MCS_DEFAULT_EXPIRE) {
	return $mc->replace($key, $value, $flag, $expire);
}
#写入
function mc_set($mc, $key, $value, $flag = MCS_DEFAULT_FLAG, $expire = MCS_DEFAULT_EXPIRE) {
	return $mc->set($key, $value, $flag, $expire);
}
#删除
function mc_del($mc, $key, $timeout = MCS_DEFAULT_DEL_TIMEOUT) {
	if ( is_array($key) ) 
		foreach ( $key as $k ) $mc->delete($k, $timeout);
	else $mc->delete($key, $timeout);
}
#获取锁key
function mc_lock_key($key) {
	return MCS_LOCK_KEY_PREFIX . $key;
}
#锁
function mc_lock($mc, $key, $expire = MCS_DEFAULT_EXPIRE, $timeout = MCS_DEFAULT_LOCK_TIMEOUT) {
	$begin		= microtime(TRUE);
	$now		= $begin;
	$used		= $now - $begin;
	$lock_key	= mc_lock_key($key);
	$usleep		= 1;
	while ( microtime(TRUE) - $begin < $timeout  ) {
		if ( mc_add($mc, $key, 1, MCS_DEFAULT_FLAG, $expire) ) return TRUE;
		else usleep($usleep *= 1.2);
	}
	return FALSE;
}
#解锁
function mc_unlock($mc, $key) {
	$lock_key	= mc_lock_key($key);
	return mc_del($mc, $lock_key);
}
