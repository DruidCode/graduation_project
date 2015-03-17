<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * 活动报名
 *
 *
 *
 */

class Sign extends CI_Controller
{
    function __construct()
    {
        parent :: __construct();
        $this->load->model('graduation/sign_model', 'sign');
        $this->load->model('graduation/admin_model', 'admin');
        $this->load->model('api/api_model', 'api');
        $this->load->config('sign');
        $this->load->helper(array('url', 'curl', 'sm', 'web_api'));
    }

    public function index()
    {
		$id = $this->input->get_post('aid', true);
		$re = $this->admin->selectBy('act', array('id'=>$id));
		$data = array(
			'act' => $re[0],
		);
		if ($re[0]['sign_time'] < time()) {
			$this->load->view('graduation/close.html', $data);
			return;
		}
		$this->load->view('graduation/loginpage.html', $data);
    }

	//获取验证码
    public function send_code()
    {
        $mobile = $this->input->post('mobile', true);
		$id = $this->input->get_post('aid', true);
        $mobile = trim($mobile);
        if ( !$this->api->checkTel($mobile) ) return $this->api->xNetOut('1', '手机格式不正确', '', 'default');

		//是否登陆过
        $record = $this->sign->selectBy('sign', array('mobile'=>$mobile, 'act_id'=>$id));
        if (empty($record)) {
            $vcode = $this->hasVc();
        } else {
            $vcode = $record[0]['vcode'];
        }
        $insertData = array(
            'act_id' => $id,
            'mobile' => $mobile,
            'vcode'  => $vcode,
            'check_status'  => 1,
            'submit_time' => date('Y-m-d H:i:s'), //获取验证码时间
        );
        $in = $this->replaceData('sign', array('mobile'=>$mobile, 'act_id'=>$id), $insertData);
        if ($in) {
			//发送验证码
            return $this->api->xNetOut('0', '发送成功,请注意查收手机短信，并填写短信中的邀请码'.$vcode, '', 'default');
        } else {
            log_message('info', 'mobile ' . $mobile . ' intoTable failed');
            return $this->api->xNetOut('2', '获取失败,请重新获取', '', 'default');
        }
    }

    //验证码登陆
	//根据手机号判断用户类型
    public function login()
    {
        $mobile = trim($this->input->get_post('mobile', true));
        $vcode  = trim($this->input->get_post('vercode', true));
        $aid  = trim($this->input->get_post('aid', true));

		log_message('info', 'the mobile ' . $mobile . ' try to login');
        if ( !$this->api->checkTel($mobile) ) {
            $code = 1;
            $msg  = '手机号码格式不对';
            $this->api->xNetOut($code, $msg, '', 'default');
            return;
        }
        $record = $this->sign->checkLogin($mobile, $vcode, $aid);
        if ( empty($record) ) {
            $code = 2;
            $msg  = '请检查您的手机号和验证码是否正确';
            $this->api->xNetOut($code, $msg, '', 'default');
            return;
        }
		log_message('info', 'the mobile ' . $mobile . ' login succeed');
		$uid = $record[0]['id'];
		
		$step = $record['0']['register_step'] ? $record['0']['register_step'] : 0;
		//用cookie存登陆信息
		$this->update_cookie(REGISTER_COOKIE, array(UID_COOKIE=>$uid, STEP_COOKIE=>$step, AID_COOKIE=>$aid));
		/*
		#未注册
		if ( empty($record[0]['uname']) ) {
            $this->api->xNetOut(0, '', site_url('sign/register'), 'default');
			return;
		}
		*/

		# 判断跳到哪个页面
     	$sign_step = $this->config->item('sign_step');
        return $this->api->xNetOut('0', '', site_url('sign').'/'.$sign_step[ $step + 1 ], 'default');
    }

	//基本信息页
	public function basic()
	{
		$check = $this->check_login();
		if (empty($check)) {
        	$this->load->view('graduation/loginpage.html');
			return;
		}
		$uid = $check[UID_COOKIE];
		//是否已经填写过
		$data = array();
        $info = $this->sign->selectBy('sign', array('id'=>$uid));
		if ($info[0]['mobile']) { //填写过
			$data['name']    = $info[0]['uname'];
			$data['mobile']  = $info[0]['mobile'];
			$data['email']   = $info[0]['email'];
        	$this->load->view('graduation/insidersign.html', $data);
			return;
		} else { //未填写过
        	$this->load->view('graduation/insidersign.html');
			return;
		}
	}

