<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Commend extends CI_Controller {

	function __construct() {
        parent :: __construct();
        $this->load->helper(array('url'));
		$this->load->model("api/api_model","api");
		$this->load->model("graduation/admin_model","admin");
		$this->load->library('pagination');
	}

	public function index() {
	}

	#详情
	public function detail() {
		$cid = $this->input->get_post('cid' , TRUE);

		if ( empty($cid) ) {
			redirect('welcome');
			return;
		}
		$content = $this->admin->selectBy('commend', array('is_active'=>1, 'id'=>$cid));
		$data = array(
			'name' => $content[0]['name'],
			'detail' => $content[0]['detail'],
		);
		$this->load->view('graduation/commend_detail.html', $data);
	}
}
