<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News extends CI_Controller {

	function __construct() {
        parent :: __construct();
        $this->load->helper(array('url'));
		$this->load->model("api/api_model","api");
		$this->load->model("graduation/news_model","news");
		$this->load->model("graduation/admin_model","admin");
		$this->load->library('pagination');
	}

	public function index() {
		$total = $this->news->get_news_total();
		$per = 5;
		$config['base_url'] = site_url('news/index');
		$config['total_rows'] = $total;
		$config['per_page'] = $per;
		//获取留言列表
		$list = $this->admin->get_list('news', $per, $this->uri->segment(3), 'ctime desc', 'is_active = 1');
		$sider = $this->admin->get_list('news', 10, 0, 'ctime desc', 'is_active = 1');
		$this->pagination->initialize($config);
		$re = $this->pagination->create_links();

		$data = array(
			'page' => $re,
			'list' => $list,
			'sider' => $sider,
		);
		$this->load->view('graduation/news.html', $data);
	}

	#新闻详情
	public function news_detail() {
		$nid = $this->input->get_post('nid' , TRUE);

		if ( empty($nid) ) {
			redirect('news');
			return;
		}
		$content = $this->news->news_detail($nid);
		$sider = $this->admin->get_list('news', 10, 0, 'ctime desc', 'is_active = 1');
		$data = array(
			'title' => $content[0]['news_title'],
			'info' => $content[0]['news_info'],
			'source' => $content[0]['news_source'],
			'time' => date('Y-m-d', $content[0]['ctime']),
			'sider' => $sider,
		);
		$this->load->view('graduation/news_detail.html', $data);
	}
}