	//基本信息页点下一步，JS调用
	//更新信息到数据库
	public function basic_into()
	{
		$check = $this->check_login();
		if (empty($check)) {
        	$this->api->xNetOut('0', '', site_url('sign/index'), 'default');
			return;
		}
        $name 	  = trim($this->input->get_post('name', true));
        $mobile   = trim($this->input->get_post('mobile', true));
        $email    = trim($this->input->get_post('email', true));
        $aid  = trim($this->input->get_post('aid', true));
        if ( empty($name) ) return $this->api->xNetOut('3', '姓名不能为空', '', 'default');
        if ( !$this->api->checkTel($mobile) ) return $this->api->xNetOut('6', '手机格式不正确', '', 'default');
		$insert_data = array(
        	'uname'    => $name,
        	'mobile'   => $mobile,
        	'email'    => $email,
        	'register_step'  => 1,
        	'register_time' => date('Y-m-d H:i:s'),
		);
        $re = $this->replaceData('sign', array('id'=>$check[UID_COOKIE]), $insert_data);
        if ($re) {
			$this->update_cookie(REGISTER_COOKIE, array(UID_COOKIE=>$check[UID_COOKIE], STEP_COOKIE=>1, AID_COOKIE=>$check[AID_COOKIE]));
        	$this->api->xNetOut('0', '', site_url('sign/route'), 'default');
			return;
		} else {
        	log_message('info', 'mobile ' . $mobile . ' submit basic failed');
        	$this->api->xNetOut('1', '提交失败', '', 'default');
		}
	}

	//行程页
	public function route()
	{
		$check = $this->check_login();
		if (empty($check)) {
        	$this->load->view('graduation/loginpage.html');
			return;
		}
        $info = $this->sign->selectBy('sign', array('id'=>$check[UID_COOKIE]));
        $act = $this->sign->selectBy('act', array('id'=>$check[AID_COOKIE]));
		#判断行程剩余
		$route_num = $this->sign->get_route(2, 40);
		$is_valid = true;
		if(!$route_num){
			$is_valid = false;
		}
		$data = array();
		$data['route'] = array();
		$data['is_valid'] = $is_valid;
		$data['route'] = str_replace(chr(10), '<br/>', $act[0]['act_route']);
        $this->load->view('graduation/insidersch.html', $data);
		return;
	}

	//行程更新到数据库
	public function route_into()
	{
		$check = $this->check_login();
		if (empty($check)) {
        	$this->load->view('graduation/loginpage.html');
			return;
		}
		$route = $this->input->get_post('route');
        $data['route'] = json_encode($route);
        $data['register_step'] = 2;
        $data['register_time'] = date('Y-m-d H:i:s');
        $re = $this->replaceData('sign', array('id'=>$check[UID_COOKIE]), $data);
        if ($re) {
			$this->update_cookie(REGISTER_COOKIE, array(UID_COOKIE=>$check[UID_COOKIE], STEP_COOKIE=>2, AID_COOKIE=>$check[AID_COOKIE]));
        	redirect("sign/invoice");
			return;
		} else {
        	$this->api->xNetOut('1', '提交失败', '', 'default');
		}
	}

	//显示发票信息
	public function invoice()
	{
		$check = $this->check_login();
		if (empty($check)) {
        	$this->load->view('graduation/loginpage.html');
			return;
		}
		$data = array();
		$data['money'] = USER_COST . '元';
        $info = $this->sign->selectBy('sign', array('id'=>$check[UID_COOKIE]));
		$data['invoice_type'] = $info[0]['invoice_type'];
		$data['invoice_title'] = empty($info[0]['invoice_type']) ? '' : $info[0]['invoice_title'];

		$this->load->view('graduation/insiderinvoice.html', $data);
	}

	//发票更新到数据库
	public function invoice_into()
	{
		$check = $this->check_login();
		if (empty($check)) {
        	$this->load->view('graduation/loginpage.html');
			return;
		}
		$data = array();
		$data['invoice_type'] = trim($this->input->get_post('invoice_type', true));
        $data['register_step'] = 3;
        $data['register_time'] = date('Y-m-d H:i:s');
		if ( $data['invoice_type'] == 1 ) {
			$data['invoice_title'] = trim($this->input->get_post('invoice_title', true));
		}
        $re = $this->replaceData('sign', array('id'=>$check[UID_COOKIE]), $data);
        if ($re) {
			$this->update_cookie(REGISTER_COOKIE, array(UID_COOKIE=>$check[UID_COOKIE], STEP_COOKIE=>3, AID_COOKIE=>$check[AID_COOKIE]));
        	redirect('sign/confirm');
		} else {
        	log_message('info', 'mobile ' . $mobile . ' submit basic failed');
        	$this->api->xNetOut('1', '提交失败', '', 'default');
		}
	}

