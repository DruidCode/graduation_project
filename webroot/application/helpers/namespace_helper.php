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
 * @access	public
 * @param	string	path to file
 * @return	string
 */
if ( ! function_exists('namespace_app'))
{
	function namespace_app()
	{
		static $_app_namespace = '';
		if( $_app_namespace != '') return $_app_namespace;
		
		$CI = & get_instance();
		$CI->load->config('config');
		$_app_namespace = $CI->config->config['namespace'];
		log_message('debug', 'namespace  : ' . $_app_namespace);
		return $_app_namespace;
	}
}

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
if ( ! function_exists('namespace_sto_root'))
{
        function namespace_sto_root($sub)
        {
			return '/' . namespace_app() . '/sto/' . $sub;
        }
}


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
if ( ! function_exists('namespace_cache_root'))
{
        function namespace_cache_root($sub)
        {
			if($sub != '') $sub = trim($sub, "/") . '/';
			return '/' . namespace_app() . '/cache/' . $sub;
        }
}


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
if ( ! function_exists('namespace_queue_root'))
{
        function namespace_queue_root($sub)
        {
			if($sub != '') $sub = trim($sub, "/") . '/';
			return '/' . namespace_app() . '/queue/' . $sub;
        }
}


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
if ( ! function_exists('namespace_path_md5'))
{
        function namespace_path_md5($id, $root)
        {
			return $root . '/' . substr($id, 0, 3) . '/'. substr($id, 3, 3) . '/' . substr($id, 6, 2);
        }
}

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
if ( ! function_exists('namespace_path_time'))
{
        function namespace_path_time($time, $root)
        {
			return $root . '/' . substr($time, 0, 4) . '/'. substr($time, 4,4);
        }
}

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
if ( ! function_exists('namespace_path_id'))
{
        function namespace_path_id($id, $root)
        {
			return $root . '/' . ($id>>30) . '/' . (($id>>20)&(1023)) . '/' . (($id>>10)&(1023));
        }
}


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
if ( ! function_exists('namespace_path_timeid'))
{
        function namespace_path_timeid($id, $time, $root)
        {
			return $root . '/' . $time . '/' . (($id>>20)) . '/' . (($id>>10)&(1023)); 
        }
}


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
if ( ! function_exists('namespace_path_timemd5'))
{
        function namespace_path_timemd5($id, $time, $root)
        {
			return $root . '/' . $time . '/' . substr($id, 0, 3) . '/' . substr($id, 3, 3);
        }
}


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
if ( ! function_exists('namespace_filename'))
{
        function namespace_filename($root, &$id, $data, $path, $suffix, $mod, $time, &$subpath)
        {
			 $subpath = '';
			 $filename = '';
			 //名字逻辑
			 if($id !== '')
			 	$filename = $id;
			 else if($data !== '') {
			 	$filename = md5($data . uniqid());
				$id = substr($filename, 0, 12);
				$filename = $id;
			 }
			 else
			 	return FALSE;
			 
			 $file_path = '';
				
			 if($time == 0) $time = time(NULL);
			 $times = $time . "";
			 if(strlen($time . "") === strlen(time(NULL) . "")) $times = date('Ymd', $time);
			 if($times === '' || intval($times) === 0) return FALSE;
			 
			 switch($mod) {
			    			case 'md5': //md5 模式生成新的文件id
				 				$subpath = namespace_path_md5($filename, $path);
				   				break;
							case 'time': //时间模式，按年月日生成
								$subpath = namespace_path_time($times, $path);
								break;
							case 'id':
								$subpath = namespace_path_id(intval($id), $path);
								break;
							case 'no':
								$subpath = $path; 
								break;
							case 'timeid':
								$subpath = namespace_path_timeid(intval($id), $times, $path);
								break;
							case 'timemd5':
								$subpath = namespace_path_timemd5($filename, $times, $path);
								break;
			}

			//名字生成
			if($suffix !== '') $id .= '.' . trim($suffix, ".");
			$file_path = $root . '/'. $subpath . '/' . $id;
			$subpath = $root . '/'. $subpath . '/';

			return $file_path;
        }
}

if ( ! function_exists('namespace_pathok'))
{
	function namespace_pathok($path) {
		if($path == '' || $path == '/') return TRUE;

		if(!is_dir($path)) {
			$dir = dirname($path);	
			namespace_pathok($dir);
			mkdir($path);
		}
		return TRUE;
	}
}

/* Location: ./system/helpers/file_helper.php */
