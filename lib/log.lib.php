<?php
/*
 +----------------------------------------------------------------------+
 | api.zhisland.com                                                     |
 +----------------------------------------------------------------------+
 | author: 雷果国(leiguoguo@zhisland.com)                               |
 +----------------------------------------------------------------------+
 | api 统一输出                                                         |
 +----------------------------------------------------------------------+
 */

date_default_timezone_set('Asia/Chongqing');
#定义日志默认信息
if ( !defined('LOG_DEFAULT_PATH') ) define('LOG_DEFAULT_PATH', dirname(dirname(__FILE__)) . '/log');
if ( !defined('LOG_BACKUP_PATH') ) define('LOG_BACKUP_PATH', dirname(dirname(__FILE__)) . '/log/backup');
if ( !defined('LOG_DELAY_WRITE') ) define('LOG_DELAY_WRITE', FALSE);
define('LOG_FILENAME_PREFIX',				'log_');
define('LOG_FILENAME_TIME_FMT',				'Y-m-d-H');
define('LOG_TIME_FMT',						'Y-m-d H:i:s');
define('LOG_TIME_PATTERN',					'\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}');
define('LOG_SEPARATE',						' ');
define('LOG_NEWLINE_PATTERN',				'/\n/');
define('LOG_NEWLINE_REPLACEMENT',			'~|__|~');
define('LOG_NEWLINE_REVERT_PATTERN',		'/~|_|~/');
define('LOG_NEWLINE_REVERT_REPLACEMENT',	"\n");
define('LOG_NEWLINE',						"\n");

#定义日志级别相关
if ( !defined('LOG_LEVEL') ) define('LOG_LEVEL', 0xFF);	#日志级别
define('LOG_LEVEL_INFO',					0x01);
define('LOG_LEVEL_WARNING',					0x02);
define('LOG_LEVEL_DEBUG',					0x04);
define('LOG_LEVEL_ERROR',					0x08);
define('LOG_LEVEL_BAKUP',					0x10);
define('LOG_LEVELS',						'log_levels');
$GLOBALS[LOG_LEVELS]	= array(
	LOG_LEVEL_INFO		=> 'info', 
	LOG_LEVEL_WARNING	=> 'warning', 
	LOG_LEVEL_DEBUG		=> 'debug', 
	LOG_LEVEL_ERROR		=> 'error', 
	LOG_LEVEL_BAKUP		=> 'backup', 
);

#真实写文件的入口, 这里实现延迟写
function log_real_write($file = NULL, $msg = NULL) {
	#非延迟写
	if ( !LOG_DELAY_WRITE ) return error_log($msg, 3, $file);
	#延迟写
	static $msgs	= array();
	if (  func_num_args() == 2 ) {
		if ( !array_key_exists($file, $msgs) ) $msgs[$file] = '';
		$msgs[$file]	.= $msg;
	} else if ( func_num_args() == 1 ) {
		if ( array_key_exists($file, $msgs) ) {
			error_log($msgs[$file], 3, $file);
			unset($msgs[$file]);
		}
	} else if ( func_num_args() == 0 ) {
		foreach ( $msgs as $file => $msg ) error_log($msg, 3, $file);
		$msgs	= array();
	}
}
#日志基础函数
function log_write($log_level, $log_no, $log_msg, $log_filepath = LOG_DEFAULT_PATH) {
	if(LOG_LEVEL & $log_level) {
		if(!is_string($log_msg)) 
			$log_msg	= serialize($log_msg);
		$log_msg	= preg_replace(LOG_NEWLINE_PATTERN, LOG_NEWLINE_REPLACEMENT, $log_msg);
		$now		= time();
		$log_time	= date(LOG_TIME_FMT, $now);
		$file_time	= date(LOG_FILENAME_TIME_FMT, $now);
		$file_name	= $log_filepath . DIRECTORY_SEPARATOR . LOG_FILENAME_PREFIX . $file_time;
		$log_level	= $GLOBALS[LOG_LEVELS][$log_level];
		$log_msg	= sprintf('%s' . LOG_SEPARATE . '%10s' . LOG_SEPARATE . '%08d' . LOG_SEPARATE . '%s' . LOG_NEWLINE, 
							$log_time, $log_level, $log_no, $log_msg);
		log_real_write($file_name, $log_msg);
	}
}
function log_info($log_no, $log_msg) {
	log_write(LOG_LEVEL_INFO, $log_no, $log_msg);
}
function log_warning($log_no, $log_msg) {
	log_write(LOG_LEVEL_WARNING, $log_no, $log_msg);
}
function log_debug($log_no, $log_msg) {
	log_write(LOG_LEVEL_DEBUG, $log_no, $log_msg);
}
function log_error($log_no, $log_msg) {
	log_write(LOG_LEVEL_ERROR, $log_no, $log_msg);
}
function log_backup($log_no, $log_msg) {
	if(LOG_LEVEL & LOG_LEVEL_BAKUP) {
		$log_msg	= json_encode($log_msg);
		$log_msg	= gzdeflate($log_msg);
		$now		= time();
		$log_time	= date(LOG_TIME_FMT, $now);
		$file_time	= date(LOG_FILENAME_TIME_FMT, $now);
		$file_name	= LOG_BACKUP_PATH . DIRECTORY_SEPARATOR . LOG_FILENAME_PREFIX . $file_time;
		$log_level	= $GLOBALS[LOG_LEVELS][LOG_LEVEL_BAKUP];
		$log_msg	= sprintf('%s' . LOG_SEPARATE . '%10s' . LOG_SEPARATE . '%08d' . LOG_NEWLINE . '%s' . LOG_NEWLINE . "\r\n\r\n", 
							$log_time, $log_level, $log_no, $log_msg);
		$fp			= fopen($file_name, 'ab');
		fwrite($fp, $log_msg);
		fclose($fp);
	}
}
function log_restore($log_file, $process, $assoc = true) {
	$fp			= fopen($log_file, 'rb');
	while($line1 = fgets($fp)) {
		preg_match('/^(' . LOG_TIME_PATTERN . ')\s+(\w+)\s+(\d+)/', $line1, $matches);
		$line2	= '';
		while($buff = fgets($fp)) {
			if($buff == "\r\n" && $prev == "\r\n") {
				$line2 = substr($line2, 0, strlen($line2) - 2);
				break;
			} else {
				$line2 .= $buff;
			}
			$prev = $buff;
		}
		$data	= json_decode(gzinflate($line2), $assoc);
		$process($matches[1], $matches[2], $matches[3], $data);
	}
}
