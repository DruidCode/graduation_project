<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter File Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/file_helpers.html
 */

// ------------------------------------------------------------------------

/**
 * Read File
 *
 * Opens the file specfied in the path and returns it as a string.
 *
 * @access      public
 * @param       string  path to file
 * @return      string
 */
if ( !function_exists('memcache_api_load') ) {
    function memcache_api_load() { 
        require_once(dirname(__FILE__) . '/../../../lib/memcache.lib.php');
    }
}

//验证
function verify_vc($mobile)
{
    $mc = mc_instance(MEMCACHE_INFOS);
    if (!is_resource($mc->connection)) {
        return false;
    }
    $re = mc_get($mc, $mobile);
    if ($re == false) {
        return false;
    } else {
        return $re;
    }
}

//记录 过期时间为到第二天凌晨
function memcache_record($mobile, $vc)
{
    $mc = mc_instance(MEMCACHE_INFOS);
    if (!is_resource($mc->connection)) {
        return false;
    }
    $now = time();
    $end = strtotime("23:59:59");
    $time = $end - $now;
    $key = $mobile;
	log_message('info', $mobile . ' time=='.$time.' vc=='.$vc);
    $set = mc_set($mc, $key, $vc, 0, $time);
    if ($set == false) {
        return false;
    }
    return true;
}

//清除验证码，当重复发送指令是，必须清除上一次的验证码memcache
function flush_record($mobile)
{
    $mc = mc_instance(MEMCACHE_INFOS);
    if (!is_resource($mc->connection)) {
        return false;
    }
    mc_del($mc, $mobile); 
}
/* Location: ./system/helpers/file_helper.php */
