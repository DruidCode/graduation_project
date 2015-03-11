<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	function __construct() {
        parent :: __construct();
        $this->load->helper(array('url'));
        $this->load->model('graduation/admin_model', 'admin');
        $this->load->model('api/api_model', 'api');
	}
	public function index() {
		#活动
		$act_list = $this->admin->get_list('act', 3, '', 'ctime desc', 'is_active = 1');
		foreach ($act_list as &$act) {
			$act['act_info'] = $this->api->mbStr($act['act_info'], '50') . '...';
		}
		#表彰列表
		$commend_list = $this->admin->get_list('commend', 3, '', '', 'is_active = 1');
		foreach ($commend_list as &$li) {
			$li['brief'] = $this->api->mbStr($li['brief'], '50') . '...';
		}
		$data = array(
			'commend_list' => $commend_list,
			'act_list' => $act_list,
		);
		$this->load->view('graduation/index.html', $data);
	}

	#根据活动名搜索
	public function search() {
        $name  = trim($this->input->get_post('search', true));
		$list = $this->admin->get_list('act', 10000000, $this->uri->segment(3), '', "act_name like '%" . $name . "%'");

		$data = array(
			'list' => $list,
		);
		$this->load->view('graduation/search.html', $data);
	}
}
