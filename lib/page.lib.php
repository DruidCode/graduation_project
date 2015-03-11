<?php
/*
 +----------------------------------------------------------------------+
 | mini-system 0.3                                                      |
 +----------------------------------------------------------------------+
 | author: 雷果国(leiguoguo@zhisland.com)                               |
 +----------------------------------------------------------------------+
 | mini-system 分页基础计算                                             |
 +----------------------------------------------------------------------+
 */
define('PAGE_FIRST',						'f');
define('PAGE_LAST',							'l');
define('PING_FIRST',						'f');
define('PING_LAST',							'l');

/**
 * page_correct 
 * 页码修正
 * @param mixed $page 		#请求页码: PAGE_FIRST(首页), PAGE_LAST(尾页), 正数和0(正常页码), 负数(倒数页码, -1表示最后一页)
 * @param mixed $page_count #总页数
 */
function page_correct($page, $page_count) {
	if ( $page == PAGE_FIRST ) $page = 1;
	else if ( $page == PAGE_LAST ) $page = $page_count;
	else if ( $page < 0 ) $page = $page_count + $page + 1;
	return min($page_count, max(1, $page));
}
#页码分段数修正, 算法同page_correct
function ping_correct($ping, $ping_count) {
	if ( $ping == PING_FIRST ) $ping = 1;
	else if ( $ping == PING_LAST ) $ping = $ping_count;
	else if ( $ping < 0 ) $ping = $ping_count + $ping + 1;
	return min($ping_count, max(1, $ping));
}
/**
 * ping_to_page 
 * 分段页码转换为普通页码
 * @param mixed $ping_page 	请求页码
 * @param mixed $ping 		请求段号
 * @param mixed $ping_count 每页段数
 * @param mixed $total_ping 总段数
 */
function ping_to_page($ping_page, $ping, $ping_count, $total_ping) {
	$total_ping_page	= ceil($total_ping / $ping_count);
	$ping_page			= page_correct($ping_page, $total_ping_page);
	$page_ping			= min($ping_count, $total_ping - ($ping_page - 1) * $ping_count);
	$ping				= ping_correct($ping, $page_ping);
	return ($ping_page - 1) * $ping_count + $ping;
}
/**
 * page_to_ping 
 * 普通分页到分段页码转换
 * @param mixed $page 		#页码(只接受修正后的正整形页码)
 * @param mixed $ping_count #每页分段数
 */
function page_to_ping($page, $ping_count) {
	#强制只接收修正后的整型页码
	$page		= abs(intval($page));
	$ping		= ($page - 1) % $ping_count + 1;
	$ping_page	= ceil($page / $ping_count);
	return array($ping_page, $ping);
}



//////////////////////////xp////////////////////////////////////////////////////


function ext_correct($ext_correct = NULL, $min = 0, $max = 0) {
	if($ext_correct){
		if($min && $max)
			if($min == $max)
				return " $ext_correct = $max ";
			else
				return " $ext_correct > $min and $ext_correct < $max ";
		elseif($min)
			return " $ext_correct > $min ";
		elseif($max){
			return " $ext_correct < $max ";
        }
	}
	return '';
}

