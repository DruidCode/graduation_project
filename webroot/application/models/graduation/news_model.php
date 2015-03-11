<?php
if(!defined('BASEPATH'))  exit('No direct script access allowed');

class News_model extends CI_model {

	function __construct() {
		parent :: __construct();
        $this->load->helper(array('web_api','sm', 'curl'));
        $this->load->model('api/api_model', 'api');
		$this->load->database('graduation');
	}

	public function add_guestbook($data) {
        $re = $this->db->insert('guestbook', $data);
        $return = ($this->db->affected_rows() > 0) ? $this->db->insert_id() : '-1';
        log_message('info', 'mysql handle result = '.var_export(mysql_error(),true). ' ' . __LINE__ . __FILE__);
        return $return;
	}

    //$num是每页记录数，$offset是偏移
    public function get_news_list($num, $offset)
    {
		if ( empty($offset) ) $offset = 0;
		$this->db->where('is_active = 1');
        $this->db->order_by('ctime desc');
        $query = $this->db->get('news', $num, $offset);
        $data =  $query->result_array();
        return $data;
    }

    public function get_news_total()
    {
		$this->db->where('is_active = 1');
        $query = $this->db->get('news');
        $total = $query->num_rows();
        return $total;
    }

	public function news_detail($nid)
	{
        $query = $this->db->get_where('news', array('is_active'=>'1', 'id'=>$nid));
        $data  = $query->result_array();
        return $data;
	}
}
