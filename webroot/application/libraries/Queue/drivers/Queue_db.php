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

class CI_Queue_db extends CI_Driver {

	protected $_db = NULL;	
	protected $_root = '';
	// ------------------------------------------------------------------------	

	/**
	 * Fetch from cache
	 *
	 * @param 	mixed		unique key id
	 * @return 	mixed		data on success/false on failure
	 */	
	public function pop($id, $num=0)
	{
		$qname = $this->_root . $id;
		if($this->_db) {
			$query = false;
			$sql = 'select * from  queue where qname = ? order by id ';
			if($num) {
				$sql .= ' limit ?';
				$query = $this->_db->query($sql, array($qname, $num));
			} else {
				$query = $this->_db->query($sql, array($qname));
			}

			if($query) {
				$ret_array = array();
				$id_array = array();

				foreach($query->result() as $row) {
					$ret_array[$row->id] = $row->message;	
					array_push($id_array, intval($row->id));
				}

				//删除队列元素
				foreach($id_array as $id) {
					$sql	 = 'delete from queue where id = ? and qname = ?';
					$this->_db->query($sql, array($id, $qname));
				}

				if(sizeof($ret_array) > 0) return $ret_array;

			} else {
				$this->_db->reconnect();
				return FALSE;
			}
		}
		return FALSE;
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
	public function push($id, $data, $ttl = 60)
	{
		$qname = $this->_root . $id;
		if($this->_db) {
			$query = false;
			$sql	= 'insert into queue(qname, message) values(?,?)';
			
			$num = 0;
			if(is_array($data)) {
				foreach($data as $data_one) {
					$query = $this->_db->query($sql, array($qname, $data_one));
					if($query) $num ++;
				}
			}
			else {
				$query = $this->_db->query($sql, array($qname, $data));
				if($query) $num ++;
			}

			if($num) {
				return $num;
			} else {
				$this->_db->reconnect();
				return FALSE;
			}
		}
		return FALSE;
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Setup memcached.
	 */
	private function _setup_dbq()
	{
		if( $this->_db != NULL) return TRUE;
		
		// Try to load memcached server info from the config file.
		$CI =& get_instance();
		$CI->load->helper('namespace');
		$this->_root = namespace_queue_root('');
		
		$DB = $CI->load->database('queue', TRUE);
		if($DB) {
			$this->_db = $DB;
			return TRUE;
		}

		return FALSE;
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
		return $this->_setup_dbq();
	}

	// ------------------------------------------------------------------------

}
// End Class

/* End of file Cache_memcached.php */
/* Location: ./system/libraries/Cache/drivers/Cache_memcached.php */
