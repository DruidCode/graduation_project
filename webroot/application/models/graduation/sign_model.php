<?php
if(!defined('BASEPATH'))  exit('No direct script access allowed');

class Sign_model extends CI_model {
    var $sign_fileds; 
    var $with_child_fileds;

	function __construct() {
		parent :: __construct();
        $this->load->config('sign');
        $this->load->helper(array('web_api','sm', 'curl'));
        $this->load->model('api/api_model', 'api');
        web_api_load();
        sm_api_load();
		$this->load->database('graduation');
	}

	#判断行程
	#行程id，限额
	public function get_route($route_id, $num)
	{
		$sql = "select id,uname,mobile,route from sign where route is not null";
        $query = $this->db->query($sql);
        $result =  $query->result_array();
		$route_num = 0;
		foreach ($result as $res) {
			$route = json_decode($res['route']);
			if (empty($route)) continue;
			if (in_array($route_id, $route)) $route_num++;
		}
		if ($route_num >= $num) return false;
		return true;
	}

	#获取用户类型
	public function get_user_type($mobile)
	{
		$info = get_user_info($mobile);
		if ($this->is_vip($mobile)) {
			return VIP_USER;
		} else if (empty($info) || $info['user_is_ding'] == 0) {
			#手机号无法获取数据信息，查看本地库
        	$user = $this->selectBy('sign', array('mobile'=>$mobile));
			if ( empty($user) || empty($user[0]['usertype'])) {
				return NON_USER;
			} else if ($user[0]['usertype'] == 1) {
				return NOMAL_USER;
			} else if ($user[0]['usertype'] == 2) {
				return VIP_USER;
			} else if ($user[0]['usertype'] == 3) {
				return NON_USER;
			}
			return NON_USER;
		} else {
			return NOMAL_USER;
		}
	}

	//是否免费岛民
	public function is_vip($mobile)
	{
		if (!file_exists(dirname(dirname(dirname(__file__))).'/config/vip.txt')) {
			log_message('info', 'read vip error');
			return false;
		}
		$file = file(dirname(dirname(dirname(__file__))).'/config/vip.txt', FILE_IGNORE_NEW_LINES);
		if (in_array($mobile, $file)) return true;
		return false;
	}

    //获取岛民信息
	#type 旧版客户端、新版客户端
    public function getUserInfo($mobile)
    {
		//TODO 先判断是否免费uid
        $info = get_user_info($mobile);
        if (empty($info)) return false;
        return $info;
    }

    #注册新用户
    public function register($arr){
        if(false != ($result = $this->getUserInfo($arr['mobile']))){
                return $result['uid'];
        }
        $this->load->helper('curl');
        $post = array(
            'app_key'   => ACT_DB_APP_KEY,
            'login_type' => 'mobile',
            'login_mobile' => (string)$arr['mobile'],
            'mobile' => (string)$arr['mobile'],
            'password' => rand(100000, 999999),
            'uname' =>  (string)$arr['uname'],
            'nick_name' => (string)$arr['uname'],
            'com_name' => (string)$arr['company'],
            'position'  => (string)$arr['title'],
            'login_email' => (string)$arr['email'],
            'email' => (string)$arr['email'],
            'user_class' => '1',
            'user_is_ding' => 0,
            'user_type' => 0,
            'client_uid' => time(),
            'uname_pinyin' => $this->get_pin_yin((string)$arr['uname']),
        );
        $result = curl_get_post(ACT_DB_API_URL.'/api/user/add', $post);
        log_message('info', '/api/user/add url:'. ACT_DB_API_URL.'/api/user/add' . 'param :'.var_export($post, true).'sign result='.var_export($result,true));
        $result = json_decode($result, true);
        if(is_array($result) && (200 == $result['code'])) return $result['uid'];
        if(is_array($result) && (300 == $result['code'])) return -1; //已经注册
        return false;
    }

