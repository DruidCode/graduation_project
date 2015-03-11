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
if ( !function_exists('sm_api_load') ) {
	function sm_api_load() {
		require_once(dirname(__FILE__) . '/../../../lib/ms_client_api.php');
	}
}

if ( !function_exists('sms_send') ) {

	function sms_send($mobiles, $content, $snumber = '24') {
		$client = MS_CLIENT_TYPE_WWW;
		$version = MS_CONST_VER_SMS;
		$client_id = '1001';
		$source = 'csmd';
		$secret_key = '89b1bc817acb742dc915be12510f5e33';
		$r = ms_send($client, $version, $client_id, $source, $secret_key, $mobiles, $content, $snumber);
		log_message('info', ' sms send ' . $mobiles . ' , ' . $content . ' result='.var_export($r,true));
		return $r;
	}
}
/* Location: ./system/helpers/file_helper.php */
