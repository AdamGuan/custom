<?php
class M_api extends MY_Model {

	public function __construct() {
		parent :: __construct();
		$this -> load -> database();
		//load helper
		$this->load->helper(array('im'));
	}
	
	/**
	 * 写入msg到应用数据库
	 * @parame	$parames
	 *				from		string
	 *				to			string
	 *				msg_id		string
	 *				timestamp	string
	 *				who_to_who	int		1:客服发给用户,0:用户发给客服
	 * @return	@result			int		大于0则成功，否则失败
	 */
	public function write_msg($parames = array())
	{
		$result  = 0;
		if(isset($parames['from'],$parames['to'],$parames['msg_id'],$parames['timestamp'],$parames['who_to_who']))
		{
			$F_from_name = $parames['from'];
			$F_to_name = $parames['to'];
			$F_msg_id = $parames['msg_id'];
			$F_timestamp = $parames['timestamp'];
			$who_to_who = $parames['who_to_who'];

			//客服发给用户
			if($who_to_who == 1)
			{
				$tmp = get_hash_db($F_from_name);
				$table = 't_msg_custom_send_'.$tmp[1].$tmp[2];
				$sql = 'INSERT INTO '.$table.'(`F_from_name`,`F_to_name`,`F_msg_id`,`F_timestamp`) VALUES("'.$F_from_name.'","'.$F_to_name.'","'.$F_msg_id.'","'.$F_timestamp.'")';
				$this->db->query($sql);
				$result = (int)$this->db->insert_id();
			}
			else if($who_to_who == 0)	//用户发给客服
			{
				
				$table = 't_msg_stduent_send_'.date('Ym',(int)$F_timestamp/1000);
				$sql = 'INSERT INTO '.$table.'(`F_from_name`,`F_to_name`,`F_msg_id`,`F_timestamp`) VALUES("'.$F_from_name.'","'.$F_to_name.'","'.$F_msg_id.'","'.$F_timestamp.'")';
				$this->db->query($sql);
				$result = (int)$this->db->insert_id();
				
				if(isset($result) && $result > 0)
				{
					$result = 0;
					$tmp = get_hash_db($F_to_name);
					$table = 't_msg_custom_receive_'.$tmp[1].$tmp[2];
					$sql = 'INSERT INTO '.$table.'(`F_from_name`,`F_to_name`,`F_msg_id`,`F_timestamp`) VALUES("'.$F_from_name.'","'.$F_to_name.'","'.$F_msg_id.'","'.$F_timestamp.'")';
					$this->db->query($sql);
					$result = (int)$this->db->insert_id();
				}
			}
		}
		return $result;
	}

	/**
	 * 写入评价
	 * @parame	$parames
	 *				from		string
	 *				to			string
	 *				score		float
	 *				content		string
	 * @return	@result			int		大于0则成功，否则失败
	 */
	public function write_grade($parames = array())
	{
		$result  = 0;
		if(isset($parames['from'],$parames['to'],$parames['score'],$parames['content']))
		{
			$F_from = $parames['from'];
			$F_to = $parames['to'];
			$F_score = (float)$parames['score'];
			$F_content = $parames['content'];
			$F_createtime = date('Y-m-d H:i:s',time());

			$table = 't_custom_score';
			$sql = 'INSERT INTO '.$table.'(`F_from`,`F_to`,`F_score`,`F_content`,`F_createtime`) VALUES("'.$F_from.'","'.$F_to.'",'.$F_score.',"'.$F_content.'","'.$F_createtime.'")';
			$this->db->query($sql);
			$result = $this->db->insert_id();
		}
		return $result;
	}
	
