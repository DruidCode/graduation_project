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
if ( !function_exists('web_api_load') ) {
	function web_api_load() {
		require_once(dirname(__FILE__) . '/../../../lib/www_api.php');
	}
}

if ( !function_exists('get_user_info') ) {
/*
	function get_user_info($mobile) {
        $param = array('mobile'=>$mobile);
        $data = api('user')->get_user_info_by_mobile($param);
        log_message('info', $mobile . ' request web info='.var_export($data,true));
        return $data;
	}
*/
    function get_user_info($mobile){
        require_once(dirname(__FILE__).'/curl_helper.php');
        $result = curl_get_post(ACT_DB_API_URL.'/api/user/selbymobile', array('app_key'=>ACT_DB_APP_KEY, 'mobile'=>$mobile));
        log_message('info', $mobile . ' request web info='.var_export($result, true));
        $result = json_decode($result, true);
        log_message('info', $mobile . ' request web info='.var_export($result, true));
        if($result && ('200' == $result['code'])){
            return (array)$result['users'][0];
        }
        return array();
    }
}
/* Location: ./system/helpers/file_helper.php */
