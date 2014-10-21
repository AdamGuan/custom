<?php
/**
 * 环信群组
 */
class M_custom extends MY_Model {

	private $org_name;
	private $app_name;
	private $client_id;
	private $client_secret;

	private $F_group_id;

	public function __construct() {
		parent :: __construct();
		$this -> load -> database();
		//load helper
		$this->load->helper(array('im'));
		//ini chat var
		$this->org_name = $this->my_config['org_name'];
		$this->app_name = $this->my_config['app_name'];
		$this->client_id = $this->my_config['client_id'];
		$this->client_secret = $this->my_config['client_secret'];
		//
		$this->F_group_id = $this -> session -> userdata('F_group_id');
	}
	
	/**
	 * 创建一个客服
	 * @parame	$parames	array
	 *		name	string	客服用户名
	 *		pwd		string	密码，明文
	 *		groupid	int		组ID
	 * @return $custom_id	string	环信反馈的用户ID,如果为0则失败
	 */
	public function create_custom($parames = array())
	{
		$custom_id = 0;
		//
		if(isset($parames['name'],$parames['pwd'],$parames['groupid']))
		{
			//判断是否在同一个组
			if($this->F_group_id != 0 && $this->F_group_id != $parames['groupid'])
			{
				return $custom_id;
			}
			//检查要创建的用户是否在应用数据库以及环信中已存在
			$sql = 'SELECT F_custom_id FROM t_custom WHERE F_custom_name = "'.$parames['name'].'" LIMIT 1';
			$query = $this->db->query($sql);
			if($query->num_rows() <= 0)
			{
				$arrayResult =  im_do("users/".$parames['name'],"GET");
				if(!(isset($arrayResult,$arrayResult['entities']) && is_array($arrayResult['entities']) && count($arrayResult['entities']) > 0))
				{
					//查询群组id是否存在
					$sql = 'SELECT F_id FROM t_group WHERE F_id = '.(int)$parames['groupid'].' LIMIT 1';
					$query = $this->db->query($sql);
					if($query->num_rows() > 0)
					{
						$groupid = (int)$parames['groupid'];
						//创建环信用户
						$data=array(
							"username"=>$parames['name'],
							"password"=>$this->my_config['custom_pwd'],
						);
						$arrayResult =  im_do("users","POST",$data);
						if((isset($arrayResult,$arrayResult['entities']) && is_array($arrayResult['entities']) && isset($arrayResult['entities'][0]['uuid'])))
						{
							$custom_id_tmp = $arrayResult['entities'][0]['uuid'];
							$time = date('Y-m-d H:i:s',time());
							//把用户添加到应用数据库
							$sql = 'INSERT INTO t_custom(`F_custom_id`,`F_custom_name`,`F_custom_nickname`,`F_custom_createtime`,`F_custom_modifytime`,`F_groupid`) VALUES("'.$custom_id_tmp.'","'.$parames['name'].'","'.$parames['name'].'","'.$time.'","'.$time.'",'.$groupid.')';
							$this->db->query($sql);
							$tmp = $this->db->insert_id();
							if(isset($tmp) && $tmp > 0)
							{
								$custom_id = $custom_id_tmp;
							}
						}
					}
				}
			}

		}
		return $custom_id;
	}
	
	/**
	 * 获取全部的客服
	 * @return	$list	array	二维
	 *		F_custom_id			string		客服在环信中的ID
	 *		F_custom_name		string		客服名
	 *		F_custom_createtime	datetime	客服创建时间
	 *		F_groupname			string		组名
	 */
	public function get_custom_list()
	{
		$list = array();
		$F_group_id = $this->F_group_id;
		$where = '1';
		if($F_group_id != 0)
		{
			$where .= ' AND t_custom.F_groupid = '.$F_group_id;
		}
		$sql = 'SELECT F_custom_id,F_custom_name,F_custom_createtime,F_groupname FROM t_custom LEFT JOIN t_group ON t_custom.F_groupid = t_group.F_id WHERE '.$where.' ORDER BY F_custom_createtime DESC';
		$query = $this->db->query($sql);
		if($query->num_rows() > 0)
		{
			$list = $query->result_array();
		}
		return $list;
	}
	
