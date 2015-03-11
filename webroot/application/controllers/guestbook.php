<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Guestbook extends CI_Controller {

	function __construct() {
        parent :: __construct();
        $this->load->helper(array('url'));
		$this->load->model("api/api_model","api");
		$this->load->model("graduation/guestbook_model","guestbook");
		session_start();
	}

	public function index() {
		$verifyCode = site_url('guestbook/getCode/100/24');
		$addUrl     = site_url('guestbook/add_guestbook');
		//获取留言列表
		$total = $this->guestbook->get_guest_total();
		$per = 5;
		$this->load->library('pagination');
		$config['base_url'] = site_url('guestbook/index');
		$config['total_rows'] = $total;
		$config['per_page'] = $per;
		$list = $this->guestbook->get_guest_list($per, $this->uri->segment(3));
		$this->pagination->initialize($config);
		$page = $this->pagination->create_links();
		$data = array(
			'code' => $verifyCode,
			'add' => $addUrl,
			'list' => $list,
			'page' => $page,
		);
		$this->load->view('graduation/guestbook.html', $data);
	}

	#生称验证码图片
	public function getCode($w, $h) {
		$im = imagecreate($w, $h);
    	$red = imagecolorallocate($im, 255, 0, 0);
    	$white = imagecolorallocate($im, 255, 255, 255);
  
    	$num1 = rand(1, 20);
    	$num2 = rand(1, 20);
  
    	$_SESSION['grad_code'] = $num1 + $num2;
  
    	$gray = imagecolorallocate($im, 118, 151, 199);
    	$black = imagecolorallocate($im, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
  
    	//画背景
    	imagefilledrectangle($im, 0, 0, 100, 24, $black);
    	//在画布上随机生成大量点，起干扰作用;
    	for ($i = 0; $i < 80; $i++) {
        	imagesetpixel($im, rand(0, $w), rand(0, $h), $gray);
    	}
  
    	imagestring($im, 5, 5, 4, $num1, $red);
    	imagestring($im, 5, 30, 3, "+", $red);
    	imagestring($im, 5, 45, 4, $num2, $red);
    	imagestring($im, 5, 70, 3, "=", $red);
    	imagestring($im, 5, 80, 2, "?", $white);
  
    	header("Content-type: image/jpeg");
    	imagejpeg($im);
    	imagedestroy($im);
	}

	#留言
	public function add_guestbook() {
		$name = $this->input->get_post('name' , TRUE);
		$email = $this->input->get_post('email' , TRUE);
		$text = $this->input->get_post('content' , TRUE);
		$vcode = $this->input->get_post('code' , TRUE);

		if ( empty($vcode) || $vcode != $_SESSION['grad_code']) {
			$this->api->xNetOut(1, '验证码错误');
			return;
		}

		$data = array (
			'name' => $name,
			'email' => $email,
			'text' => $text,
			'ctime' => time(),
		);
		$re = $this->guestbook->add_guestbook($data);
		if ($re > 0) {
			$this->api->xNetOut(0, '留言成功');
			return;
		} else {
			$this->api->xNetOut(1, '留言失败');
			return;
		}
	}
}
