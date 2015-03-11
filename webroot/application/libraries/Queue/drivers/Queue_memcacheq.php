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

class CI_Queue_memcacheq extends CI_Driver {

	private $_memcached = NULL;	// Holds the memcached object
	protected $_root = '';
	protected $_memcache_conf 	= array(
					'default' => array(
						'default_host'		=> '127.0.0.1',
						'default_port'		=> 21211,
						'default_weight'	=> 1
					)
				);

	// ------------------------------------------------------------------------	

	/**
	 * Fetch from cache
	 *
	 * @param 	mixed		unique key id
	 * @return 	mixed		data on success/false on failure
	 */	
	public function pop($id, $num=0)
	{	
		$id = $this->_root . $id; 
		$id = md5($id);
		$data = $this->_memcached->get($id);
		if( is_array($data) ) {
			if(isset($data[1]) && $data[1] == 1) return $this->pop_data($data[0]);
			return $data[0];
		}
		return FALSE;
		//return (is_array($data)) ? $data[0] : FALSE;
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
	public function push($id, $data, $ttl = 0)
	{
		$id = $this->_root . $id;
		$id = md5($id);
		if (get_class($this->_memcached) == 'Memcached')
		{
			$r = $this->_memcached->set($id, array($data, time(), $ttl), $ttl);
			if(false === $r) {
				$data_id = $this->push_data($data);
				$r = $this->_memcached->set($id, array($data_id, 1, $ttl), $ttl);
			}
			return $r;
		}
		else if (get_class($this->_memcached) == 'Memcache')
		{
			$r = $this->_memcached->set($id, array($data, time(), $ttl), 0, $ttl);
			if(false === $r) {
				$data_id = $this->push_data($data);
				$r = $this->_memcached->set($id, array($data_id, 1, $ttl), 0, $ttl);
			}
			return $r;
		}
		
		return FALSE;
	}

	//存储大数据
	private function push_data($data) {
		$CI = &get_instance();
		$_db = $CI->load->database('queue', TRUE);
		$r = $_db->insert('queue_mdata', array('body'=>$data));
		if($r) {
		$id = $_db->insert_id();
		return $id;
		}
		return false;
	}
	private function pop_data($data_id) {
		$CI = &get_instance();
		$_db = $CI->load->database('queue', TRUE);
		$q = $_db->get_where('queue_mdata', array('id' => $data_id));
		if(isset($q->row()->body)) {
			$_db->update('queue_mdata', array('status'=>1), array('id' => $data_id));	
			return $q->row()->body;
		}
		return '';
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Setup memcached.
	 */
	private function _setup_memcached()
	{
		if( $this->_memcached != NULL) return TRUE;

		// Try to load memcached server info from the config file.
		$CI =& get_instance();
		$CI->load->helper('namespace');
		$this->_root = namespace_queue_root('');

		if ($CI->config->load('config', TRUE, TRUE))
		{
			if (is_array($CI->config->config['memcacheq']))
			{
				$this->_memcache_conf = NULL;

				foreach ($CI->config->config['memcacheq'] as $name => $conf)
				{
					$this->_memcache_conf[$name] = $conf;
				}				
			}			
		}

		if(extension_loaded('memcached'))	
			$this->_memcached = new Memcached();
		else	
			$this->_memcached = new Memcache();
		
		foreach ($this->_memcache_conf as $name => $cache_server)
		{
			if ( ! array_key_exists('hostname', $cache_server))
			{
				$cache_server['hostname'] = $this->_default_options['default_host'];
			}
	
			if ( ! array_key_exists('port', $cache_server))
			{
				$cache_server['port'] = $this->_default_options['default_port'];
			}
	
			if ( ! array_key_exists('weight', $cache_server))
			{
				$cache_server['weight'] = $this->_default_options['default_weight'];
			}

			if(get_class() === 'Memcached')
				$this->_memcached->addServer(
					$cache_server['hostname'], $cache_server['port'], $cache_server['weight']
			);
			else {
				$r = $this->_memcached->addServer(
                                        $cache_server['hostname'], $cache_server['port'], true, $cache_server['weight']
                        	);
			}	
		}
	}

	// ------------------------------------------------------------------------


	/**
	 * Is supported
	 *
	 * Returns FALSE if memcached is not supported on the system.
	 * If it is, we setup the memcached object & return TRUE
	 */
	public function is_supported()
	{
		if ( ! extension_loaded('memcached') && !extension_loaded('memcache') )
		{
			log_message('error', 'The Memcached Extension must be loaded to use Memcached Cache.');
			return FALSE;
		}
		
		$this->_setup_memcached();
		return TRUE;
	}

	// ------------------------------------------------------------------------

}
// End Class

/* End of file Cache_memcached.php */
/* Location: ./system/libraries/Cache/drivers/Cache_memcached.php */
