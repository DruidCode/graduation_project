<?php
/*
 +----------------------------------------------------------------------+
 | mini-system 0.3                                                      |
 +----------------------------------------------------------------------+
 | author: 雷果国(leiguoguo@zhisland.com)                               |
 +----------------------------------------------------------------------+
 | mini-system mysql基础库                                              |
 +----------------------------------------------------------------------+
 */
ms_load_lib('page');
ms_load_common('common');

define('MYSQL_CFG_K_HOST',						'host');
define('MYSQL_CFG_K_PORT',						'port');
define('MYSQL_CFG_K_USERNAME',					'username');
define('MYSQL_CFG_K_PASSWORD',					'password');
define('MYSQL_CFG_K_DBNAME',					'dbname');
define('MYSQL_CFG_K_CHARSET',					'charset');

define('PAGINATE_COUNT_ALIAS',					'count');
define('PAGINATE_COUNT_SQL_PATTERN',			'/(?<=(?<!`|\'|")SELECT).*(?=(?<!`|\'|")FROM\s)/i');
define('PAGINATE_COUNT_SQL_REPLACEMENT',		' count(*) AS ' . PAGINATE_COUNT_ALIAS . ' ');
define('PAGINATE_WHERE_SQL_PATTERN',            '/where/i');

define('PAGINATE_CONDITION_GROUP_SQL_PATTERN',			'/(group\s+by)/i');
define('PAGINATE_CONDITION_ORDER_SQL_PATTERN',			'/(order\s+by)/i');
define('PAGINATE_CONDITION_SQL_REPLACEMENT',			'\1');

define('STRIP_HAVING_PATTERN',							'/HAVING.*?(?=\bLIMIT\b|$)/i');

define('PAGINATE_FIRST',						'f');
define('PAGINATE_LAST',							'l');

define('MYSQL_SAFE_PLACEHOLDER',				'?');
define('MYSQL_SAFE_SIG',						'SAFE:');
define('MYSQL_SAFE_SIGLEN',						5);
define('MYSQL_SAFE_INT',						'i');			#整型
define('MYSQL_SAFE_STRING',						's');			#字符串
define('MYSQL_SAFE_DOUBLE',						'd');			#浮点型
define('MYSQL_SAFE_BLOB',						'b');			#二进制
define('MYSQL_SAFE_LITERAL',					'l');			#字面量
define('MYSQL_SAFE_FIELD',						'f');			#字段名
define('MYSQL_SAFE_ALIAS',						'a');			#字段名+别名(数组方式传值,第一个字段名, 第二个别名)
define('MYSQL_SAFE_INT_ARRAY',					'I');			#整型数组
define('MYSQL_SAFE_STRING_ARRAY',				'S');			#字符串数组
define('MYSQL_SAFE_DOUBLE_ARRAY',				'D');			#浮点型数组
define('MYSQL_SAFE_BLOB_ARRAY',					'B');			#二进制数组
define('MYSQL_SAFE_LITERAL_ARRAY',				'L');			#字面量数组
define('MYSQL_SAFE_FIELD_ARRAY',				'F');			#字段名数组
define('MYSQL_SAFE_ALIAS_ARRAY',				'A');			#字段名+别名数组

define('E_MYSQL_CONNECT_FAILURE',				-1001);
define('EM_MYSQL_CONNECT_FAILURE',				'Mysql数据库连接失败[%s:%s]');
define('E_MYSQL_UNSAFE_SQL',					'请不要执行未经过安全处理的SQL[%s]');
define('E_MYSQL_BIND_INCORRECT',				'绑定参数不完整');
define('E_MYSQL_BIND_UNSUPPORT',				'绑定类型不支持: %s in %s, offset: %s');

