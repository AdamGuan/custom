<?php
class M_manager extends MY_Model {

	public function __construct() {
		parent :: __construct();
		$this -> load -> database();
	}
	
	/**
	 * 修改管理员
	 * @parame	$parames	array
	 *		manager_id	int
	 *		user_name	string	optional
	 *		user_pwd	string	optional
	 *		role_id		int		optional
	 *		group_id	int		optional
	 * @return	$result	int	1成功，0失败
	 */
	public function modify_manager($parames = array())
	{
		$result = 0;
		if(isset($parames['manager_id']))
		{
			$data = array();
			//确定是否更新role_id
			if(isset($parames['role_id']))
			{
				$sql = 'SELECT F_role_id FROM t_role WHERE F_role_id = '.(int)$parames['role_id'].' LIMIT 1';
				$query = $this->db->query($sql);
				if($query->num_rows() > 0)
				{
					$data['F_role_id'] = $parames['role_id'];
				}
			}
			//确定是否更新group_id
			if(isset($parames['group_id']))
			{
				$sql = 'SELECT F_id FROM t_group WHERE F_id = '.(int)$parames['group_id'].' LIMIT 1';
				$query = $this->db->query($sql);
				if($query->num_rows() > 0)
				{
					$data['F_group_id'] = $parames['group_id'];
				}
			}
			//确定是否更新user_name
			if(isset($parames['user_name']))
			{
				$data['F_user_login_name'] = $parames['user_name'];
			}
			//确定是否更新user_pwd
			if(isset($parames['user_pwd']))
			{
				$data['F_user_login_pwd'] = sha1($parames['user_pwd']);
			}
			
			if(count($data) > 0)
			{
				$datetime = date('Y-m-d H:i:s',time());
				$data['F_update_time'] = $datetime;
				$this->db->where('F_user_id', (int)$parames['manager_id']);
				$this->db->update('t_user', $data);
				if($this->db->affected_rows() > 0)
				{
					$result = 1;
				}
			}
		}
		return $result;
	}
	
	/**
	 * 添加一个管理员
	 * @parame	$parames	array
	 *				role_id		int
	 *				username	string
	 *				userpwd		string
	 *				group_id	int
	 * @return	$result	int	>0成功，0失败
	 */
	public function add_a_manager($parames = array())
	{
		$result = 0;
		if(isset($parames['role_id'],$parames['username'],$parames['userpwd'],$parames['group_id']))
		{
			//查询角色ID是否存在
			$sql = 'SELECT F_role_id FROM t_role WHERE F_role_id = '.(int)$parames['role_id'].' LIMIT 1';
			$query = $this->db->query($sql);
			if($query->num_rows() > 0)
			{
				//查询组ID是否存在
				$status = 0;
				if($parames['group_id'] != 0)
				{
					$sql = 'SELECT F_id FROM t_group WHERE F_id = '.(int)$parames['group_id'].' LIMIT 1';
					$query = $this->db->query($sql);
					if($query->num_rows() > 0)
					{
						$status = 1;
					}
				}
				if($status == 1)
				{
					$datetime = date('Y-m-d H:i:s',time());
					$data = array(
						'F_user_login_name' => $parames['username'] ,
						'F_user_login_pwd' => sha1($parames['userpwd']),
						'F_role_id' => $parames['role_id'],
						'F_group_id' => $parames['group_id'],
						'F_create_time' => $datetime,
						'F_update_time' => $datetime
					);
					$this->db->insert('t_user', $data); 
					if($this->db->insert_id() > 0)
					{
						$result = $this->db->insert_id();
					}
				}
			}
		}
		return $result;
	}
	
	/**
	 * 删除一个管理员
	 * @parame	$manager_id	int
	 * @return	$result	int	1成功，0失败
	 */
	public function delete_manager($manager_id)
	{
		$result = 0;
		if(isset($manager_id) && $manager_id > 0)
		{
			$sql = 'DELETE FROM t_user WHERE F_user_id = '.(int)$manager_id;
			$query = $this->db->query($sql);
			if($this->db->affected_rows() > 0)
			{
				$result = 1;
			}
		}
		return $result;
	}
	
	/**
	 * 获取所有管理员
	 * @return	$list	array二维
	 *				F_user_id			int
	 *				F_user_login_name	string
	 *				F_create_time		datetime
	 *				F_role_id			int
	 *				F_role_name			string
	 *				F_group_id			int
	 *				F_groupname			string
	 */
	public function get_all_manager()
	{
		$list = array();
		$sql = 'SELECT u.F_user_id,u.F_user_login_name,u.F_create_time,u.F_role_id,r.F_role_name,u.F_group_id,g.F_groupname FROM t_user u LEFT JOIN t_role r ON u.F_role_id = r.F_role_id LEFT JOIN t_group g ON u.F_group_id = g.F_id ORDER BY F_create_time DESC';
		$query = $this->db->query($sql);
		if($query->num_rows() > 0)
		{
			$list = $query->result_array();
		}
		return $list;
	}

}

/**
 * End of file m_manager.php
 */
/**
 * Location: ./app/model/m_manager.php
 */