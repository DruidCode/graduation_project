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
class Lock {

	/**
	 * Constructor
	 */
	public function __construct()
	{
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
	public function lock($id)
	{
		return FALSE;
	}

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
        public function unlock($id)
        {
                return FALSE;
        }

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
        public function wait($id, $timeout=30)
        {
                return FALSE;
        }

}
// END Log Class

/* End of file Log.php */
/* Location: ./system/libraries/Log.php */