#mysql库公共错误处理
function mysql_error_handler($conn) {
	$errno	= mysql_errno($conn);
	$errstr	= mysql_error($conn);
	if ( $errno ) ms_plugin_install('mysql.error', $errno, $errstr);
	return $errno;
}
#初始化mysql连接
function mysql_init($server_cfg) {
	static $conns	= array();
	$key			= md5(serialize($server_cfg));
	if ( array_key_exists($key, $conns) && is_resource($conns[$key]) ) {
		if ( mysql_ping($conns[$key]) ) {
			return $conns[$key];
		} else {
			mysql_close($conns[$key]);
			unset($conns[$key]);
		}
	}
	$host		= $server_cfg[MYSQL_CFG_K_HOST];
	$port		= $server_cfg[MYSQL_CFG_K_PORT];
	$username	= $server_cfg[MYSQL_CFG_K_USERNAME];
	$password	= $server_cfg[MYSQL_CFG_K_PASSWORD];
	$conn		= mysql_connect($host . ':' . $port, $username, $password);
	if ( !is_resource($conn) ) ms_plugin_install('mysql.error', E_MYSQL_CONNECT_FAILURE, sprintf(EM_MYSQL_CONNECT_FAILURE, $host, $port));
	if ( array_key_exists(MYSQL_CFG_K_DBNAME, $server_cfg) ) mysql_switch_db($conn, $server_cfg[MYSQL_CFG_K_DBNAME]);
	if ( array_key_exists(MYSQL_CFG_K_CHARSET, $server_cfg) ) mysql_switch_charset($conn, $server_cfg[MYSQL_CFG_K_CHARSET]);
	$conns[$key]	= $conn;
	return $conns[$key];
}
#切换连接数据库
function mysql_switch_db($conn, $dbname) {
	mysql_select_db($dbname, $conn);
	mysql_error_handler($conn);
}
#切换字符集
function mysql_switch_charset($conn, $charset) {
	mysql_set_charset($charset, $conn);
	mysql_error_handler($conn);
}
#查询并获取结果集
function mysql_get_resultset($conn, $sql) {
	#安全签名检查
	mysql_safe_check($sql);
	#安全签名解除
	$sql	= mysql_safe_unsig($sql);
	$rs		= mysql_query($sql, $conn);
	mysql_error_handler($conn);
	return $rs;
}
#执行SQL, 只关心结果是否成功
function mysql_execute($conn, $sql) {
	#安全签名检查
	mysql_safe_check($sql);
	#安全签名解除
	$sql	= mysql_safe_unsig($sql);
	mysql_query($sql, $conn);
	return mysql_error_handler($conn) == 0;
}
#安全sql签名
function mysql_safe_sig($sql) {
	if ( !mysql_is_safe($sql) ) $sql = MYSQL_SAFE_SIG . $sql;
	return $sql;
}
#安全sql签名解除
function mysql_safe_unsig($sql) {
	if ( mysql_is_safe($sql) ) $sql = substr($sql, MYSQL_SAFE_SIGLEN);
	return $sql;
}
#检查sql是否是安全签名的
function mysql_is_safe($sql) {
	return strpos($sql, MYSQL_SAFE_SIG) === 0;
}
#安全检查
function mysql_safe_check($sql) {
	if ( !mysql_is_safe($sql) ) ms_trigger_error(E_USER_ERROR, E_MYSQL_UNSAFE_SQL, $sql);
}
#转换获取安全的SQL
function mysql_safe_sql($conn, $sql, $types = NULL) {
	if ( func_num_args() == 2 ) return mysql_safe_sig($sql);
	$args			= func_get_args();
	$args			= array_values(array_slice($args, 3));
	$replacements	= mysql_safe_args($conn, $types, $args);
	$sql			= mysql_safe_replacements($sql, $replacements);
	return mysql_safe_sig($sql);
}
#全部替换占位符
function mysql_safe_replacements($sql, $replacements) {
	$offset	= 0;
	foreach ( $replacements as $replacement ) 
		$sql	= mysql_safe_replacement($sql, $replacement, $offset);
	return $sql;
}
#替换单个占位符
function mysql_safe_replacement($sql, $replacement, &$offset) {
	$offset	= strpos($sql, MYSQL_SAFE_PLACEHOLDER, $offset);
	$sql	= substr_replace($sql, $replacement, $offset, 1);
	$offset	= $offset + strlen($replacement) - 1;
	return $sql;
}
#安全处理参数
function mysql_safe_args($conn, $types, $args) {
	if ( !is_string($types) || !is_array($args) )
		return ms_trigger_error(E_USER_ERROR, E_MYSQL_BIND_INCORRECT);
	$types_array	= str_split($types);;
	$replacements	= array();
	$length			= count($args);
	foreach ( $types_array as $index => $type ) {
		if ( $index >= $length ) break;
		$value	= $args[$index];
		switch ( $type ) {
			case MYSQL_SAFE_INT:
				$value	= mysql_safe_int($value, $conn);
				break;
			case MYSQL_SAFE_DOUBLE:
				$value	= mysql_safe_float($value, $conn);
				break;
			case MYSQL_SAFE_STRING:
			case MYSQL_SAFE_BLOB:
				$value	= mysql_safe_string_quote($value, $conn, '\'');
				break;
			case MYSQL_SAFE_LITERAL:
				$value	= mysql_safe_string_literal($value, $conn);
				break;
			case MYSQL_SAFE_FIELD:
				$value	= mysql_safe_field($value, $conn);
				break;
			case MYSQL_SAFE_ALIAS:
				$value	= mysql_safe_alias($value, $conn);
				break;
			case MYSQL_SAFE_INT_ARRAY:
				$value	= mysql_safe_array('int', $value, $conn);
				break;
			case MYSQL_SAFE_DOUBLE_ARRAY:
				$value	= mysql_safe_array('float', $value, $conn);
				break;
			case MYSQL_SAFE_STRING_ARRAY:
			case MYSQL_SAFE_BLOB_ARRAY:
				$value	= mysql_safe_array('string_quote', $value, $conn, '\'');
				break;
			case MYSQL_SAFE_LITERAL_ARRAY:
				$value	= mysql_safe_array('string_literal', $value, $conn);
				break;
			case MYSQL_SAFE_FIELD_ARRAY:
				$value	= mysql_safe_array('field', $value, $conn);
				break;
			case MYSQL_SAFE_ALIAS_ARRAY:
				$value	= mysql_safe_array('alias', $value, $conn);
				break;
			default:
				ms_trigger_error(E_USER_ERROR, E_MYSQL_BIND_UNSUPPORT, $types, $type, $index);
				break;
		}
		$replacements[$index]	= $value;
	}
	return $replacements;
}
#字符串字面量处理
function mysql_safe_string_literal($value, $conn) {
	return mysql_real_escape_string(strval($value), $conn);
}
#带引号字符串处理
function mysql_safe_string_quote($value, $conn, $quote = '"') {
	return $quote . mysql_safe_string_literal($value, $conn) . $quote;
}
#字段名字符串处理
function mysql_safe_field($value, $conn) {
	$components	= explode('.', $value);
	array_walk($components, 'array_walk_call', 'trim');
	foreach ( $components as &$component ) 
		$component	= mysql_safe_string_quote($component, $conn, '`');
	return implode('.', $components);
}
#别名处理
function mysql_safe_alias($value, $conn) {
	return mysql_safe_field($value[0], $conn) . ' AS ' . mysql_safe_field($value[1], $conn);
}
#整型处理
function mysql_safe_int($value, $conn) {
	return intval($value);
}
#浮点数处理
function mysql_safe_float($value, $conn) {
	return floatval($value);
}
#数组类型处理
function mysql_safe_array($type, $values, $conn) {
	$args	= func_get_args();
	$args	= array_values(array_slice($args, 1));
	$func	= 'mysql_safe_' . $type;
	$eles	= array();
	foreach ( $values as $value ) {
		$args[0]	= $value;
		array_push($eles, call_user_func_array($func, $args));
	}
	return implode(',', $eles);
}
#数组形式字符串安全处理
function mysql_escape_string_array($conn, &$args, $quote = '\'') {
	foreach ( $args as $index => &$arg ) 
		$arg	= $quote . mysql_real_escape_string(strval($arg), $conn) . $quote;
}
#别名数组的安全处理
function mysql_escape_alias_array($conn, &$args) {
	foreach ( $args as $index => &$arg ) 
		$arg	= '`' . mysql_real_escape_string(strval($arg[0]), $conn) . '`' 
				. ' AS ' 
				. '`' . mysql_real_escape_string(strval($arg[1]), $conn) . '`';
}
#返回符合条件的第一条.同时获取字段位置和字段名作为key的数组结果
function mysql_getb($conn, $sql, &$row_num = NULL) {
	$rs		= mysql_get_resultset($conn, $sql);
	$row	= mysql_fetch_array($rs, MYSQL_BOTH);
	if ( func_num_args() == 3 ) $row_num = mysql_num_rows($rs);
	mysql_free_result($rs);
	return $row;
}
#返回符合条件的所有.同时获取字段位置和字段名作为key的数组结果
function mysql_getb_all($conn, $sql, &$row_num = NULL) {
	$rows	= array();
	$rs		= mysql_get_resultset($conn, $sql);
	while ( $row = mysql_fetch_array($rs, MYSQL_BOTH) ) $rows[]	= $row;
	if ( func_num_args() == 3 ) $row_num = mysql_num_rows($rs);
	mysql_free_result($rs);
	return $rows;
}
#返回符合条件的第一条.只获取字段位置作为key的数组结果
function mysql_getn($conn, $sql, &$row_num = NULL) {
	$rs		= mysql_get_resultset($conn, $sql);
	$row	= mysql_fetch_array($rs, MYSQL_NUM);
	if ( func_num_args() == 3 ) $row_num = mysql_num_rows($rs);
	mysql_free_result($rs);
	return $row;
}
#返回符合条件的所有.同时获取字段位置作为key的数组结果
function mysql_getn_all($conn, $sql, &$row_num = NULL) {
	$rows	= array();
	$rs		= mysql_get_resultset($conn, $sql);
	while ( $row = mysql_fetch_array($rs, MYSQL_NUM) ) $rows[]	= $row;
	if ( func_num_args() == 3 ) $row_num = mysql_num_rows($rs);
	mysql_free_result($rs);
	return $rows;
}
#返回符合条件的第一条.只获取字段名作为key的数组结果
function mysql_geta($conn, $sql, &$row_num = NULL) {
	$rs		= mysql_get_resultset($conn, $sql);
	$row	= mysql_fetch_array($rs, MYSQL_ASSOC);
	if ( func_num_args() == 3 ) $row_num = mysql_num_rows($rs);
	mysql_free_result($rs);
	return $row;
}
#返回符合条件的所有.只获取字段名作为key的数组结果
function mysql_geta_all($conn, $sql, &$row_num = NULL) {
	$rows	= array();
	$rs		= mysql_get_resultset($conn, $sql);
	while ( $row = mysql_fetch_array($rs, MYSQL_ASSOC) ) $rows[]	= $row;
	if ( func_num_args() == 3 ) $row_num = mysql_num_rows($rs);
	mysql_free_result($rs);
	return $rows;
}
#查询给定sql结果集数量
function mysql_count($conn, $sql) {
	$count_sql		= preg_replace(PAGINATE_COUNT_SQL_PATTERN, PAGINATE_COUNT_SQL_REPLACEMENT, $sql);
	$count_sql		= preg_replace(STRIP_HAVING_PATTERN, '', $count_sql);
	$count_ret		= mysql_geta($conn, $count_sql);
	return intval($count_ret[PAGINATE_COUNT_ALIAS]);
}
function mysql_count_raw($conn, $sql) {
	$count_ret		= mysql_geta($conn, $sql);
	return intval($count_ret[PAGINATE_COUNT_ALIAS]);
}
/**
 * mysql_paginate_limit 
 * 根据分页参数重构sql语句
 * @param mixed $sql 			#要重构的SQL语句(引用参数)
 * @param mixed $page_size 		#期望的每页记录数
 * @param mixed $page 			#请求的页码, 可以是数字页码, PAGINATE_FIRST(首页), PAGINATE_LAST(尾页), 返回修正后的页码
 * @param mixed $total_record 	#总记录数, 对于无法自动查询的SQL, 可以自己传入记录数, 返回最终使用的总记录数
 * @return 修正后的SQL语句
 */
