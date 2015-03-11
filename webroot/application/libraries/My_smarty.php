<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH.'third_party/Smarty/Smarty.class.php';

/**
 * Smarty Class
 *
 * Lets you use smarty engine in CodeIgniter.
 *
 * @package		Application
 * @subpackage	Libraries
 * @category	Customize Class
 * @author		sitearth
 * @link		blog.sitearth.com
 */
class My_smarty extends Smarty {
	
	var $CI;
	
	/**
	 * Smarty constructor
	 *
	 * The constructor runs the session routines automatically
	 * whenever the class is instantiated.
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->CI =& get_instance();
		$this->template_dir = APPPATH . 'views/';
		$this->compile_dir = APPPATH.'/../../cache/templates_c/';
		$this->left_delimiter = '{';
		$this->right_delimiter = '}';
	}

	// --------------------------------------------------------------------
	
	/**
	 * An encapsulation of display method in smarty class
	 * 
	 * @access	public
	 * @param	string
	 * @param   mixed
	 * @return	void
	 */
	public function view($template_file, $assigns = array())
	{
		if (strpos($template_file, '.') === false)
		{
			$template_file .= '.html';
		}
		$tepdir = $this->template_dir[0];
		if ( ! is_file($tepdir . '/' . $template_file)) {
			show_error("Smarty error: {$template_file} cannot be found.");
		}

		if (is_array($assigns) && !empty($assigns))
		{
			foreach ($assigns as $key => $value)
				$this->assign($key, $value);
		}
		$this->assign('base_url', base_url());		
		$this->display($template_file);		
	}

}
/* End of file Smarty.php */
/* Location: ./application/libraries/Smarty.php */