	//确认页
	public function confirm()
	{
		$check = $this->check_login();
		if (empty($check)) {
        	$this->load->view('graduation/loginpage.html');
			return;
		}
		$uid = $check[UID_COOKIE];
        $info = $this->sign->selectBy('sign', array('id'=>$uid));
        $act = $this->sign->selectBy('act', array('id'=>$check[AID_COOKIE]));

		$view = $this->uri->segment(3) == 'view' ? true : false;
		$data = array();
		$data['info'] = $info[0];
		$data['route'] = str_replace(chr(10), '<br/>', $act[0]['act_route']);
		//$data['status'] = $status;
        $this->load->view('graduation/insiderconfirm.html', $data);
	}
	
	//确认报名/支付
	public function confirm_sign(){
		$check = $this->check_login();
		if (empty($check)) {
        	$this->load->view('graduation/loginpage.html');
			return;
		}
		$uid = $check[UID_COOKIE];
		//跳转成功页面
		redirect("sign/sign_success");
	}
	
	//TODO 报名成功
	public function sign_success(){
		$this->load->view('graduation/insiderdone.html');
	}

	//非会员
	public function unsign_success(){
		$this->load->view('graduation/outsidersign.html');
	}
	

    //注册页面
	public function register()
	{
		$check = $this->check_login();
		if ( empty($check) ) {
			redirect('sign/index');
			return;
		}
        $record = $this->sign->selectBy('sign', array('id'=>$check['uid']));
		$mobile = $record[0]['mobile'];
		$data = array(
			'mobile' => $mobile,
		);
        $this->load->view('graduation/register.html', $data);
	}

    //注册
    public function register_into()
    {
        $name    = trim($this->input->get_post('name', true));
        $mobile  = trim($this->input->get_post('mobile', true));
        $email    = trim($this->input->get_post('email', true));

        if ( !$this->api->checkEmail($email) ) return $this->api->xNetOut('7', '邮箱格式不正确', '', 'default');
        if ( !$this->api->checkTel($mobile) ) return $this->api->xNetOut('6', '手机格式不正确', '', 'default');

        
        $insertData = array(
            'uname'    => $name,
            'mobile'   => $mobile,
            'email'     => $email,
            'submit_time' => date('Y-m-d H:i:s'),
        );

       	$re = $this->replaceData('sign', array('mobile'=>$mobile), $insertData);
        log_message('info', 'mobile '.$mobile . 'sign result='.var_export($re,true));
        if ($re) {
            $this->api->xNetOut('0', '', site_url('sign/basic'), 'default');
			return;
        } else {
            log_message('info', 'mobile ' . $mobile . ' submit failed');
            $this->api->xNetOut('9', '提交失败', '', 'default');
			return;
        }
    }

	//生成验证码
    private function hasVc()
    {
        $vc = mt_rand(100000, 999999);
        return $vc;
    }

    private function replaceData($table, $where, $data)
    {
		if (empty($where)) {
			$select = false;
		} else {
        	$select = $this->sign->selectBy($table, $where);
		}
        if ($select) {//有记录就更新
            $update = $this->sign->updateBy($table, $where, $data);
            if (mysql_error()) {
                log_message('info', var_export($where,true) . 'update data '.var_export($data,true) . ' error='.mysql_error());
                return false;
            }
            return $select[0]['id'];
        } else {//无记录就新增
            $last_id = $this->sign->intoTable($table, $data);
            if ($last_id < 0) {
                log_message('info', var_export($where,true) . 'insert data '.var_export($data,true) . ' error='.mysql_error());
                return false;
            }
            return $last_id;
        }
    }

	//判断是否登陆
    private function check_login()
    {
		//TODO 判断cookie  还有客户端登陆判断
		$cookie = $this->update_cookie(REGISTER_COOKIE); 
		return $cookie;
    }

	//更新cookie
	private function update_cookie($name, $value='')
	{
		#获取cookie
		if ( empty($value) ) {
			$cookie = $this->api->cookie($name, $value); 
			$cookie = base64_decode($cookie);
			$cookie = unserialize($this->api->str_encrypt($cookie, COOKIE_PASSWARD));
			return $cookie;
		}
		#设置cookie
		$cookie_value = $this->api->str_encrypt( serialize($value), COOKIE_PASSWARD);
		$this->api->cookie($name, base64_encode($cookie_value) );
	}
}
