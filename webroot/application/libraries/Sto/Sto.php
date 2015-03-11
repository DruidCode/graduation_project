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
 * CodeIgniter Caching Class 
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		ExpressionEngine Dev Team
 * @link		
 */
class CI_Sto extends CI_Driver_Library {
	
	protected $valid_drivers 	= array(
		'sto_local', 'sto_nfs', 'sto_dummy', 
	);

	protected $_adapter			= 'dummy'; //默认指针
	protected $_backup_driver;
	
	// ------------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param array
	 */
	public function __construct($config = array())
	{
		if ( ! empty($config))
		{
			$this->_initialize($config);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get 
	 *
	 * Look for a value in the cache.  If it exists, return the data 
	 * if not, return FALSE
	 *
	 * @param 	string	
	 * @return 	mixed		value that is stored/FALSE on failure
	 */
	public function get($id, $path='', $suffix='', $mod='md5', $time=0)
	{	
		return $this->{$this->_adapter}->get($id, $path, $suffix, $mod, $time);
	}
	
	public function delete($id, $path='', $suffix='', $mod='md5', $time=0)
	{
		return $this->{$this->_adapter}->delete($id, $path, $suffix, $mod, $time);
	}
	// ------------------------------------------------------------------------

	/**
	 * Cache Save
	 *
	 * @param 	string		Unique Key
	 * @param 	mixed		Data to store
	 * @param 	int			Length of time (in seconds) to cache the data
	 * @mod    1,no(无模式） 2，time（时间顺序），3 id（id顺序），4 md5（乱序）
                   5，time+id 6, time+md5 一共六种存储模式
	 * @return 	boolean		true on success/false on failure
	 */
	public function put(&$id, $data, $path='', $suffix='', $mod='md5', $time=0)
	{
		return $this->{$this->_adapter}->put($id, $data, $path, $suffix, $mod, $time);
	}
	
	// ------------------------------------------------------------------------

        /**
         * Cache Save
         *
         * @param       string          Unique Key
         * @param       mixed           Data to store
         * @param       int                     Length of time (in seconds) to cache the data
         *
         * @return      boolean         true on success/false on failure
         */
        public function	append($id, $data, $path='', $suffix='', $mod='md5', $time=0)
        {
                return $this->{$this->_adapter}->append($id, $data, $path, $suffix, $mod, $time);
        }
		
	// ------------------------------------------------------------------------

        /**
         * Cache Save
         *
         * @param       string          Unique Key
         * @param       mixed           Data to store
         * @param       int                     Length of time (in seconds) to cache the data
         *
         * @return      boolean         true on success/false on failure
         */
        public function fd($id, $path='', $suffix='', $mod='md5', $time=0)
        {
                return $this->{$this->_adapter}->fd($id, $path, $suffix, $mod, $time);
        }
	
	// ------------------------------------------------------------------------

        /**
         * Cache Save
         *
         * @param       string          Unique Key
         * @param       mixed           Data to store
         * @param       int                     Length of time (in seconds) to cache the data
         *
         * @return      boolean         true on success/false on failure
         */
        public function info($id, $key=array('size','ctime'), $path='', $suffix='', $mod='md5', $time=0)
        {
                return $this->{$this->_adapter}->info($id, $key, $path, $suffix, $mod, $time);
        }
	// ------------------------------------------------------------------------

	/**
	 * Initialize
	 *
	 * Initialize class properties based on the configuration array.
	 *
	 * @param	array 	
	 * @return 	void
	 */
	private function _initialize($config)
	{        
		$default_config = array(
				'adapter',
			);

		foreach ($default_config as $key)
		{
			if (isset($config[$key]))
			{
				$param = '_'.$key;

				$this->{$param} = $config[$key];
			}
		}

		if (isset($config['backup']))
		{
			if (in_array('sto_'.$config['backup'], $this->valid_drivers))
			{
				$this->_backup_driver = $config['backup'];
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Is the requested driver supported in this environment?
	 *
	 * @param 	string	The driver to test.
	 * @return 	array
	 */
	public function is_supported($driver)
	{
		static $support = array();

		if ( ! isset($support[$driver]))
		{
			$support[$driver] = $this->{$driver}->is_supported();
		}

		return $support[$driver];
	}

	// ------------------------------------------------------------------------

	/**
	 * __get()
	 *
	 * @param 	child
	 * @return 	object
	 */
	public function __get($child)
	{
		$obj = parent::__get($child);
		
		if ( ! $this->is_supported($child))
		{
			$this->_adapter = $this->_backup_driver;
		}

		return $obj;
	}
	
	// ------------------------------------------------------------------------
}
// End Class

/* End of file Cache.php */
/* Location: ./system/libraries/Cache/Cache.php */
