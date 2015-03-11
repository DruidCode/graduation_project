<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Activity extends CI_Controller {

	function __construct() {
        parent :: __construct();
        $this->load->helper(array('url'));
		$this->load->library('pagination');
		$this->load->model("graduation/admin_model","admin");
	}

	public function index() {
		$total = $this->admin->get_total('act', 'is_active = 1');
		$per = 5;
		$config['base_url'] = site_url('activity/index');
		$config['total_rows'] = $total;
		$config['per_page'] = $per;
		//获取留言列表
		$list = $this->admin->get_list('act', $per, $this->uri->segment(3), 'ctime desc');
		$this->pagination->initialize($config);
		$re = $this->pagination->create_links();

		$data = array(
			'page' => $re,
			'list' => $list,
		);
		$this->load->view('graduation/activity.html', $data);
	}
}
