<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Morning_Sign extends MY_Model{
	public function __construct(){
		parent::__construct();
		$this->table = 'e_morning_sign';
		$this->db = $this->load->database('default',TRUE);
	}
	public function insert_morningsign_item($data){
		$result =  $this->insert($data);
		if($result['status']){
			$result['record'] = $this->get_record_by_id($result['affected_row']);
			return $result['record'];
		}
		else{
			return 0;
		}
	}
	public function get_total_counts(){
		return $this->result_count();
	}

	protected function get_record_by_id($id){
		$condition = array('id'=>$id);
		$this->where($condition);
		return $this->result_single();
	}

	public function get_record_by_uid_and_date($uid,$date){
		$condition = array('openid'=>$uid,'time >='=>$date);
		$this->where($condition);
		return $this->result_single();
	}


	public function get_total_count_on_this_date($date){
		$condition = array('time >='=>$date);
		$this->where($condition);
		return $this->result_count();
	}

	public function get_total_count_on_this_date_by_uid($date,$uid)
	{
		$condition = array('time >=' => $date,'openid'=>$uid);
		$this->where($condition);
		return $this->result_count();
	}

	#起床时间最早的用户
	public function get_the_earliest_info_on_this_date($date,$num,$start = 0)
	{
		$condition = array('time >='=>$date);
		$this->where($condition);
		$this->order_by('rank','asc');
		$this->limit($num,$start);
		return $this->result_array();
	}
}