	/**
	 * 获取一个客服在im中的信息
	 * @parame	$user_name_list	array
	 * @return	$info	array一维
	 *				F_custom_status		string
	 *				F_custom_receive	int
	 *				F_custom_replay		int
	 */
	public function get_custom_info_from_im($user_name)
	{
		$info = array(
			'F_custom_status'=>-1,
			'F_custom_receive'=>-1,
			'F_custom_replay'=>-1,
		);
		if(isset($user_name))
		{
			//获取在线状态
			$online = 'offline';
			
			$arrayResult =  im_do("users/".$user_name."/status","GET");
			if(isset($arrayResult,$arrayResult['data']))
			{
				$online = $arrayResult['data'][$user_name];
			}
			
			$info['F_custom_status'] = $online;
			//获取咨询人数
			$total = 0;
			$tmp = get_hash_db($user_name);
			$table = 't_msg_custom_receive_'.$tmp[1].$tmp[2];
			$sql = 'SELECT COUNT(DISTINCT F_from_name) AS total FROM '.$table.' WHERE F_to_name = "'.$user_name.'"';
			$query = $this->db->query($sql);
			$row = $query->row_array();
			if(isset($row['total']))
			{
				$total = (int)$row['total'];
			}
			$info['F_custom_receive'] = $total;
			//获取恢复条数
			$table = 't_msg_custom_send_'.$tmp[1].$tmp[2];
			$sql = 'SELECT COUNT(F_id) AS total FROM '.$table.' WHERE F_from_name = "'.$user_name.'"';
			$query = $this->db->query($sql);
			$row = $query->row_array();
			if(isset($row['total']))
			{
				$total = (int)$row['total'];
			}
			$info['F_custom_replay'] = $total;
		}
		return $info;
	}

