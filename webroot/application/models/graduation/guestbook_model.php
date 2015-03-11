<?php
if(!defined('BASEPATH'))  exit('No direct script access allowed');

class Guestbook_model extends CI_model {

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
    public function get_guest_list($num, $offset)
    {
		if ( empty($offset) ) $offset = 0;
        $this->db->order_by('ctime desc');
        $query = $this->db->get('guestbook', $num, $offset);
        $data =  $query->result_array();
        return $data;
    }

    public function get_guest_total()
    {
        $query = $this->db->get('guestbook');
        $total = $query->num_rows();
        return $total;
    }
}