    public function get_pin_yin($_str){
            $path = dirname(__FILE__).'/pinyin.txt';
            $str_array = file($path);
            $map = array();
            foreach($str_array as $key=>$val){
                    $val = explode(',', $val);
                    $map[trim($val[0])] = trim($val[1]);
            }
            $count = mb_strlen($_str, 'utf-8');
            $pinyin = '';
            for($i=0; $i<$count; $i++){
                    $str = mb_substr($_str, $i, 1, 'utf-8');
                    if(preg_match('/[a-zA-z]/', $str)){
                            $pinyin .= $str;
                    }else{
                            $yin  = $map[$str];
                            if($yin){
                                    $pinyin .= $yin;
                            }
                    }
            }
            return $pinyin;
    }

    #不付费报名
    public function sign($uid, $act_id){
            if(empty($uid) || empty($act_id)){
               return false; 
            }
            return $this->get_gapi_response('api_web/act_sign_vip', array('uid'=>$uid, 'activity_id'=>$act_id));
    }

    #获取用户报名与付费状态
    #is_singed 报名
    #is_payed 付费
    public function get_sign_pay_status($uid, $act_id){
        $return = array(
            'is_signed' => false,
            'is_payed' => false,
            'price' => 0,
            'pay_type_str' => '',
            'pay_no' => '',
        );
        $db = $this->load->database('zhgroup', TRUE);
        $result = $db->from('ci_act_sign')->where(array('uid'=>$uid, 'actid'=>$act_id, 'ispay'=>1))->get()->result_array();
        if($result){
            $return['is_signed']  = true;
            $return['is_payed'] = true;
        }
        $result = $db->from('ci_order')->where(array('order_type'=>3, 'is_pay'=>1, 'tableid'=>$act_id, 'uid'=>$uid))->get()->result_array();
        if($result){
            $return['price'] = $result[0]['order_price'];
            $pay_info = $db->from('ci_payorder')->where(array('orderno'=>$result[0]['orderno'], 'is_pay'=>1))->get()->result_array();
            if($pay_info){
                $type = $pay_info[0]['paytype'];
                if(3 == $type) $return['pay_type_str'] = '支付宝网页';
                else if(4 == $type) $return['pay_type_str'] = '微信支付';
                $return['pay_no'] = $result[0]['pay_no'];
            }
        }
        return $return;
    }

    #调用gapi
    public function get_gapi_response($interface, $param){
            $gapi_conf = $this->config->item('gapi');
            $url  = $gapi_conf['url'].'/'.$interface;
            $config['app_key'] = $gapi_conf['app_key'];
            $config['app_secret'] = $gapi_conf['app_secret'];
            if(is_array($param)) $param = array_merge($param, $config);
            $result = curl_get_post($url, $param);
            log_message('info', $url . ' param '.var_export($param, true) . ' result='.var_export($result,true));
            $result = json_decode($result, true);
            return $result;
    }

    //获取房间已经选择数
    public function getChooseHotel($hotel_id)
    {
        $this->db->select_sum('num');
        $this->db->where('hotel_id', $hotel_id);
        $query = $this->db->get('sign_hotel_relation');
        if ($query->num_rows() > 0) return $query->result_array();
        return null;
    }
   
    //根据uid获取号码发送催款短信
    public function sendSmsByid($uid)
    {
        $info = $this->selectBy('sign', array('id'=>$uid));
        if (empty($info)) return false;
        if ($info['0']['needaccom'] < 1 || $info['0']['is_pay'] == 1) return true;
        $sum = $info['0']['sum'];
        //$re = sms_send($info['0']['mobile'], '您已报名成功，应付费用'.$sum .'元，请于5日内完成汇款。因三亚旅游旺季，逾期未到款酒店房间将无法确保为您长时间保留。如有疑问请联系您的服务经理或致电客服热线400-100-9737');
        $re = true;
        log_message('info', $info['0']['mobile'] . ' send sum '.$sum . ' result='.var_export($re,true));
        return $re;
    }

    public function checkLogin($mobile, $vcode)
    {
        //$mobile = $this->api->encrypt($mobile);
        $record = $this->selectBy('sign', array('mobile'=>$mobile, 'vcode'=>$vcode));
		log_message('info', 'selectBy record = ' . var_export($record, true));
        return $record;
    }