	/**
	 * 获取多个客服在im中的信息
	 * @parame	$user_name_list	array
	 * @return	$result	array二维
	 *				F_custom_status		string
	 *				F_custom_receive	int
	 *				F_custom_replay		int
	 *				F_custom_name		string
	 */
	public function get_customs_info_from_im($user_name_list)
	{
		$result = array();
		if(is_array($user_name_list) && count($user_name_list) > 0)
		{
			foreach($user_name_list as $user_name)
			{
				$info = array(
					'F_custom_status'=>-1,
					'F_custom_receive'=>-1,
					'F_custom_replay'=>-1,
					'F_custom_name'=>$user_name,
				);
				//获取咨询人数
				$total = 0;
				$tmp = get_hash_db($user_name);
				$table = 't_msg_custom_receive_'.$tmp[1].$tmp[2];
				$sql = 'SELECT COUNT(DISTINCT F_from_name) AS total FROM '.$table.' WHERE F_to_name = "'.$user_name.'"';
				$query = $this->db->query($sql);
				$row = $query->row_array();
				if(isset($row['total']))
				{
					$total = (int)$row['total'];
				}
				$info['F_custom_receive'] = $total;
				//获取恢复条数
				$table = 't_msg_custom_send_'.$tmp[1].$tmp[2];
				$sql = 'SELECT COUNT(F_id) AS total FROM '.$table.' WHERE F_from_name = "'.$user_name.'"';
				$query = $this->db->query($sql);
				$row = $query->row_array();
				if(isset($row['total']))
				{
					$total = (int)$row['total'];
				}
				$info['F_custom_replay'] = $total;
				//
				$result[] = $info;
				//构建获取在线状态的url
				$url_list[] = 'users/'.$user_name.'/status';
			}
			//获取在线状态
			if(isset($url_list) && count($url_list) > 0)
			{
				$arrayResultList =  im_do_multi($url_list,"GET");
				if(isset($arrayResultList) && is_array($arrayResultList) && count($arrayResultList) > 0)
				{
					foreach($result as $key=>$item)
					{
						$online = 'offline';
						if(isset($arrayResultList[$key],$arrayResultList[$key]['data']))
						{
							$online = $arrayResultList[$key]['data'][$item['F_custom_name']];
						}
						$result[$key]['F_custom_status'] = $online;
					}
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * 获取一个用户对某个客服的聊天记录
	 * @parame	$parame	array
	 *				user_name	string
	 *				custom_name	string
	 *				page		int
	 *				rows		int
	 * @return	$result	array二维
	 *				F_timestamp	datetime
	 *				F_msg		string
	 */
	public function get_student_sendto_custome_chat($parame = array())
	{
		$result = array();
		$user_name = $parame['user_name'];
		$custom_name = $parame['custom_name'];
		if(isset($user_name,$custom_name))
		{
			$tmp = get_hash_db($custom_name);
			$table = 't_msg_custom_receive_'.$tmp[1].$tmp[2];

			$limit = '';
			if(isset($parame['page'],$parame['rows']))
			{
				$offset = (int)(($parame['page'] -1)*$parame['rows']);
				$size = (int)$parame['rows'];
				if($offset < 0)$offset = 0;
				if($size < 0)$size = 0;
				$limit = 'LIMIT '.$offset.','.$size;
			}

			$sql = 'SELECT F_msg_id,FROM_UNIXTIME(F_timestamp/1000,\'%Y-%m-%d %H:%i:%s\') as F_timestamp FROM '.$table.' WHERE F_to_name = "'.$custom_name.'" AND F_from_name = "'.$user_name.'" '.$limit;
			$query = $this->db->query($sql);
			$result = $query->result_array();
			$query_tmp = '';
			foreach($result as $key=>$item)
			{
				$result[$key]['F_msg'] = "";
				if(strlen($query_tmp) == 0)
				{
					$query_tmp = "msg_id='".$item['F_msg_id']."'";
				}
				else
				{
					$query_tmp .= " || msg_id='".$item['F_msg_id']."'";
				}
				//unset($result[$key]['F_msg_id']);
			}
			//查询chat内容
			$query = "ql=select * where ".$query_tmp;
			$arrayResult = im_do("chatmessages","GET",array(),$query);
			if(isset($arrayResult,$arrayResult['entities']) && is_array($arrayResult['entities']) && count($arrayResult['entities']) > 0)
			{
				foreach($result as $key=>$item)
				{
					foreach($arrayResult['entities'] as $k=>$it)
					{
						if($item['F_msg_id'] == $it['msg_id'])
						{
							if(isset($it['payload'],$it['payload']['bodies'],$it['payload']['bodies'][0]))
							{
								$type = $it['payload']['bodies'][0]['type'];
								if($type == 'txt')
								{
									$result[$key]['F_msg'] = $it['payload']['bodies'][0]['msg'];
								}
								else if($type == 'audio')
								{
									$result[$key]['F_msg'] = "[语音]";
								}
								else if($type == 'img')
								{
									$result[$key]['F_msg'] = "[图片]";
								}
							}
							unset($arrayResult[$k]);
							break;
						}
					}
				}
			}
		}
		return $result;
	}

	/**
	 * 获取一个用户对某个客服的聊天记录总数
	 * @parame	$parame	array
	 *				user_name	string
	 *				custom_name	string
	 * @return	$total	int
	 */
	public function get_student_sendto_custome_chat_total($parame = array())
	{
		$total = 0;
		$user_name = $parame['user_name'];
		$custom_name = $parame['custom_name'];
		if(isset($user_name,$custom_name))
		{
			$tmp = get_hash_db($custom_name);
			$table = 't_msg_custom_receive_'.$tmp[1].$tmp[2];

			$sql = 'SELECT count(F_msg_id) as total FROM '.$table.' WHERE F_to_name = "'.$custom_name.'" AND F_from_name = "'.$user_name.'"';
			$query = $this->db->query($sql);
			$row = $query->row_array();
			$total = (int)$row['total'];
		}
		return $total;
	}
	

	/**
	 * 获取一个客服的聊天记录
	 * @parame	array
	 *		custom_name	string
	 *		page		int
	 *		rows		int
	 * @return	$result	array二维
	 *				username	string
	 *				timestamp	datetime
	 *				status		string
	 *				msg			string
	 *				msg_id		string
	 *				userid		string
	 */
	public function get_custom_receive_chat($parame = array())
	{
		$result = array();
		$user_name = $parame['custom_name'];
		if(isset($user_name))
		{
			$tmp = get_hash_db($user_name);
			$table = 't_msg_custom_receive_'.$tmp[1].$tmp[2];
			$table2 = 't_msg_custom_send_'.$tmp[1].$tmp[2];

			$limit = '';
			
			if(isset($parame['page'],$parame['rows']))
			{
				$offset = (int)(($parame['page'] -1)*$parame['rows']);
				$size = (int)$parame['rows'];
				if($offset < 0)$offset = 0;
				if($size < 0)$size = 0;
				$limit = 'LIMIT '.$offset.','.$size;
			}
			

			$sql = 'SELECT tt.receive_f_id,tt.student as username,tt.F_from_name as userid,FROM_UNIXTIME(tt.receive_msg_timestamp/1000,\'%Y-%m-%d %H:%i:%s\') as timestamp,tt.receive_msg_id as msg_id,if((tt.receive_msg_timestamp<tt2.send_msg_timestamp),"已回复","未回复") as status FROM (SELECT t.* FROM (SELECT F_id as receive_f_id,F_to_name as custom,F_from_name,F_msg_id as receive_msg_id,F_timestamp as receive_msg_timestamp,t_student.t_student_name as student FROM '.$table.',t_student WHERE F_to_name = "'.$user_name.'" AND F_from_name = t_student.t_id ORDER BY F_timestamp DESC) AS t GROUP BY t.student ORDER BY t.receive_msg_timestamp DESC) as tt LEFT JOIN (SELECT t2.* FROM (SELECT F_from_name as custom,F_to_name as student,F_timestamp as send_msg_timestamp FROM '.$table2.' WHERE F_from_name = "'.$user_name.'" ORDER BY F_timestamp DESC) AS t2 GROUP BY t2.student ORDER BY t2.send_msg_timestamp DESC) as tt2 ON tt.student = tt2.student ORDER BY tt.receive_msg_id DESC '.$limit;

			$query = $this->db->query($sql);
			$result = $query->result_array();
			$query_tmp = '';
			foreach($result as $key=>$item)
			{
				$result[$key]['msg'] = '';
				if(strlen($query_tmp) == 0)
				{
					$query_tmp = "msg_id='".$item['msg_id']."'";
				}
				else
				{
					$query_tmp .= " || msg_id='".$item['msg_id']."'";
				}
				#unset($result[$key]['msg_id']);
				unset($result[$key]['receive_f_id']);
			}
			//查询chat内容
			$query = "ql=select * where ".$query_tmp;
			$arrayResult = im_do("chatmessages","GET",array(),$query);
			if(isset($arrayResult,$arrayResult['entities']) && is_array($arrayResult['entities']) && count($arrayResult['entities']) > 0)
			{
				foreach($result as $key=>$item)
				{
					foreach($arrayResult['entities'] as $k=>$it)
					{
						if($item['msg_id'] == $it['msg_id'])
						{
							if(isset($it['payload'],$it['payload']['bodies'],$it['payload']['bodies'][0]))
							{
								$type = $it['payload']['bodies'][0]['type'];
								if($type == 'txt')
								{
									$result[$key]['msg'] = $it['payload']['bodies'][0]['msg'];
								}
								else if($type == 'audio')
								{
									$result[$key]['msg'] = "[语音]";
								}
								else if($type == 'img')
								{
									$result[$key]['msg'] = "[图片]";
								}
							}
							unset($arrayResult[$k]);
							break;
						}
					}
				}
			}
		}
		return $result;
		
	}

	/**
	 * 获取一个客服的聊天记录总数
	 * @parame	array
	 *		custom_name	string
	 * @return	$total	int
	 */
	public function get_custom_receive_chat_total($parame = array())
	{
		
		$total = 0;
		$user_name = $parame['custom_name'];
		if(isset($user_name))
		{
			$tmp = get_hash_db($user_name);
			$table = 't_msg_custom_receive_'.$tmp[1].$tmp[2];

			$sql = 'SELECT count(DISTINCT F_from_name) as total FROM '.$table;

			$query = $this->db->query($sql);
			$row = $query->row_array();
			$total = (int)$row['total'];
		}
		return $total;
		
	}
	
	/**
	 * 重置多个客服的密码
	 * @parame	$custom_name_list	array
	 * @return $result	int	1成功，0失败
	 */
	public function reset_customs_passwd($custom_name_list)
	{
		$result = 0;
		if(isset($custom_name_list) && is_array($custom_name_list) && count($custom_name_list) > 0)
		{
			$url_list = array();
			foreach($custom_name_list as $custom_name)
			{
				$url_list[] = 'users/'.$custom_name.'/password';
			}
			$data = array('newpassword'=>$this->my_config['custom_pwd']);
			$resultList =  im_do_multi($url_list,"PUT",$data);
			if(is_array($resultList) && count($resultList) > 0)
			{
				foreach($resultList as $item)
				{
					if(isset($item,$item['action']) && !isset($item['error']))
					{
						$result = 1;
					}
					else
					{
						$result = 0;
						break;
					}
				}
			}
		}
		//重置应用服务器上的客服密码
		if($result == 1)
		{
			//$custom_name_list
			$data = array(
				'F_custom_pwd' => $this->my_config['custom_pwd'],
				'F_custom_modifytime' => date('Y-m-d H:i:s',time()),
			);
			$this->db->where_in('F_custom_name', $custom_name_list);
			$this->db->update('t_custom', $data); 
		}

		return $result;
	}
	
	/**
	 * 删除一个客服
	 * @parame	$custom_name	string
	 */
	public function delete_custom($custom_name)
	{
		if(isset($custom_name))
		{
			//查询是否可以删除
			$flag = 0;
			$group_id = $this->F_group_id;
			if($group_id != 0)
			{
				$query = $this->db->get_where('t_custom', array('F_custom_name' => $custom_name,'F_groupid'=>$group_id), 1, 0);
				if($query->num_rows() > 0)
				{
					$flag = 1;
				}
			}
			else
			{
				$flag = 1;
			}
			if($flag == 1)
			{
				$tmp = get_hash_db($custom_name);
				//删除msg表的记录
				$table = 't_msg_custom_receive_'.$tmp[1].$tmp[2];
				$sql = 'DELETE FROM '.$table.' WHERE F_to_name = "'.$custom_name.'"';
				$this->db->query($sql);

				$table = 't_msg_custom_send_'.$tmp[1].$tmp[2];
				$sql = 'DELETE FROM '.$table.' WHERE F_from_name = "'.$custom_name.'"';
				$this->db->query($sql);
				//删除t_custom表示数据
				$sql = 'DELETE FROM t_custom WHERE F_custom_name = "'.$custom_name.'"';
				$this->db->query($sql);
				//删除t_custom_score表数据
				$sql = 'DELETE FROM t_custom_score WHERE F_to = "'.$custom_name.'"';
				$this->db->query($sql);
				//删除环信上的用户
				$resultList =  im_do("users/".$custom_name,"DELETE");
			}
		}
	}

}

/**
 * End of file m_custom.php
 */
/**
 * Location: ./app/model/m_custom.php
 */
