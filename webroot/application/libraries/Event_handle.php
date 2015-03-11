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
 * Logging Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Logging
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/errors.html
 */
class Event_handle {

	protected $_handle;
	protected $_event;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$CI =& get_instance();
		$CI->load->library('Event');
		$this->_event  =  $CI->event;
	}

	// --------------------------------------------------------------------

	/**
	 * Write Log File
	 *
	 * Generally this function will be called using the global log_message() function
	 *
	 * @param	string	the error level
	 * @param	string	the error message
	 * @param	bool	whether the error is a native PHP error
	 * @return	bool
	 */
	public function dispatch($event_class, $handle)
	{
		$this->_handle = $handle;
		$events = $this->_event->readall($event_class);
		if($events === false) return 0;

		foreach($events as $event) {
			$event_name = $event->event_name;
			$data = $event->data;
			if (method_exists($this->_handle, $event_name))
				call_user_func_array(array($this->_handle, $event_name), array($data));
			else 
				log_message('error', 'Event not found: class ' . get_class($this->_handle) . ' data:' . json_encode($event));
			
			log_message('debug', 'Event:' . json_encode($event));
		}
		
		return sizeof($events);	
	}

}
// END Log Class

/* End of file Log.php */
/* Location: ./system/libraries/Log.php */