    //根据条件查询记录
    //param  like  array('uid'=>$uid)
    public function selectBy($table, $array)
    {
        $query = $this->db->get_where($table, $array);     
        log_message('info', 'mysql handle result = '.var_export(mysql_error(),true). ' ' . __LINE__ . __FILE__);
        if ($query->num_rows() > 0) return $query->result_array();
        return null;
    }

    public function selectByParam($role, $export, $start, $end)
    {
        if ($export == 'over') {
            $sql = "select * from sign where ( sign_step >= 4 or route is not null ) and (check_status != 3 or check_status is null)";
        } else if ($export == 'not') {
            $sql = "select * from sign where route is null and (check_status != 3 or check_status is null)";
        } else if ($export == 'cancel') {
            $sql = "select * from sign where check_status = 3";
        }
        $query = $this->db->query($sql, array($start, $end));
		log_message('info', 'sql=='.$sql);
        log_message('info', 'mysql handle result = '.var_export(mysql_error(),true). ' ' . __LINE__ . __FILE__);
        if ($query->num_rows() > 0){
             $result =  $query->result_array();
             foreach($result as &$r){
                $r = $this->get_sign_info($r['id']);
             }
             return $result;
        }
        return null;
    }

    #整理结果
    public function tidys(&$result){
            foreach($result as &$r){
                    $r = $this->get_sign_info($r['id']);
            }
    }

    //查询前一天未完成注册的记录
    public function hotelYester($yesterday, $today)
    {
        $sql = "select * from sign where sign_step < 5 and sign_time > ? and sign_time < ?";
        $query = $this->db->query($sql, array($yesterday, $today));
        log_message('info', 'mysql handle result = '.var_export(mysql_error(),true). ' ' . __LINE__ . __FILE__);
        return $query->result_array();
    }

    public function deleteBy($table, $array)
    {
        $this->db->delete($table, $array);     
        log_message('info', 'mysql handle result = '.var_export(mysql_error(),true). ' ' . __LINE__ . __FILE__);
        return true;
    }

    //插入记录
    public function intoTable($table, $data)
    {
        $re = $this->db->insert($table, $data);
        log_message('info', 'mysql handle result = '.var_export(mysql_error(),true). ' ' . __LINE__ . __FILE__);
        $return = ($this->db->affected_rows() > 0) ? $this->db->insert_id() : '-1';
        return $return;
    }

