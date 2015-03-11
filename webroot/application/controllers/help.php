<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Help extends CI_Controller {

	function __construct() {
        parent :: __construct();
        $this->load->helper(array('url'));
	}

	public function index() {
		$this->load->view('graduation/about.html');
	}

	public function contact() {
		$this->load->view('graduation/contact.html');
	}

	public function about() {
		$this->load->view('graduation/about.html');
	}
}