function mysql_paginate_limit($conn, &$sql, $page_size, &$page = NULL, &$total_record = NULL,
										$ext_condition = null, $min = 0, $max = 0,
										$having_ext_condition = null, $hmin = 0, $hmax = 0) {
	$argc		= func_num_args();
	$page_size	= intval($page_size);
	#不关注页数
	if ( $argc == 3 ) {
		$limit	= ' LIMIT ' . $page_size;
	#关注页数
	} else {
		#未指定总记录数则替换SQL查询记录数
		if ( !is_int($total_record) ) $total_record = mysql_count($conn, $sql);
		#计算总页数
		$total_page			= ceil($total_record / $page_size);
		$total_page			= $total_page ? $total_page : 1;
		#页码修正
		$page				= page_correct($page, $total_page);
		#计算偏移量
		$offset	= ($page - 1) * $page_size;
		$limit	= " LIMIT $offset, $page_size";
		

		/*
		 * 处理有附加条件的情况
		 * 为客户段这种需要传入min和max的情况使用
		 */
		if($ext_condition){
			$ext_condition_sql = ext_correct($ext_condition, $min, $max);
			$sql = mysql_conditon_append($sql, $ext_condition_sql);
		}
		/*
		 * having的情况 仅仅是message情况用到
		 */
		if($having_ext_condition){
			$ext_condition_sql = ext_correct($having_ext_condition, $hmin, $hmax);
			$sql = mysql_conditon_append_having($sql, $ext_condition_sql);
		}
	}
	$sql = $sql . $limit;
    return $sql;
}
#取前$count条
function mysql_top_paginate_sql($conn, $sql, $count) {
	return sprintf('%s LIMIT %d', $sql, $count);
}
#普通分页语句构建
function mysql_normal_paginate_sql($conn, $sql, $page_size, &$page, &$total_record = NULL) {
	if ( !is_int($total_record) ) 
		$total_record	= mysql_count($conn, $sql);
	$total_page		= ceil($total_record / $page_size);
	$total_page		= $total_page ? $total_page : 1;
	$page			= page_correct($page, $total_page);
	$offset			= ($page - 1) * $page_size;
	return sprintf('%s LIMIT %d, %d', $sql, $offset, $page_size);
}
#线性分页语句构造
function mysql_line_paginate_sql($conn, $sql, $count, $type, $field, $min = NULL, $max = NULL) {
	$min	= intval($min);
	$max	= intval($max);
	if ( $min <= 0 && $max <= 0 ) return $sql;
	$ext_condition_sql	= ext_correct($field, $min, $max);
	switch ( $type ) {
		case PAGINATE_LINE_TYPE_WHERE:
			$sql	= mysql_conditon_append($sql, $ext_condition_sql);
			break;
		case PAGINATE_LINE_TYPE_HAVING:
			$sql	= mysql_conditon_append_having($sql, $ext_condition_sql);
			break;
		default:
			ms_trigger_error(E_USER_ERROR, E_MYSQL_PAGINATE_LINE_TYPE);
			break;
	}
	return mysql_top_paginate_sql($conn, $sql, $count);
}
#偏移量类缓存
function mysql_offset_paginate_sql($conn, $sql, $count, &$offset = NULL, &$total_record = NULL) {
	if ( !$offset ) $offset = 0;
	if ( !$total_record ) $total_record = mysql_count($conn, $sql);
	$offset	= max(0, min($total_record - 1, $offset));
	return sprintf('%s LIMIT %d, %d', $sql, $offset, $count);
}
#分页查询, 返回字段位置和字段名做key两种方式结果
function mysql_paginateb($conn, $sql, $page_size, &$page, &$total_record = NULL,
		$ext_condition = NULL, $min = 0, $max = 0,
		$having_ext_condition= NULL, $hmin = 0, $hmax = 0) {
	mysql_paginate_limit($conn, $sql, $page_size, $page, $total_record, $ext_condition, $min, $max, $having_ext_condition, $hmin, $hmax);
	return mysql_getb_all($conn, $sql);
}
#分页查询, 返回字段位置做key的查询结果
function mysql_paginaten($conn, $sql, $page_size, &$page, &$total_record = NULL,
		$ext_condition = NULL, $min = 0, $max = 0,
		$having_ext_condition= NULL, $hmin = 0, $hmax = 0) {
	mysql_paginate_limit($conn, $sql, $page_size, $page, $total_record, $ext_condition, $min, $max, $having_ext_condition, $hmin, $hmax);
	return mysql_getn_all($conn, $sql);
}
#分页查询, 返回字段名做key的查询结果
function mysql_paginatea($conn, $sql, $page_size, &$page, &$total_record = NULL,
		$ext_condition = NULL, $min = 0, $max = 0,
		$having_ext_condition= NULL, $hmin = 0, $hmax = 0) {
	mysql_paginate_limit($conn, $sql, $page_size, $page, $total_record, $ext_condition, $min, $max, $having_ext_condition, $hmin, $hmax);
	return mysql_geta_all($conn, $sql);
}
#限制条数查询, 返回字段位置和字段名做key两种方式结果
function mysql_limitb($conn, $sql, &$count,
		$ext_condition = NULL, $min = 0, $max = 0,
		$having_ext_condition= NULL, $hmin = 0, $hmax = 0) {
	mysql_paginate_limit($conn, $sql, $count, $ext_condition, $min, $max, $having_ext_condition, $hmin, $hmax);
	return mysql_getb_all($conn, $sql, $count);
}
#限制条数查询, 返回字段位置做key结果
function mysql_limitn($conn, $sql, &$count,
		$ext_condition = NULL, $min = 0, $max = 0,
		$having_ext_condition= NULL, $hmin = 0, $hmax = 0) {
	mysql_paginate_limit($conn, $sql, $count, $ext_condition, $min, $max, $having_ext_condition, $hmin, $hmax);
	return mysql_getn_all($conn, $sql, $count);
}
#限制条数查询, 返回字段名做key结果
function mysql_limita($conn, $sql, &$count,
		$ext_condition = NULL, $min = 0, $max = 0,
		$having_ext_condition= NULL, $hmin = 0, $hmax = 0) {
	mysql_paginate_limit($conn, $sql, $count, $ext_condition, $min, $max, $having_ext_condition, $hmin, $hmax);
	return mysql_geta_all($conn, $sql, $count);
}
/**
 * mysql_ping_paginateb 
 * 分段式分页查询, 返回字段位置和字段名做key两种方式
 * @param mixed $conn 			连接
 * @param mixed $sql 			SQL语句
 * @param mixed $ping_size 		每段记录数
 * @param mixed $page_ping 		每页分段数
 * @param mixed $ping_page 		请求页码: PAGE_FIRST(首页), PAGE_LAST(尾页), 正数/0(正常页码), 负数(从后向前的页码, -1表示最后一页)
 * @param mixed $ping 			请求段号(页内): PING_FIRST(首段), PING_LAST(尾段), 正数/0(正常段号), 负数(从后向前的段号, -1表示最后一段)
 * @param mixed $total_record 	总记录数
 */
