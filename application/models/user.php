<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Model{
	public function __construct(){
		parent::__construct();
		$this->table = 'e_user';
		$this->db = $this->load->database('default',TRUE);
	}

	public function insert_user($user){
		return $this->insert($user);
	}

	public function get_total_user_counts(){
		return $this->result_count();
	}

	public function update_user_name_by_uid($uid,$name){
		$condition = array('openid'=>$uid);
		$this->where($condition);
		$data = array('name' => $name);
		return $this->update($data);
	}
	
	public function update_user_continuousSignDays_by_uid($uid,$continuousSignDays){
		$condition = array('openid'=>$uid);
		$this->where($condition);
		$data = array('continuousSignDays' => $continuousSignDays);
		return $this->update($data);
	}

	public function update_user_score_by_uid($uid,$score){
		$condition = array('openid'=>$uid);
		$this->where($condition);
		$data = array('score' => $score);
		return $this->update($data);
	}

	public function update_user_lastWeekScore_by_uid($uid,$lastWeekScore){
		$condition = array('openid'=>$uid);
		$this->where($condition);
		$data = array('lastWeekScore' => $lastWeekScore);
		return $this->update($data);
	}



	public function update_user_thisWeekScore_by_uid($uid,$thisWeekScore){
		$condition = array('openid'=>$uid);
		$this->where($condition);
		$data = array('thisWeekScore' => $thisWeekScore);
		return $this->update($data);
	}

	public function update_user_continuousWeekSignDays_by_uid($uid,$continuousWeekSignDays){
		$condition = array('openid'=>$uid);
		$this->where($condition);
		$data = array('continuousWeekSignDays' => $continuousWeekSignDays);
		return $this->update($data);
	} 


	public function update_user_weixinID_by_uid($uid,$weixinID){
		$condition = array('openid'=>$uid);
		$this->where($condition);
		$data = array('weixinID' => $weixinID);
		return $this->update($data);
	}

	public function update_user_lastSignDate_by_uid($uid,$lastSignDate){
		$condition = array('openid'=>$uid);
		$this->where($condition);
		$data = array('lastSignDate' => $lastSignDate);
		return $this->update($data);
	}

	public function get_user_by_uid($uid){
		$condition = array('openid'=>$uid);
		$this->where($condition);
		return $this->result_single();
	}

	public function has_this_user_name($uid){
		$user = $this->get_user_by_uid($uid);
		if(!empty($user))
			return TRUE;
		else
			return FALSE;
	}	

	#积分最高的用户
	public function get_the_highest_score_users($num,$start = 0){
		$this->order_by('score','desc');
		$this->limit($num,$start);
		return $this->result_array();
	}
}