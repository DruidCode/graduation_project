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
if ( !function_exists('curl_get_content') ) {
	//以curl方式获取网页内容
	function curl_get_content($url, $post=false, $location=false) {
		$ch = curl_init();
		if($location == 302) 	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);  
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if(false !== $post) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		$content = curl_exec($ch);
		curl_close($ch);

		return $content;
	}

	
}


if ( !function_exists('curl_get_post') ) {
	//post
	function curl_get_post($url, $post=false, &$status=200, $timeout=20) {
		$ch = curl_init();
		if ( empty($ch) )return false;
		curl_setopt($ch, CURLOPT_URL, $url);
		//post����
		if(false !== $post)	{
			curl_setopt ( $ch, CURLOPT_POST, 1 );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query ( $post, '', '&' ) );
		}
		//https
		if (trim ( substr ( $url, 0, 5 ) ) == 'https') {
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 1 );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		}
		
		curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$content = curl_exec($ch);
		//���ش����
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $content;
	}
}

/* Location: ./system/helpers/file_helper.php */