    //根据条件更新数据
    public function updateBy($table, $where, $data)
    {
        $re = $this->db->update($table, $data, $where);
        log_message('info', 'mysql handle result = '.var_export(mysql_error(),true). ' ' . __LINE__ . __FILE__);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    //获取完成记录数
    public function get_total($table, $where)
    {
        $this->db->where($where);
        $query = $this->db->get($table);
        $total = $query->num_rows();
        return $total;
    }

    //$num是每页记录数，$offset是偏移
    public function get_all($where, $num, $offset)
    {
        $this->db->order_by('submit_time desc, sign_time desc');
        $this->db->where($where);
        $query = $this->db->get('sign', $num, $offset);
        $data =  $query->result_array();
        foreach($data as &$val){
            $val = $this->get_sign_info($val['id']);
        }
        return $data;
    }

    #选择全部
    public function select_all()
    {
        $this->db->order_by('sign_time', 'desc');
        $result = $this->db->get('sign')->result_array();
        $this->tidys($result);
        return $result;
    }

    //排重
    public function check_duplicate($mobile, $email, $time='')
    {
        $sql = "select * from zh2013_sign where sign_step = 4 and create_time < ? and ( mobile = ? or email = ?)";
        $query = $this->db->query($sql, array($time, $mobile, $email));
        return $query;
    }

    #流水id获取报名信息
    public function get_sign_info($id){
       $info = $this->db->from('sign')->where(array('id'=>$id))->get()->result_array();
       if(empty($info)) return array();
       $children = $this->db->from('child_with')->where(array('uid'=>$info[0]['zhid']))->get()->result_array();
       isset($info[0]) || ($info[0] = array());
       isset($children[0]) || ($children[0] = array());
       $info = array_merge($children[0], $info[0]);
       $user_info = $this->getUserInfo($info['mobile']);
       if($user_info){
            $info['avatar_url'] = $user_info['head_url'];
            if(empty($info['uname'])) $info['uname'] = $user_info['nick_name'];
            if(empty($info['company'])) $info['company'] = $user_info['com_name'];
            if(empty($info['zhid'])) $info['zhid'] = $user_info['uid'];
            if(empty($info['position'])) $info['position'] = $user_info['position'];
			$info['service_name'] = $user_info['service_name'];
			$info['industry'] = $user_info['main_industry_label'];
			$info['province'] = $user_info['province_label'];
			if(empty($info['usga'])) {
				$info['turnover'] = $user_info['turnover_label'];
			} else{
				$info['turnover'] = $info['usga'];
			}
       }
       if($info){
            $pay_info = $this->get_sign_pay_status($info['zhid'], ACT_ID);
            if(empty($info['sum']))
                $info['sum'] = (int)$pay_info['price'];
			if(empty($info['is_pay']))
            	$info['is_pay'] = (int)$pay_info['is_payed'];
            if($pay_info['is_signed'] == false){
                   $info['check_status'] = 3;  
                   $info['is_signed'] = false;
            }
            else {
                $info['sign_step'] = 4;
                $info['is_signed'] = true;
            }
            $info['pay_type_str'] = (string)$pay_info['pay_type_str'];
            $info['pay_no'] = (string)$pay_info['pay_no'];
       }
       if($info['route']) $info['route'] = json_decode($info['route'], true);
       $info['usertype'] = $this->get_user_type($info['mobile']);
       return $info;
    }

    #根据输入的用户数据获取家宴的用户信息
    public function getImportUserId($info){
           $data = $this->getUserInfo($info['mobile']);
           if(empty($data)) {
                $uid = $this->sign(
                    array(
                        'uname'=>$info['uname'],
                        'title'=>$info['title'],
                        'company'=>$info['company'],
                        'email' => $info['email'],
                        'mobile' => $info['mobile'],
              )
             );
              return $uid;
         }
         return $data['uid'];
    }

    public function get_id_by_mobile($mobile){
          if($mobile){
                $result = $this->db->where('mobile', $mobile)->get('sign')->result_array();
                if($result){
                    return $result[0]['id'];
                }
          }
          return false;
    }

    #按数据库字段名保存到数据库
    public function save_info($info){
        $sign_fileds  = $this->sign_fileds;
        $with_child_fileds = $this->with_child_fileds;
        if($id = $this->get_id_by_mobile($info['mobile'])){
            $info['id'] =  $id;
        }
        $info['zhid'] = $this->getImportUserId($info);
        if(empty($info['zhid'])) return false;
        $info['usertype']   = $this->get_user_type($info['mobile']);
        $info['submit_time'] = date('Y-m-d H:i:s');
        $info['sign_time'] = date('Y-m-d H:i:s');
        if(is_array($info['route']) && $info['route']) $info['route'] = json_encode(array_values($info['route']));
        $info['uid'] = $info['zhid'];
        if($info['id']){
            $this->db->where('id', $info['id'])->update('sign', array_intersect_key($info, array_flip($sign_fileds)));
        }else{
            $info['vcode'] = rand(100000, 999999);
            $this->db->insert('sign', array_intersect_key($info, array_flip($sign_fileds)));
        }
        if($this->db->from('child_with')->where('uid', $info['zhid'])->get()->result_array()){
            $this->db->where('uid', $info['zhid'])->update('child_with', array_intersect_key($info, array_flip($with_child_fileds)));
        }else{
            $this->db->insert('child_with', array_intersect_key($info, array_flip($with_child_fileds)));
        }
        $db=$this->load->database('zhgroup', TRUE);
        if(array_key_exists('do_sign', $info) && $info['do_sign']){
                if($result = $db->from('ci_act_sign')->where(array('uid'=>$info['uid'], 'actid'=>ACT_ID))->get()->result_array())
                   $db->where(array('uid'=>$info['uid'], 'actid'=>ACT_ID))->update('ci_act_sign', array('ispay'=>1));
                else
                   $this->sign($info['uid'], ACT_ID);
        }else{
                $db->where(array('uid'=>$info['uid'], 'actid'=>ACT_ID))->update('ci_act_sign', array('ispay'=>0));
        }
        return true;
    }
}