	/**
	 * 查找一个客服
	 * @parame	$parames	array
	 *				local	string	地区,如：广东
	 * @return $custom	array
	 *		F_custom_name		客服账户名
	 *		F_custom_nickname	客服昵称
	 */
	public function find_custom($parames = array())
	{
		$custom = array();
		if(isset($parames['local']))
		{
			$local = trim($parames['local']);
			$sql = 'SELECT cn.F_custom_name,cn.F_custom_nickname FROM t_custom as cn,t_group as g WHERE g.F_groupname = "'.$local.'" AND g.F_id = cn.F_groupid';
			$query = $this->db->query($sql);
			if($query->num_rows() > 0)
			{
				$result = $query->result_array();
				$online_list = array();
				$url_list = array();
				foreach($result as $row)
				{
					$url_list[] = 'users/'.$row['F_custom_name'].'/status';
				}
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
						if($online == 'online')
						{
							$online_list[] = $item;
						}
					}
				}
				//选择一个客服
				if(isset($online_list[0]))
				{
					$max = count($online_list) - 1;
					$custom = $online_list[rand(0,$max)];
				}
				else
				{
					$max = count($result) - 1;
//					$custom = $result[rand(0,$max)]['F_custom_name'];
					$custom = $result[rand(0,$max)];
				}
			}
		}
		return $custom;
	}
	
	/**
	 * 注册一个学生到环信
	 * @$parames	array
	 *		username	string
	 * @return	array
	 *				username	string
	 *				userpwd		string
	 *				key			string
	 */
	public function register_student($parames = array())
	{
		$pwd = null;
		$user_name = null;
		if(isset($parames['username']))
		{
			//查询在应用数据库中是否存在
			$sql = 'SELECT t_id,t_student_pwd FROM t_student WHERE t_student_name = "'.$parames['username'].'" LIMIT 1';
			$query = $this->db->query($sql);
			if($query->num_rows() > 0)	//应用数据库中存在
			{
				$row = $query->row_array();
				$user_name = $row['t_id'];
				//查询环信用户
				$arrayResult = $this->get_user_from_im($row['t_id']);
				if(isset($arrayResult,$arrayResult['action']) && !isset($arrayResult['error']))	//在环信中存在
				{
					$pwd = $row['t_student_pwd'];
				}
				else	//在环信中不存在
				{
					//注册到环信
					$create_pwd = generate_pwd(6);
					$arrayResult = $this->add_user_to_im($parames['username'],$create_pwd);
					if(isset($arrayResult,$arrayResult['action']) && !isset($arrayResult['error']))	//注册到环信成功
					{
						$pwd = $create_pwd;
					}
					else	//注册到环信失败
					{
						//删除应用数据库中的数据
						$sql = 'DELETE FROM t_student WHERE t_student_name = "'.$parames['username'].'"';
						$this->db->query($sql);
					}
				}
			}
			else	//在应用数据库中不存在
			{
				//写入应用数据库
				$create_pwd = generate_pwd(6);
				$sql = 'INSERT INTO t_student(`t_student_name`,`t_student_pwd`) VALUES("'.$parames['username'].'","'.$create_pwd.'")';
				$query = $this->db->query($sql);
				if($this->db->insert_id() > 0)	//写入应用数据库成功
				{
					//注册到环信
					$username = $this->db->insert_id();
					$arrayResult = $this->add_user_to_im($username,$create_pwd);
					if(isset($arrayResult,$arrayResult['action']) && !isset($arrayResult['error']))	//注册到环信成功
					{
						$pwd = $create_pwd;
					}
					else //注册到环信失败
					{
						//删除应用数据库数据
						$sql = 'DELETE FROM t_student WHERE t_student_name = "'.$parames['username'].'"';
						$this->db->query($sql);
					}
				}
			}
		}

		//设置key
		
		if(!is_null($user_name) && !is_null($pwd))
		{
			$key = generate_pwd(10);
			$data = array(
				't_student_key' => $key,
			);
			$this->db->where('t_student_name', $parames['username']);
			$this->db->update('t_student', $data); 
			if(!($this->db->affected_rows() > 0))
			{
				unset($key);
			}
			else
			{
				return array('username'=>$user_name,'userpwd'=>$pwd,'key'=>$key);
			}
		}
		
//		return array('username'=>$user_name,'userpwd'=>$pwd);
		return array('username'=>null,'userpwd'=>null,'key'=>null);
	}
	
	/**
	 * 获取地区列表
	 */
	public function get_local_list()
	{
		$local_list = array();
		$sql = 'SELECT F_groupname FROM t_group WHERE 1 ORDER BY F_groupname';
		$query = $this->db->query($sql);
		if($query->num_rows() > 0)
		{
			$local_list = $query->result_array();
		}
		return $local_list;
	}
	
	/** 
	* 添加用户到环信
	*/
	protected function add_user_to_im($username,$userpwd)
	{
		$data=array(
			"username"=>(string)$username,
			"password"=>(string)$userpwd,
		);
		
		return im_do("users","POST",$data);
	}

	/** 
	* 获取一个环信用户
	*/
	protected function get_user_from_im($username)
	{
		$username = (string)$username;
		return im_do("users/".$username,"GET");
	}

	/** 
	* 删除一个环信用户
	*/
	protected function del_user_in_im($username)
	{
		$username = (string)$username;
		return im_do("users/".$username,"DELETE");
	}

	/**
	 * 修改客服昵称
	 * @parame	$parames	array
	 *				custom_name		客服账户名
	 *				custom_nickname	客服昵称
	 * @return $result	int
	 */
	public function modify_custom_nickname($parames = array())
	{
		$result = 0;
		if(isset($parames['custom_nickname'],$parames['custom_name']))
		{
			$valid = 0;
			//验证昵称有效性
			$len = strlen(trim($parames['custom_nickname']));
			if($len >= 1 && $len <= 20)
			{
				$valid = 1;
			}

			if($valid > 0)
			{
				//修改昵称
				$data = array(
					'F_custom_nickname' => trim($parames['custom_nickname']),
					'F_custom_modifytime' => date('Y-m-d H:i:s',time()),
				);
				$this->db->where('F_custom_name', $parames['custom_name']);
				$this->db->update('t_custom', $data); 
				if($this->db->affected_rows() > 0)
				{
					$result = 1;
				}
			}
		}
		return $result;
	}

	/**
	 * 获取客服信息
	 * @parame	$parames	array
	 *				custom_name		客服账户名
	 *				custom_pwd		客服密码
	 * @return $info	array
	 *			F_custom_name		string
	 *			F_custom_nickname	string
	 *			F_groupname			string
	 *			key					string
	 */
	 
	public function get_custom_info($parames = array())
	{
		$info = array();
		if(isset($parames['custom_pwd'],$parames['custom_name']))
		{
			//验证用户有效
			$this->db->select('F_custom_name,F_custom_nickname,F_groupname');
			$this->db->from('t_custom');
			$this->db->join('t_group', 't_custom.F_groupid = t_group.F_id');
			$this->db->where(array('F_custom_name' => $parames['custom_name'],'F_custom_pwd'=>$parames['custom_pwd']));
			$this->db->limit(1, 0);
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				$info = $query->row_array();
				//设置key
				$key = generate_pwd(10);
				$data = array(
					'F_custom_key' => $key,
					'F_custom_modifytime' => date('Y-m-d H:i:s',time()),
				);
				$this->db->where('F_custom_name', $parames['custom_name']);
				$this->db->update('t_custom', $data); 
				if($this->db->affected_rows() > 0)
				{
					$info['key'] = $key;
				}
				else
				{
					$info = array();
				}
			}
		}
		return $info;
	}
	

	/**
	 * 修改客服密码
	 * @parame	$parames	array
	 *				custom_name			客服账户名
	 *				custom_pwd_current	客服当前密码
	 *				custom_pwd_new		客服新密码
	 * @return $result	int
	 */
	public function modify_custom_pwd($parames = array())
	{
		$result = 0;
		
		if(isset($parames['custom_pwd_current'],$parames['custom_pwd_new'],$parames['custom_name']))
		{
			$valid = 0;
			//验证密码有效性
			$valid = preg_match('/^[0-9a-zA-Z\-_]{6,20}$/i',trim($parames['custom_pwd_new']));

			if($valid > 0)
			{
				//验证用户有效
				$query = $this->db->get_where('t_custom', array('F_custom_name' => $parames['custom_name'],'F_custom_pwd'=>$parames['custom_pwd_current']), 1, 0);
				
				if ($query->num_rows() > 0)
				{
					//修改环信中客服密码
					$url = 'users/'.$parames['custom_name'].'/password';
					$data = array('newpassword'=>trim($parames['custom_pwd_new']));
					$im_do_result =  im_do($url,"PUT",$data);
					if(isset($im_do_result,$im_do_result['action']) && !isset($im_do_result['error']))
					{
						//修改应用服务器中客服密码
						$data = array(
							'F_custom_pwd' => trim($parames['custom_pwd_new']),
							'F_custom_modifytime' => date('Y-m-d H:i:s',time()),
						);
						$this->db->where('F_custom_name', $parames['custom_name']);
						$this->db->update('t_custom', $data); 
						if($this->db->affected_rows() > 0)
						{
							$result = 1;
						}
					}
				}
				
			}
		}
		
		return $result;
	}
	
	/**
	 * 检查key是否有效
	 * @parame	$name	string
	 * @parame	$type	0用户，1客服
	 * @return	$result	string
	 */
	public function get_key($name,$type)
	{
		$key = null;
		
		if(isset($name,$type))
		{
			if($type == 0)	//用户
			{
				$this->db->select('t_student_key');
				$this->db->from('t_student');
				$this->db->where(array('t_id' => $name)); 
				$this->db->limit(1);
				$query = $this->db->get();

				if ($query->num_rows() > 0)
				{
					$row = $query->row_array();
					$key = $row['t_student_key'];
				}
			}
			else	//客服
			{
				$this->db->select('F_custom_key');
				$this->db->from('t_custom');
				$this->db->where(array('F_custom_name' => $name)); 
				$this->db->limit(1);
				$query = $this->db->get();
				if ($query->num_rows() > 0)
				{
					$row = $query->row_array();
					$key = $row['F_custom_key'];
				}
			}
		}
		
		return $key;
	}
	
	/**
	 * 检测客服对用户是否存在关系
	 * @parame	$parames	array
	 *				custom_name		客服账户名
	 *				user_name		客服当前密码
	 * @return $result	int
	 */
	 
	protected function check_custom_relation($parames = array())
	{
		$result = 0;

		if(isset($parames['custom_name'],$parames['user_name']))
		{
			$valid = 0;
			//验证客服有效性

			$query = $this->db->get_where('t_custom', array('F_custom_name' => $parames['custom_name']), 1, 0);
			if ($query->num_rows() > 0)
			{
				$valid = 1;
			}
			
			//验证用户有效性
			if($valid > 0)
			{
				$valid = 0;	
				$query = $this->db->get_where('t_student', array('t_id' => $parames['user_name']), 1, 0);
				if ($query->num_rows() > 0)
				{
					$valid = 1;
				}
			}

			if($valid > 0)
			{
				$valid = 0;
				//查找关系是否已存在
				$query = $this->db->get_where('t_custom_relation', array('F_custom_name' => $parames['custom_name'],'F_student_name'=>$parames['user_name']), 1, 0);
				if ($query->num_rows() <= 0)
				{
					$valid = 1;
				}
				//添加客服关系
				if($valid > 0)
				{
					$result = 1;
				}
			}
			
		}
		
		
		return $result;
	}
	
	/**
	 * 添加客服对用户关系
	 * @parame	$parames	array
	 *				custom_name		客服账户名
	 *				user_name		客服当前密码
	 * @return $result	int
	 */
	 
	public function add_custom_relation($parames = array())
	{
		$result = 0;
		
		$valid = 0;
		$valid = $this->check_custom_relation($parames);
		//添加客服关系
		if($valid == 1)
		{
			$data = array(
				'F_custom_name' => $parames['custom_name'],
				'F_student_name' => $parames['user_name'],
				'F_add_time' => date('Y-m-d H:i:s',time())
			);
			$this->db->insert('t_custom_relation', $data); 
			if($this->db->insert_id() > 0)
			{
				$result = 1;
			}
		}
		
		return $result;
	}
	
	/**
	 * 删除客服对用户关系
	 * @parame	$parames	array
	 *				custom_name		客服账户名
	 *				user_name		客服当前密码
	 * @return $result	int
	 */
	 
	public function delete_custom_relation($parames = array())
	{
		$result = 0;
		
		$valid = 0;
		$valid = $this->check_custom_relation($parames);
		//删除客服关系
		if($valid != 1)
		{
			$data = array(
				'F_custom_name' => $parames['custom_name'],
				'F_student_name' => $parames['user_name'],
			);
			$this->db->where($data);
			$this->db->delete('t_custom_relation'); 
			if($this->db->affected_rows() > 0)
			{
				$result = 1;
			}
		}
		
		return $result;
	}
	
	/**
	 * 查询某个客服对用户关系
	 * @parame	$parames	array
	 *				custom_name		客服账户名
	 * @return $list	array
	 *				add_datetime	datetime
	 *				user_name		string
	 *				register_name	string
	 */
	 
	public function get_custom_relation($parames = array())
	{
		$list = array();
		if(isset($parames['custom_name']))
		{
			$valid = 0;
			//验证客服有效性

			$query = $this->db->get_where('t_custom', array('F_custom_name' => $parames['custom_name']), 1, 0);
			if ($query->num_rows() > 0)
			{
				$valid = 1;
			}
			//获取客服对用户的关系
			if($valid > 0)
			{
				/*
				$this->db->select('tcr.F_add_time as add_datetime,ts.t_id as user_name,ts.t_student_name as register_name');
				$this->db->from('t_custom_relation as tcr,t_student as ts');
				
				$data = array(
					'tcr.F_custom_name'=>$parames['custom_name'],
					'tcr.F_student_name'=>'`ts.t_id`',
				);
				
				$data = '`tcr`.`F_custom_name`="'.(string)$parames['custom_name'].'" AND `tcr`.`F_student_name` = `ts`.`t_id`';
				$this->db->where($data); 
				
				$this->db->order_by('add_datetime desc'); 
				$query = $this->db->get();
				*/
				$sql = 'SELECT tcr.F_add_time as add_datetime,ts.t_id as user_name,ts.t_student_name as register_name FROM t_custom_relation as tcr,t_student as ts WHERE `tcr`.`F_custom_name`="'.(string)$parames['custom_name'].'" AND `tcr`.`F_student_name` = `ts`.`t_id` ORDER BY add_datetime desc';
				$query = $this->db->query($sql);
				if ($query->num_rows() > 0)
				{
					$list = $query->result_array();
				}
			}
		}
		
		return $list;
	}
	
}

/**
 * End of file m_custom.php
 */
/**
 * Location: ./app/model/m_custom.php
 */
