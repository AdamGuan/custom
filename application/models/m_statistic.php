<?php
class M_statistic extends MY_Model {

	public function __construct() {
		parent :: __construct();
		$this -> load -> database();
	}
	
	/**
	 * 根据地区获取周每日的咨询人数
	 * @parame	$group_id	int
	 * @return	$list	array二维
	 *				total	int
	 *				week	string
	 */
	public function get_ask_persons_every_week($group_id,$weekoffset = 0)
	{
		$list = array();
		if(isset($group_id))
		{
			//获取这个星期的日期
			$week_date_list = get_weeks_date($weekoffset);
			if(is_array($week_date_list) && count($week_date_list) > 0)
			{
				//查询属于地区的客服
				$custom_list = array();
				$sql = 'SELECT F_custom_name FROM t_custom WHERE F_groupid = "'.(int)$group_id.'"';
				$query = $this->db->query($sql);
				if($query->num_rows() > 0)
				{
					$array = $query->result_array();
					foreach($array as $item)
					{
						$custom_list[] = $item['F_custom_name'];
					}
				}

				if(count($custom_list) > 0)
				{
					//
					$customs = '"'.implode('","',$custom_list).'"';
					foreach($week_date_list as $date)
					{
						$table_suffix = date('Ym',strtotime($date));
						$table = 't_msg_stduent_send_'.$table_suffix;
						$sql = 'SELECT COUNT(DISTINCT F_from_name) AS total FROM '.$table.' WHERE FROM_UNIXTIME(F_timestamp/1000,\'%Y-%m-%d\') = "'.$date.'" AND F_to_name IN('.$customs.')';

						$query = $this->db->query($sql);
						if($query->num_rows() > 0)
						{
							$row = $query->row_array();
							$total = $row['total'];
							$list[] = array(
								'total'=>(int)$total,
								'week'=>date('l',strtotime($date)),
								'date'=>$date,
							);
						}
					}
				}
			}
		}

		if(isset($week_date_list) && count($week_date_list) > 0 && count($list) <= 0)
		{
			foreach($week_date_list as $date)
			{
				$list[] = array(
					'total'=>0,
					'week'=>date('l',strtotime($date)),
					'date'=>$date,
				);
			}
		}
		return $list;
	}

	/**
	 * 根据地区获取月每日的咨询人数
	 * @parame	$group_id	int
	 * @return	$list	array二维
	 *				total	int
	 *				date	date
	 *				day		int
	 */
	public function get_ask_persons_every_month($group_id,$monthoffset = 0)
	{
		$list = array();
		if(isset($group_id))
		{
			//获取这个星期的日期
			$date_list = get_months_date($monthoffset);
			if(is_array($date_list) && count($date_list) > 0)
			{
				//查询属于地区的客服
				$custom_list = array();
				$sql = 'SELECT F_custom_name FROM t_custom WHERE F_groupid = "'.(int)$group_id.'"';
				$query = $this->db->query($sql);
				if($query->num_rows() > 0)
				{
					$array = $query->result_array();
					foreach($array as $item)
					{
						$custom_list[] = $item['F_custom_name'];
					}
				}
				if(count($custom_list) > 0)
				{
					//
					$customs = '"'.implode('","',$custom_list).'"';
					$table_suffix = date('Ym',strtotime($date_list[0]));
					$table = 't_msg_stduent_send_'.$table_suffix;
					$sql_list = array();
					foreach($date_list as $date)
					{
						$sql_list[] = '(SELECT COUNT(DISTINCT F_from_name) AS total,"'.$date.'" as date FROM '.$table.' WHERE FROM_UNIXTIME(F_timestamp/1000,\'%Y-%m-%d\') = "'.$date.'" AND F_to_name IN('.$customs.'))';
						
					}
					if(isset($sql_list[0]) > 0)
					{
						$sql = implode(" UNION ALL ",$sql_list);
						$query = $this->db->query($sql);
						if($query->num_rows() > 0)
						{
							$array = $query->result_array();
							foreach($array as $key=>$item)
							{
								$array[$key]['total'] = $item['total'];
								$array[$key]['date'] = $item['date'];
								$array[$key]['day'] = date('j',strtotime($item['date']));
							}
							$list = $array;
						}
					}
				}
			}
		}

		if(isset($date_list) && count($date_list) > 0 && count($list) <= 0)
		{
			foreach($date_list as $date)
			{
				$list[] = array(
					'total'=>0,
					'day'=>date('j',strtotime($date)),
					'date'=>$date,
				);
			}
		}
		return $list;
	}

}

/**
 * End of file m_statistic.php
 */
/**
 * Location: ./app/model/m_statistic.php
 */