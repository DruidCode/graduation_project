<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	function __construct() {
        parent :: __construct();
        $this->load->helper(array('url'));
		$this->load->config('admin');
		$this->conf = $this->config->item('upload_conf');
        $this->load->model('api/api_model', 'api');
        $this->load->model('graduation/admin_model', 'admin');
		session_start();
	}

	public function index() {
		$this->load->view('graduation/admin/login.html');
	}

	#判断是否登陆
	private function is_login() {
		if ( !isset($_SESSION['uid']) || empty($_SESSION['uid']) ) {
			return false;
		} else {
			return true;
		}
	}

	#登陆
	public function login() {
        $username  = trim($this->input->get_post('username', true));
        $password  = trim($this->input->get_post('password', true));

        $info = $this->admin->selectBy('admin', array('username'=>$username));
        if (empty($info) || $info[0]['password'] != md5($password) ) {
            $this->api->xNetOut(1, '账号密码错误', '', 'default');
			return;
        } else {
			$_SESSION['uid'] = $info[0]['id']; 
			$_SESSION['name'] = $info[0]['username']; 
            $this->api->xNetOut(0, '', site_url('admin/commend'), 'default');
        }
	}

	#退出
	public function logout() {
		if ( $this->is_login() ) {
			$_SESSION = array();
		}
		session_destroy();
		redirect('admin/index');
	}

	public function commend() {
		if ( !$this->is_login() ) {
			redirect('admin/index');
		}
		$data = array(
			'commendurl' => site_url('admin/add_commend'),
		);
		$this->load->view('graduation/admin/commend.html', $data);
	}

	#添加表彰
	public function add_commend() {
		if ( !$this->is_login() ) {
			redirect('admin/index');
		}
        $name  = trim($this->input->get_post('name', true));
        $brief  = trim($this->input->get_post('brief', true));
        $detail  = trim($this->input->get_post('detail', true));
        $active  = $this->input->get_post('is_active', true);
        $cid  = $this->input->get_post('cid', true);
        $avatar_def  = $this->input->get_post('avatar_def', true);

		$input = 'avatar';
		$maxsize = 8*1024*1024;
		$type   = $_FILES[$input]['type'];
        $size   = $_FILES[$input]['size'];
        $tmp_name = $_FILES[$input]['tmp_name'];
        $src_name = $_FILES[$input]['name'];

		$path = $this->conf['upload_path'];
		$date = date('Ymd');
		if ( !is_dir($path . $date) ) mkdir($path.$date);

		if ( !empty($size) ) {
				if ($type != "image/jpeg"
					 && $type != "image/pjpeg"
					 && $type != "image/x-png"
					 && $type != "image/png"
					 && $type != "image/gif") {
					 $code = 4;
					 $this->api->xNetOut($code, '上传图片格式错误', '', 'default');
					 return;
				 }
				 if ($_FILES[$input]['error'] > 0) {
					 $code = 1;
					 $this->api->xNetOut($code, '上传错误', '', 'default');
					 return;
				 }
				 if ($size > $maxsize) {
					 $code = 2;
					 $this->api->xNetOut($code, '文件大小大于8M', '', 'default');
					 return;
				 }
				 $new_name = $this->create_name();
				 $ext = $this->get_suffix($src_name); //后缀
				 $filename = $ext ? $new_name . '.' . $ext : $new_name;
				 $mv_re    = move_uploaded_file($tmp_name, $path . $date . '/' . $filename);
				$file_path = base_url() . $this->conf['web_url'] . $date . '/' . $filename;
		 } else if ( !empty($avatar_def) ) {
		 	$file_path = $avatar_def;
		 }
		$data = array(
			'img' => $file_path,
			'name' => $name,
			'brief' => $brief,
			'detail' => $detail,
			'is_active' => $active,
			'ctime' => time(),
		);
		if (empty($cid)) { #新增
			$this->admin->add_commend($data);
		} else { #修改
			$this->admin->updateBy('commend', array('id'=>$cid), $data);
		}
		redirect('admin/commend_list');
	}

	public function commend_detail() {
		if ( !$this->is_login() ) {
			redirect('admin/index');
		}
        $cid  = trim($this->input->get_post('cid', true));
		if (empty($cid)) {
			$this->load->view('graduation/admin/commend.html');
			return;
		}
		$re = $this->admin->selectBy('commend', array('id'=>$cid));
		$data = array(
			'name' => $re[0]['name'],
			'brief' => $re[0]['brief'],
			'detail' => $re[0]['detail'],
			'is_active' => $re[0]['is_active'],
			'cid' => $cid,
			'img' => $re[0]['img'],
		);
		$this->load->view('graduation/admin/commend.html', $data);
	}

	
	//创建唯一名字
    private function create_name()
    {
        $date = date('YmdHis');
        $rand = md5(uniqid(mt_rand()));
        $name = $date . '-' . substr($rand, 0, 8);
        return $name;
    }

	//获取文件后缀名
   private function get_suffix($filename)
   {
       if (empty($filename)) return false;
       $name_array = explode('.', $filename);
       if (count($name_array) == 1) return false;
       $names = array_reverse($name_array);
       //$ext = $this->conf['allowed_ext'];
       $ext_name = $names[0];
       //if (in_array($names[0], $ext)) $ext_name = 'jpg';
       return $ext_name;
   }

	//#表彰列表
	public function commend_list()
	{
		if ( !$this->is_login() ) {
			redirect('admin/index');
		}
		//获取留言列表
		$total = $this->admin->get_commend_total();
		$per = 5;
		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/commend_list');
		$config['total_rows'] = $total;
		$config['per_page'] = $per;
		$list = $this->admin->get_commend_list($per, $this->uri->segment(3));
		$this->pagination->initialize($config);
		$page = $this->pagination->create_links();
		$data = array(
			'list' => $list,
			'page' => $page,
		);
		$this->load->view('graduation/admin/commendlist.html', $data);
	}

	#添加活动
	public function act()
	{
		if ( !$this->is_login() ) {
			redirect('admin/index');
		}
		$this->load->view('graduation/admin/act.html');
	}

	public function add_act()
	{
		if ( !$this->is_login() ) {
			redirect('admin/index');
		}
        $act_name  = trim($this->input->get_post('act_name', true));
        $act_info  = trim($this->input->get_post('act_info', true));
        $act_route  = trim($this->input->get_post('act_route', true));
        $begin_time  = trim($this->input->get_post('begin_time', true));
        $end_time  = trim($this->input->get_post('end_time', true));
        $sign_time  = trim($this->input->get_post('sign_time', true));
        $is_active  = trim($this->input->get_post('is_active', true));
        $aid  = $this->input->get_post('aid', true);
        $avatar_def  = $this->input->get_post('avatar_def', true);

		$data = array();
		$data['act_img'] = '';
		if ( !empty($_FILES['avatar']['name']) ) {
				$input = 'avatar';
				$maxsize = 8*1024*1024;
				$type   = $_FILES[$input]['type'];
				$size   = $_FILES[$input]['size'];
				$tmp_name = $_FILES[$input]['tmp_name'];
				$src_name = $_FILES[$input]['name'];

				$path = $this->conf['upload_path'];
				$date = date('Ymd');
				if ( !is_dir($path . $date) ) mkdir($path.$date);

				if ($type != "image/jpeg"
					 && $type != "image/pjpeg"
					 && $type != "image/x-png"
					 && $type != "image/png"
					 && $type != "image/gif") {
					 $code = 4;
					 $this->api->xNetOut($code, '上传图片格式错误', '', 'default');
					 return;
				 }
				 if ($_FILES[$input]['error'] > 0) {
					 $code = 1;
					 $this->api->xNetOut($code, '上传错误', '', 'default');
					 return;
				 }
				 if ($size > $maxsize) {
					 $code = 2;
					 $this->api->xNetOut($code, '文件大小大于8M', '', 'default');
					 return;
				 }
				 $new_name = $this->create_name();
				 $ext = $this->get_suffix($src_name); //后缀
				 $filename = $ext ? $new_name . '.' . $ext : $new_name;
				 $mv_re    = move_uploaded_file($tmp_name, $path . $date . '/' . $filename);
			if ($mv_re) { #上传成功
				$file_path = base_url() . $this->conf['web_url'] . $date . '/' . $filename;
				$data['act_img'] = $file_path;
			}
		} else if ( !empty($avatar_def) ) {
			$data['act_img'] = $avatar_def;
		}
		$data['act_name'] = $act_name;
		$data['act_info'] = $act_info;
		$data['act_route'] = $act_route;
		$data['begin_time'] = strtotime($begin_time);
		$data['end_time'] = strtotime($end_time);
		$data['sign_time'] = strtotime($sign_time);
		$data['is_active'] = intval($is_active);
		$data['ctime'] = time();
		if ( empty($aid) ) { #添加
			$this->admin->add_data('act', $data);
		} else {
			$this->admin->updateBy('act', array('id'=>$aid), $data);
		}
		redirect('admin/act_list');
	}

	#活动列表
	public function act_list()
	{
		if ( !$this->is_login() ) {
			redirect('admin/index');
		}
		//获取留言列表
		$total = $this->admin->get_total('act', 'is_active = 1');
		$per = 5;
		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/act_list');
		$config['total_rows'] = $total;
		$config['per_page'] = $per;
		$list = $this->admin->get_list('act', $per, $this->uri->segment(3), 'ctime desc');
		$this->pagination->initialize($config);
		$page = $this->pagination->create_links();
		$data = array(
			'list' => $list,
			'page' => $page,
		);
		$this->load->view('graduation/admin/actlist.html', $data);
	}

	public function act_detail()
	{
		if ( !$this->is_login() ) {
			redirect('admin/index');
		}
        $aid  = trim($this->input->get_post('aid', true));
		if (empty($aid)) {
			$this->load->view('graduation/admin/act.html');
			return;
		}
		$re = $this->admin->selectBy('act', array('id'=>$aid));
		$data = array(
			'act_name' => $re[0]['act_name'],
			'act_info' => $re[0]['act_info'],
			'act_route' => $re[0]['act_route'],
			'is_active' => $re[0]['is_active'],
			'begin_time' => date('Y-m-d H:i:s', $re[0]['begin_time']),
			'end_time' => date('Y-m-d H:i:s', $re[0]['end_time']),
			'sign_time' => date('Y-m-d H:i:s', $re[0]['sign_time']),
			'aid' => $aid,
			'img' => $re[0]['act_img'],
		);
		$this->load->view('graduation/admin/act.html', $data);
	}

	public function news()
	{
		if ( !$this->is_login() ) {
			redirect('admin/index');
		}
		$this->load->view('graduation/admin/news.html');
	}

	public function news_list()
	{
		if ( !$this->is_login() ) {
			redirect('admin/index');
		}
		//获取新闻列表
		$total = $this->admin->get_total('news', 'is_active = 1');
		$per = 5;
		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/news_list');
		$config['total_rows'] = $total;
		$config['per_page'] = $per;
		$list = $this->admin->get_list('news', $per, $this->uri->segment(3), 'ctime desc');
		$this->pagination->initialize($config);
		$page = $this->pagination->create_links();
		$data = array(
			'list' => $list,
			'page' => $page,
		);
		$this->load->view('graduation/admin/newslist.html', $data);
	}

	public function news_detail()
	{
		if ( !$this->is_login() ) {
			redirect('admin/index');
		}
        $nid  = trim($this->input->get_post('nid', true));
		if (empty($nid)) {
			$this->load->view('graduation/admin/news.html');
			return;
		}
		$re = $this->admin->selectBy('news', array('id'=>$nid));
		$data = array(
			'news_title' => $re[0]['news_title'],
			'news_source' => $re[0]['news_source'],
			'news_info' => $re[0]['news_info'],
			'is_active' => $re[0]['is_active'],
			'img' => $re[0]['img'],
			'ctime' => time(),
			'nid' => $nid,
		);
		$this->load->view('graduation/admin/news.html', $data);
	}

	public function add_news()
	{
		if ( !$this->is_login() ) {
			redirect('admin/index');
		}
        $news_title  = trim($this->input->get_post('news_title', true));
        $news_source  = trim($this->input->get_post('news_source', true));
        $news_info  = trim($this->input->get_post('news_info', true));
        $active  = $this->input->get_post('is_active', true);
        $nid  = $this->input->get_post('nid', true);
        $avatar_def  = $this->input->get_post('avatar_def', true);

		$data = array();
		$data['img'] = '';
		if ( !empty($_FILES['avatar']['name']) ) {
				$input = 'avatar';
				$maxsize = 8*1024*1024;
				$type   = $_FILES[$input]['type'];
				$size   = $_FILES[$input]['size'];
				$tmp_name = $_FILES[$input]['tmp_name'];
				$src_name = $_FILES[$input]['name'];

				$path = $this->conf['upload_path'];
				$date = date('Ymd');
				if ( !is_dir($path . $date) ) mkdir($path.$date);

				if ($type != "image/jpeg"
					 && $type != "image/pjpeg"
					 && $type != "image/x-png"
					 && $type != "image/png"
					 && $type != "image/gif") {
					 $code = 4;
					 $this->api->xNetOut($code, '上传图片格式错误', '', 'default');
					 return;
				 }
				 if ($_FILES[$input]['error'] > 0) {
					 $code = 1;
					 $this->api->xNetOut($code, '上传错误', '', 'default');
					 return;
				 }
				 if ($size > $maxsize) {
					 $code = 2;
					 $this->api->xNetOut($code, '文件大小大于8M', '', 'default');
					 return;
				 }
				 $new_name = $this->create_name();
				 $ext = $this->get_suffix($src_name); //后缀
				 $filename = $ext ? $new_name . '.' . $ext : $new_name;
				 $mv_re    = move_uploaded_file($tmp_name, $path . $date . '/' . $filename);
			if ($mv_re) { #上传成功
				$file_path = base_url() . $this->conf['web_url'] . $date . '/' . $filename;
				$data['img'] = $file_path;
			}
		} else if ( !empty($avatar_def) ) {
			$data['img'] = $avatar_def;
		}
		$data['news_title'] = $news_title;
		$data['news_source'] = $news_source;
		$data['news_info'] = $news_info;
		$data['is_active'] = $active;
		$data['ctime'] = time();
		if (empty($nid)) { #新增
			$this->admin->add_data('news', $data);
		} else { #修改
			$this->admin->updateBy('news', array('id'=>$nid), $data);
		}
		redirect('admin/news_list');
	}

	//留言
	public function guestbook_list()
	{
		if ( !$this->is_login() ) {
			redirect('admin/index');
		}
		$total = $this->admin->get_total('guestbook', 'is_active = 1');
		$per = 5;
		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/guestbook_list');
		$config['total_rows'] = $total;
		$config['per_page'] = $per;
		$list = $this->admin->get_list('guestbook', $per, $this->uri->segment(3), 'ctime desc', array('is_active'=>1));
		$this->pagination->initialize($config);
		$page = $this->pagination->create_links();
		$data = array(
			'list' => $list,
			'page' => $page,
			'del' => site_url('admin/del_guestbook'),
		);
		$this->load->view('graduation/admin/guestbooklist.html', $data);
	}

	public function guestbook()
	{
		if ( !$this->is_login() ) {
			redirect('admin/index');
		}
        $gid  = trim($this->input->get_post('gid', true));
		if (empty($gid)) {
			$this->load->view('graduation/admin/guestbooklist.html');
			return;
		}
		$re = $this->admin->selectBy('guestbook', array('id'=>$gid));
		$reply = $this->admin->selectBy('guestbook_reply', array('gid'=>$gid));
		$data = array(
			'name' => $re[0]['name'],
			'email' => $re[0]['email'],
			'text' => $re[0]['text'],
			'ctime' => date('Y-m-d H:i:s', $re[0]['ctime']),
			'gid' => $gid,
			'reply' => $reply[0]['text'],
		);
		$this->load->view('graduation/admin/guestbook.html', $data);
	}

	public function guestbook_edit()
	{
		if ( !$this->is_login() ) {
			redirect('admin/index');
		}
        $gid  = trim($this->input->get_post('gid', true));
        $text = trim($this->input->get_post('text', true));
        $reply = trim($this->input->get_post('reply', true));

		$data = array(
			'text' => $text,
		);
		$this->admin->updateBy('guestbook', array('id'=>$gid), $data);
		$re = $this->admin->selectBy('guestbook_reply', array('gid'=>$gid));
		if ( empty($re) ) {
			$this->admin->add_data('guestbook_reply', array('gid'=>$gid, 'text'=>$reply, 'ctime'=>time()));
		} else {
			$this->admin->updateBy('guestbook_reply', array('gid'=>$gid), array('text'=>$reply, 'ctime'=>time()));
		}
		redirect('admin/guestbook_list');
	}

	#报名列表
	public function sign_list()
	{
		if ( !$this->is_login() ) {
			redirect('admin/index');
		}
        $aid  = trim($this->input->get_post('aid', true));
		$total = $this->admin->get_total('sign', 'act_id = ' . $aid);
		$per = 5;
		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/sign_list');
		$config['total_rows'] = $total;
		$config['per_page'] = $per;
		$list = $this->admin->get_list('sign', $per, $this->uri->segment(3), 'register_time desc', array('act_id'=>$aid, 'check_status'=>1) );
		if (!empty($list)) {
		foreach ($list as &$li) {
			switch ($li['invoice_type']) {
				case '0':
					$li['invoice_type'] = '暂不捐款';
					break;
				case '1':
					$li['invoice_type'] = '需要发票';
					break;
				case '2':
					$li['invoice_type'] = '不需要发票';
					break;
			}
		}
		}
		$this->pagination->initialize($config);
		$page = $this->pagination->create_links();
		$data = array(
			'list' => $list,
			'page' => $page,
			'export' => site_url('admin/export_excel') . '?aid='.$aid,
		);

		$this->load->view('graduation/admin/signlist.html', $data);
	}

	public function export_excel()
	{
        $aid  = trim($this->input->get_post('aid', true));
		$list = $this->admin->get_list('sign', 10000000, $this->uri->segment(3), '', array('act_id'=>$aid, 'check_status'=>1));

		$this->load->library('excel');
		$excel = new Excel();
		$filename = '活动'.$aid. '-'.date('Ymd');
		$data = array();
		$data['header'] = array('id', '姓名', '手机号', '验证码', '邮箱','捐款','发票开头', '报名时间');
		$data['rows'] = array();
		foreach ($list as $li) {
			switch ($li['invoice_type']) {
				case '0':
					$li['invoice_type'] = '暂不捐款';
					break;
				case '1':
					$li['invoice_type'] = '需要发票';
					break;
				case '2':
					$li['invoice_type'] = '不需要发票';
					break;
			}
			$data['rows'][] =  array($li['id'], $li['uname'], $li['mobile'], $li['vcode'], $li['email'], $li['invoice_type'], $li['invoice_title'], $li['register_time']);
		}
		$excel->write($filename, $data['header'], $data['rows']);
	}

	public function del_guestbook() {
		$id = $this->input->get_post('id' , TRUE);

		if (empty($id)) {
			$this->api->xNetOut(1, '删除失败');
			return;
		}

		$this->admin->updateBy('guestbook', array('id'=>$id), array('is_active'=>0));
		$this->api->xNetOut(0, '成功删除', site_url('guestbook'));
	}

	public function del_act() {
		$id = $this->input->get_post('id' , TRUE);

		if (empty($id)) {
			$this->api->xNetOut(1, '删除失败');
			return;
		}

		$this->admin->updateBy('sign', array('id'=>$id), array('check_status'=>0));
		$this->api->xNetOut(0, '成功删除');
	}
}
