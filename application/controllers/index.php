<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model("user");
		$this->load->model("morning_sign");
	}
	public function index()
	{
		$this->weixinAction();
	}
	

	public function getThisMondayTimeStamp($date) {
		$timeStamp = strtotime($date);
		$w = date('w',$timeStamp);
		$thisMonday = $timeStamp - 86400 * ($w -1);
		return $thisMonday;
	}

	public function isTimeStampEqual($timeStamp1,$timeStamp2){
		return $timeStamp1 === $timeStamp2;
	}

	public function weixinAction(){
		$uid = $_GET['id'];
		$keyword = $_GET['key'];
		if(!empty($_GET['weixinID']))
			$weixinID = $_GET['weixinID'];
		date_default_timezone_set('Asia/Shanghai'); 
		#分词
		
		if($keyword == "测试"){
			// $date1 = '2013-11-20';
	  //       $date2 = date('Y-m-d');
	  //       if($this->isTimeStampEqual($this->getThisMondayTimeStamp($date1),$this->getThisMondayTimeStamp($date2)))
	  //       	echo "yes";
	  //       else
	  //       	echo "no";

		}

		#查看积分排行榜
		if($keyword == "积分排行榜" | $keyword == "积分排名"){

			$theHighestScoreUsers = $this->user->get_the_highest_score_users(5);
			$rank = 0;
			foreach ($theHighestScoreUsers as $item) {
				echo "No.".(++$rank)." ".$item['name']."  ".$item['score']."分<br/>";
			}

			if($this->user->has_this_user_name($uid)){

				$currentUser = $this->user->get_user_by_uid($uid);
				
				echo "<br/>".$currentUser['name']."，你目前的积分是".$currentUser['score']."<br/>";
			}
			else{
				echo "<br/>只有加入积分会员才能有属于自己的积分哦，输入\"绑定 昵称\"或\"BD 昵称\"加入早睡早起联盟积分会员吧!<br/>";
			}

			
		}

		#查看我的积分
		if($keyword == "我的积分"){
			if($this->user->has_this_user_name($uid)){
				$currentUser = $this->user->get_user_by_uid($uid);
				echo $currentUser['name']."，你目前的积分是".$currentUser['score'];
			}
			else{
				echo "只有加入积分会员才能有属于自己的积分哦，输入\"绑定 昵称\"或\"BD 昵称\"加入早睡早起联盟积分会员吧!<br/>";
			}
			
		}
		#绑定昵称
		if(preg_match('/^绑定*/', $keyword)){

			$toBondName = preg_replace('/绑定/', "", $keyword);
			$toBondNameNoBlank = trim($toBondName);

			if($this->user->has_this_user_name($uid)){
				echo "你已经绑定过昵称了<br/>";
				echo "<br/>如需更改昵称，请输入\"更改绑定 昵称\"或\"GGBD 昵称\"即可<br/>";
			}

			else{			
				$user = array('name'=>$toBondNameNoBlank,'openid'=>$uid,'weixinID'=>$weixinID);
				if($this->user->insert_user($user))
				{
					echo "绑定昵称成功！<br/>".$toBondNameNoBlank.",输入\"起床\"或\"签到\"参与早睡早起联盟早起争霸吧！<br/>";
					echo "<br/>如需更改昵称，请输入\"更改绑定 昵称\"或\"GGBD 昵称\"即可<br/>";
				}
				else
					echo "发生未知错误，请重新绑定.";
			}
		}

		if(preg_match('/^更改绑定*/', $keyword)){
			$toBondName = preg_replace('/更改绑定/', "", $keyword);
			$toBondNameNoBlank = trim($toBondName);


			if($this->user->has_this_user_name($uid)){
				$this->user->update_user_name_by_uid($uid,$toBondNameNoBlank);
				echo "更改昵称成功! 输入\"起床\"或\"签到\"参与早睡早起联盟早起争霸吧！<br/>";
			}
			else{
				echo "你还没绑定呢…怎么来更改呢？<br/>";
				echo "请输入\"绑定 昵称\"或\"BD 昵称\"来加入我们的积分会员，参与更多的早睡早起积分活动吧";
			}
		}

		#起床排行
		if($keyword == "起床排行榜"){
			$date = date('Y-m-d');
			$earliestUserInfo  = $this->morning_sign->get_the_earliest_info_on_this_date($date,10);

			if(!empty($earliestUserInfo))
			{
				echo "【早起排行榜】：<br/>";
				foreach ($earliestUserInfo as $item) {
				 	echo "No.".$item['rank']." ".$item['user']." ".date('H:i',strtotime($item['time'])).'<br/>';
				 }
				$totalTodayCounts = $this->morning_sign->get_total_count_on_this_date($date);
				echo "今天已有".$totalTodayCounts ."人参与起床排行.<br/>";

				
			}
			else{
				echo"今天还没有人来签到呢！<br/><br/>";
			}

			if($this->user->has_this_user_name($uid)){
				$currentUser = $this->user->get_user_by_uid($uid);
				$currentUserName = $currentUser['name'];
				$todayYourEarlyRecord = $this->morning_sign->get_record_by_uid_and_date($uid,$date);
				if(!empty($todayYourEarlyRecord)){
					echo $currentUserName.",你今天起床时间是".date('H:i',strtotime($todayYourEarlyRecord['time'])).",排名No.".$todayYourEarlyRecord['rank']."<br/>";
				}
				else{
					echo "你今天还没有签到哦~  请回复\"起床\"或\"签到\"来进行签到<br/>";
				}
			}

			else{
				echo "请输入\"绑定 昵称\"或\"BD 昵称\"进行联盟成员加入，绑定后才能参与起床排名哦！<br/>";
			}
			echo "输入\"早起签到规则\"或\"3\"可查看早起签到的具体说明与积分获取规则.<br/>";
		}

		#起床操作

		if($keyword == "起床"){

			if($this->user->has_this_user_name($uid)){
				
				$currentUser = $this->user->get_user_by_uid($uid);
				$currentUserName = $currentUser['name'];
				$date = date('Y-m-d');#定义日期格式
				$time = date('H:i:s');#定义时间格式

				if($time>=06&&$time<=12){
					#取得当天的总记录数
					 $totalTodayRecordsCount = $this->morning_sign->get_total_count_on_this_date($date);
				    #取得该用户的记录数
					 $totalTodayYourRecordsCount = $this->morning_sign->get_total_count_on_this_date_by_uid($date,$uid);
					 if($totalTodayYourRecordsCount){
					 	echo "你今天已经签到过了哟！<br/>";
					 }
					 else{
					 	$data = array(
					 		"user"=>$currentUserName,
					 		"rank"=>$totalTodayRecordsCount+1,
					 		"openid"=>urldecode($uid)
					 	);
					 	$todayYourEarlyRecord = $this->morning_sign->insert_morningsign_item($data);
					 	if($todayYourEarlyRecord == 0){
					 		echo "签到失败<br/>";
					 	}
					 	$earliestUserInfo  = $this->morning_sign->get_the_earliest_info_on_this_date($date,10);
					 	if(!empty($earliestUserInfo))
					 		echo "【早起排行榜】：<br/>";
					 	else{
					 		#todo
					 	}
					 	foreach ($earliestUserInfo as $item) {
					 		echo "No.".$item['rank']." ".$item['user']." ".date('H:i',strtotime($item['time'])).'<br/>';
					 	}
					 	echo $todayYourEarlyRecord['user'].",你的起床时间是".date('H:i',strtotime($todayYourEarlyRecord['time'])).",是今天的第".$todayYourEarlyRecord['rank']."个起床的哟~<br/>";			 	
					 		

					 	$yesterdayYourEarlyRecord = $this->morning_sign->get_record_by_uid_and_date($uid,date('Y-m-d',time()-24*60*60));


					 	if(!empty($yesterdayYourEarlyRecord)){
					 		$yesterdayYourEarlyTime = date('H:i:s',strtotime($yesterdayYourEarlyRecord['time']));
					 		if($yesterdayYourEarlyTime<= '09:00:00'){

					 			if($time<='09:00:00'){
					 				$currentUserContinuousSignDays = $currentUser['continuousSignDays'];
					 				$this->user->update_user_continuousSignDays_by_uid($uid,$currentUserContinuousSignDays+1);
					 				echo "你已经连续".($currentUserContinuousSignDays+2)."天签到了，再接再励哦！<br/>";


					 				//周连续签到天数更新
					 				if($this->isTimeStampEqual($this->getThisMondayTimeStamp(date('Y-m-d',strtotime($yesterdayYourEarlyRecord['time']))),$this->getThisMondayTimeStamp($date))){

					 					$currentUserContinuousWeekSignDays = $currentUser['continuousWeekSignDays'];
					 					$this->user->update_user_continuousWeekSignDays_by_uid($uid,$currentUserContinuousWeekSignDays+1);

					 				}
					 				
						 		}
						 		else{
						 			$this->user->update_user_continuousSignDays_by_uid($uid,0);
						 			$this->user->update_user_continuousWeekSignDays_by_uid($uid,0);
						 		}

					 		}
					 		
					 	}
						else{
							$this->user->update_user_continuousSignDays_by_uid($uid,0);
				 			$this->user->update_user_continuousWeekSignDays_by_uid($uid,0);
						}

						
						$currentUserThisWeekScore = $currentUser['thisWeekScore'];
						$currentUserScore = $currentUser['score'];
						if($time>="06:00:00"&&$time<="07:30:00"){
							echo "你获得了3个积分！<br/>";
							$this->user->update_user_thisWeekScore_by_uid($uid,$currentUserThisWeekScore+3);
							$this->user->update_user_score_by_uid($uid,$currentUserScore+3);
						}
						else if($time>="07:30:00"&&$time<="08:30:00"){
							echo "你获得了2个积分！<br/>";
							$this->user->update_user_thisWeekScore_by_uid($uid,$currentUserThisWeekScore+2);
							$this->user->update_user_score_by_uid($uid,$currentUserScore+2);
						}
						else if($time>="08:30:00"&&$time<="09:00:00"){
							echo "你获得了1个积分！<br/>";
							$this->user->update_user_thisWeekScore_by_uid($uid,$currentUserThisWeekScore+1);
							$this->user->update_user_score_by_uid($uid,$currentUserScore+1);
						}

						$currentUser = $this->user->get_user_by_uid($uid);
						$currentUserThisWeekScore = $currentUser['thisWeekScore'];
					 	$currentUserScore = $currentUser['score'];
					 	if($todayYourEarlyRecord['rank']<=3){				 		
					 		$this->user->update_user_thisWeekScore_by_uid($uid,$currentUserThisWeekScore+2);
					 		$this->user->update_user_score_by_uid($uid,$currentUserScore+2);
					 		echo "每天早起签到前3名积分额外+2!<br/>";			 		
					 	}
					 	else if ($todayYourEarlyRecord['rank']<=5){
					 		
					 		$this->user->update_user_thisWeekScore_by_uid($uid,$currentUserThisWeekScore+1);
					 		$this->user->update_user_score_by_uid($uid,$currentUserScore+1);
					 		echo "每天早起签到4-5名积分额外+1!<br/>";	
					 	}
						

						// update上次签到的时间
						$dateTime = date("Y-m-d");
						$this->user->update_user_lastSignDate_by_uid($uid,$dateTime);

						echo "/太阳回复数字：<br/>";
						echo "【11】 → 瞟一眼天下事，早安新闻，天下我有<br/>";
						echo "【12】 → 来一小段音乐，清醒头脑，感知心情 <br/>";
						echo "【13】 → 来看看今周的早餐地点推荐<br/>";

					 }
				}
				else if($time<=06){
					echo "你这也太早了吧…再睡一会吧~六点我们才正式开始签到！<br/>";
				}
				else if($time>=12&&$time<=19){
					echo "= =这都大下午了你才起床…<br/>";
				}
				else{
					echo "你这是日夜颠倒了吗？<br/>";
				}
				//update用户的weixinID
				$this->user->update_user_weixinID_by_uid($uid,$weixinID);

			}

			else{
				echo "请输入\"绑定 昵称\"或\"BD 昵称\"进行联盟成员加入，绑定后才能参与起床排名哦！<br/>";
			}
			echo "输入\"早起签到规则\"或\"3\"可查看早起签到的具体说明与积分获取规则.<br/>";
		}



	}
}
header("Content-Type: text/html; charset=utf-8");