function mysql_ping_paginateb($conn, $sql, $ping_size, $page_ping, &$ping_page, &$ping, &$total_record = NULL) {
	if ( !is_int($total_record) ) $total_record = mysql_count($conn, $sql);
	$total_ping	= ceil($total_record / $ping_size);
	$page		= ping_to_page($ping_page, $ping, $page_ping, $total_ping);
	$page_size	= $ping_size;
	$datas	= mysql_paginateb($conn, $sql, $page_size, $page, $total_record);
	list($ping_page, $ping)	= page_to_ping($page, $page_ping);
	return $datas;
}
#分段式分页查询, 返回字段位置做key结果, 详见mysql_ping_paginateb
function mysql_ping_paginaten($conn, $sql, $ping_size, $page_ping, &$ping_page, &$ping, &$total_record = NULL) {
	if ( !is_int($total_record) ) $total_record = mysql_count($conn, $sql);
	$total_ping	= ceil($total_record / $ping_size);
	$page		= ping_to_page($ping_page, $ping, $page_ping, $total_ping);
	$page_size	= $ping_size;
	$datas	= mysql_paginaten($conn, $sql, $page_size, $page, $total_record);
	list($ping_page, $ping)	= page_to_ping($page, $page_ping);
	return $datas;
}
#分段式分页查询, 返回字段名做key结果, 详见mysql_ping_paginateb
function mysql_ping_paginatea($conn, $sql, $ping_size, $page_ping, &$ping_page, &$ping, &$total_record = NULL) {
	if ( !is_int($total_record) ) $total_record = mysql_count($conn, $sql);
	$total_ping	= ceil($total_record / $ping_size);
	$page		= ping_to_page($ping_page, $ping, $page_ping, $total_ping);
	$page_size	= $ping_size;
	$datas	= mysql_paginatea($conn, $sql, $page_size, $page, $total_record);
	list($ping_page, $ping)	= page_to_ping($page, $page_ping);
	return $datas;
}
#向数据库插入记录
function mysql_insert($conn, $sql, &$affected_row = NULL, &$last_id = NULL) {
	$argc	= func_num_args();
	if ( !mysql_execute($conn, $sql) ) return FALSE;
	if ( $argc > 2 ) $affected_row = mysql_affected_rows($conn);
	if ( $argc > 3 ) $last_id = mysql_insert_id($conn);
	return TRUE;
}
#更新记录
function mysql_update($conn, $sql, &$affected_row = NULL) {
	$argc	= func_num_args();
	if ( !mysql_execute($conn, $sql) ) return FALSE;
	if ( $argc > 2 ) $affected_row = mysql_affected_rows($conn);
	return TRUE;
}
#删除记录
function mysql_delete($conn, $sql, &$affected_row = NULL) {
	$argc	= func_num_args();
	if ( !mysql_execute($conn, $sql) ) return FALSE;
	if ( $argc > 2 ) $affected_row = mysql_affected_rows($conn);
	return TRUE;
}



