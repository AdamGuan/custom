<?php
class M_permission extends MY_Model {

	public function __construct() {
		parent :: __construct();
		$this -> load -> database();
	}
	
	/**
	 * 获取全部权限数据
	 * @return	$data	array二维
	 *		text		string
	 *		attributes	string
	 *		checked		string
	 *		iconCls		string
	 */
	public function get_permission_data()
	{
		$data = array();
		$list = $this->my_config['permission'];
		foreach($list as $item)
		{
			$data[] = array(
				'text'=>$item['desc'],
				'attributes'=>array('value'=>$item['value']),
				'checked'=>false,
				'iconCls'=>'icon-blank',
			);
		}
		return $data;
	}
	
	/**
	 * 获取某个角色权限数据
	 * @parame	$role_id	int
	 * @return	$data	array二维
	 *		text		string
	 *		attributes	string
	 *		checked		string
	 *		iconCls		string
	 */
	public function get_role_permission_data($role_id)
	{
		$data = array();
		if(isset($role_id) && $role_id > 0)
		{
			//
			$data = $this->get_permission_data();
			//
			$sql = 'SELECT F_role_privilege FROM t_role WHERE F_role_id = '.$role_id.' LIMIT 1';
			$query = $this->db->query($sql);
			if($query->num_rows() > 0)
			{
				$row = $query->row_array();
				$role_permission = $row['F_role_privilege'];
				$role_permission_list = explode(",",$role_permission);
				foreach($data as $key=>$item)
				{
					if(in_array($item['attributes']['value'],$role_permission_list))
					{
						$data[$key]['checked'] = true;
					}
				}
			}
		}
		return $data;
	}
	
	/**
	 * 更新角色权限
	 * @parame	$role_id	int
	 * @parame	$permision	string
	 * @return	$result	int	1成功，0失败
	 */
	public function update_role_permission_data($role_id,$permision)
	{
		$result = 0;
		if(isset($role_id) && $role_id > 0 && isset($permision))
		{
			//
			$all_permision = array();
			$data = $this->get_permission_data();
			foreach($data as $item)
			{
				$all_permision[] = $item['attributes']['value'];
			}
			//
			$permision_list = explode(",",$permision);
			if(is_array($permision_list) && count($permision_list) > 0 && isset($all_permision))
			{
				foreach($permision_list as $key=>$item)
				{
					if(!in_array($item,$all_permision))
					{
						unset($permision_list[$key]);
					}
				}
			}
			$permision = implode(",",$permision_list);

			$sql = 'UPDATE t_role SET F_role_privilege = "'.$permision.'" WHERE F_role_id = '.(int)$role_id;
			$query = $this->db->query($sql);
			if($this->db->affected_rows() > 0)
			{
				$result = 1;
			}
		}
		return $result;
	}
	
	/**
	 * 添加一个角色
	 * @parame	$role_name	string
	 * @parame	$permision	string
	 * @return	$result	int	>0成功，0失败
	 */
	public function add_a_role_data($parames = array())
	{
		$result = 0;
		if(isset($parames['permision'],$parames['role_name']))
		{
			$permision = $parames['permision'];
			$role_name = (string)$parames['role_name'];
			//查询角色名是否已存在
			$query = $this->db->get_where('t_role', array('F_role_name' => $role_name), 1,0);
			if (!($query->num_rows() > 0))
			{
				//
				$all_permision = array();
				$data = $this->get_permission_data();
				foreach($data as $item)
				{
					$all_permision[] = $item['attributes']['value'];
				}
				//
				$permision_list = explode(",",$permision);
				if(is_array($permision_list) && count($permision_list) > 0 && isset($all_permision))
				{
					foreach($permision_list as $key=>$item)
					{
						if(!in_array($item,$all_permision))
						{
							unset($permision_list[$key]);
						}
					}
				}
				$permision = implode(",",$permision_list);

				$data = array(
					'F_role_name' => $role_name ,
					'F_role_privilege' => $permision
				);
				$this->db->insert('t_role', $data); 
				if($this->db->insert_id() > 0)
				{
					$result = $this->db->insert_id();
				}
			}
		}
		return $result;
	}
	
	/**
	 * 删除一个角色
	 * @parame	$role_id	int
	 * @return	$result	int	1成功，0失败
	 */
	public function delete_role_data($role_id)
	{
		$result = 0;
		if(isset($role_id) && $role_id > 0)
		{
			$sql = 'DELETE FROM t_role WHERE F_role_id = '.(int)$role_id;
			$query = $this->db->query($sql);
			if($this->db->affected_rows() > 0)
			{
				$result = 1;
			}
		}
		return $result;
	}
	
	/**
	 * 获取所有角色
	 * @return	$list	array二维
	 *				F_role_id			int
	 *				F_role_name			string
	 *				F_role_privilege	string
	 *				F_groupid			int
	 */
	public function get_all_role($all = 0)
	{
		$list = array();
		if($all != 0)
		{
			$sql = 'SELECT * FROM t_role WHERE 1';
		}
		else
		{
			$sql = 'SELECT * FROM t_role WHERE F_role_privilege != "all"';
		}
		$query = $this->db->query($sql);
		if($query->num_rows() > 0)
		{
			$list = $query->result_array();
		}
		return $list;
	}

}

/**
 * End of file m_permission.php
 */
/**
 * Location: ./app/model/m_permission.php
 */