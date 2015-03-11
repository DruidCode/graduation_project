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
 * Event Class // 用于各个模块之间的数据和实践交互，后台worker的驱动器
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Logging
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/errors.html
 */
class Event {

	protected $_namespace	= 'unknow';
	protected $_event_queue	= false;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$config =& get_config();
		
		if(isset($config['namespace']) && $config['namespace'] != '') {
			$this->_namespace = $config['namespace'];
		}
		log_message('debug', 'Event class init namespace=' . $this->_namespace);
		
		$CI =& get_instance();
		$CI->load->driver('queue');
		if(isset($CI->queue)) {
			$this->_event_queue = $CI->queue->memcacheq;
			log_message('debug', 'Event class init queue');
		}
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
	public function add($event_class, $event_name, $event_msg)
	{
		$send_data = $this->_item_encode($event_name, $event_msg);
		$queue_name = $this->_queue_name($event_class); 
		if($this->_event_queue->push($queue_name, $send_data))
			return TRUE;
		else
			return FALSE;
	}


	// --------------------------------------------------------------------

        /**
         * Write Log File
         *
         * Generally this function will be called using the global log_message() function
         *
         * @param       string  the error level
         * @param       string  the error message
         * @param       bool    whether the error is a native PHP error
         * @return      bool
         */
        public function read($event_class, $count=10, $timeout=10)
        {
		$return = array();
		$begin_time = time(NULL);
		while( ($one = $this->_readone($event_class)) !== FALSE ) {
			array_push($return, $one);
			if($count && sizeof($return) >= $count) break;
			if(time(NULL) - $begin_time > $timeout) break;
		}

		if(sizeof($return) === 0) return FALSE;
		return $return;
        }

	protected function _readone($event_class) {	
		$queue_name = $this->_queue_name($event_class);
                $data   = $this->_event_queue->pop($queue_name);
                if(false === $data) return FALSE;

                $return = array();
                $event_name = '';
                $message = '';
                $this->_item_decode($data, $event_name, $message);
                if( $event_name !== '' || $message !== '') {
			$return = new stdClass;
			$return->event_name = $event_name;
			$return->data = $message;
                }
		return $return;
	}
	// --------------------------------------------------------------------

        /**
         * Write Log File
         *
         * Generally this function will be called using the global log_message() function
         *
         * @param       string  the error level
         * @param       string  the error message
         * @param       bool    whether the error is a native PHP error
         * @return      bool
         */
        public function readall($event_class)
        {
                return $this->read($event_class, 0 , 0 );
        }
	
	// --------------------------------------------------------------------

        /**
         * Write Log File
         *
         * Generally this function will be called using the global log_message() function
         *
         * @param       string  the error level
         * @param       string  the error message
         * @param       bool    whether the error is a native PHP error
         * @return      bool
         */
        protected function _item_encode($event_name, $message, $protocol='json')
        {
                $return = new stdClass;
		$return->event_name	= $event_name;
		$return->data		= $message;
		return json_encode($return);
        }
	
	// --------------------------------------------------------------------

        /**
         * Write Log File
         *
         * Generally this function will be called using the global log_message() function
         *
         * @param       string  the error level
         * @param       string  the error message
         * @param       bool    whether the error is a native PHP error
         * @return      bool
         */
	protected function _item_decode($input, &$event_name, &$message, $protocol='json') {
		$return = json_decode($input);
		if(isset($return->event_name)) $event_name = $return->event_name;
		if(isset($return->data)) $message = $return->data;
		return TRUE;
	}

	// --------------------------------------------------------------------

        /**
         * Write Log File
         *
         * Generally this function will be called using the global log_message() function
         *
         * @param       string  the error level
         * @param       string  the error message
         * @param       bool    whether the error is a native PHP error
         * @return      bool
         */
        protected function _queue_name($event_class) {
		$queue_name = 'event/'. $event_class;
		log_message('debug', 'Event class internal queue_name='. $queue_name);
		return $queue_name;
        }
}
// END Event Class

/* End of file Event.php */
/* Location: ./system/libraries/Log.php */