/////////////////////////////////////xp

function mysql_conditon_append($sql, $ext_condition_sql) {
    if(!$ext_condition_sql)
        return $sql;
	$where = ' where ';
	if(preg_match(PAGINATE_WHERE_SQL_PATTERN, $sql)) {
		$where = ' and ';
	}
	#如果有(group|order)by
	if( preg_match(PAGINATE_CONDITION_GROUP_SQL_PATTERN, $sql) ) {
		#如果有where
		$sql = preg_replace(PAGINATE_CONDITION_GROUP_SQL_PATTERN, " $where $ext_condition_sql " .
				PAGINATE_CONDITION_SQL_REPLACEMENT, $sql);
	}elseif( preg_match(PAGINATE_CONDITION_ORDER_SQL_PATTERN, $sql) ){
		$sql = preg_replace(PAGINATE_CONDITION_ORDER_SQL_PATTERN, " $where $ext_condition_sql " .
				PAGINATE_CONDITION_SQL_REPLACEMENT, $sql);
	}else{
		$sql .= $where . $ext_condition_sql;
	}
	return $sql;
}
/*
 * 附件having 条件
 */
function mysql_conditon_append_having($sql, $having_condition_sql) {
    if(!$having_condition_sql)
        return $sql;
	$having = ' having ';
	if( preg_match(PAGINATE_CONDITION_ORDER_SQL_PATTERN, $sql) ){
		$sql = preg_replace(PAGINATE_CONDITION_ORDER_SQL_PATTERN, " $having $ext_condition_sql " .
				PAGINATE_CONDITION_SQL_REPLACEMENT, $sql);
	} else {
		$sql = $sql . $having . $having_condition_sql;
	}
	return $sql;
}
