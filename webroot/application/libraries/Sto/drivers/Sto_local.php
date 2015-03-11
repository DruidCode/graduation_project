<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006 - 2012 EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 2.0
 * @filesource	
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Memcached Caching Class 
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		ExpressionEngine Dev Team
 * @link		
 */

class CI_Sto_local extends CI_Driver {
	
	protected $_root = '';
	// ------------------------------------------------------------------------	
	
	/**
	 * Fetch from cache
	 *
	 * @param 	mixed		unique key id
	 * @return 	mixed		data on success/false on failure
	 */	
	public function get($id, $path='', $suffix='', $mod='md5', $time=0)
	{
		$subpath = '';
		$filepath = namespace_filename($this->_root, $id, '', $path, $suffix, $mod, $time, $subpath);
		if(!file_exists($filepath)) return FALSE;	

		return file_get_contents($filepath);
	}
	
	public function delete($id, $path='', $suffix='', $mod='md5', $time=0) {
		$subpath = '';
		$filepath = namespace_filename($this->_root, $id, '', $path, $suffix, $mod, $time, $subpath);
		if(!file_exists($filepath)) return TRUE;
		
		return unlink($filepath);
	}
	// ------------------------------------------------------------------------

	/**
	 * Save
	 *
	 * @param 	string		unique identifier
	 * @param 	mixed		data being cached
	 * @param 	int			time to live
	 * @return 	boolean 	true on success, false on failure
	 */
	public function put(&$id, $data, $path='', $suffix='', $mod='md5', $time=0)
	{
		$subpath = '';
		$filepath = namespace_filename($this->_root, $id, $data, $path, $suffix, $mod, $time, $subpath);
		if(false === $filepath) return FALSE;
		if(!namespace_pathok($subpath))
			return FALSE;
		if( false === file_put_contents($filepath, $data)) return FALSE;
		return TRUE;
	}
	
	public function fd($id, $path='', $suffix='', $mod='md5', $time=0) {
		$subpath = '';
		$filepath = namespace_filename($this->_root, $id, '', $path, $suffix, $mod, $time, $subpath);
		if(!file_exists($filepath)) return FALSE;

		return fopen($filepath);
	}

	public function info($id, $key=array('size','ctime'), $path='', $suffix='', $mod='md5', $time=0) {
		$subpath = '';
                $filepath = namespace_filename($this->_root, $id, '', $path, $suffix, $mod, $time, $subpath);
		if(!file_exists($filepath)) return FALSE;
		
		if(in_array('size', $key)) $key['size'] = filesize($filepath);
		if(in_array('ctime', $key)) $key['ctime'] = filectime($filepath);
		
		if(sizeof($key) > 0)
			return $key;
		
		return FALSE;
	}
	/**
	 * Is supported
	 *
	 * Returns FALSE if memcached is not supported on the system.
	 * If it is, we setup the memcached object & return TRUE
	 */
	public function is_supported()
	{
		$CI = & get_instance();
		$r = $CI->load->helper('namespace');
		
		if ($CI->config->load('config', TRUE, TRUE)) {
			if($CI->config->config['data_path']) {	
				$root = namespace_sto_root('local');
				$this->_root = ($CI->config->config['data_path']) . '/' . $root;
				return TRUE;
			}
			return FALSE;
		} else
			return FALSE;
	}

	// ------------------------------------------------------------------------

}
// End Class

/* End of file Cache_memcached.php */
/* Location: ./system/libraries/Cache/drivers/Cache_memcached.php */
