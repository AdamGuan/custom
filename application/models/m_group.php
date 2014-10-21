<?php
/**
 * 环信群组
 */
class M_group extends MY_Model {

	private $F_group_id;

	public function __construct() {
		parent :: __construct();
		$this -> load -> database();

		$this->F_group_id = $this -> session -> userdata('F_group_id');
	}
	
	/**
	 * 创建一个组
	 * @parame	$groupname	string
	 * @return $groupid	int	组ID(没有创建成功返回0)
	 */
	public function create_group($groupname)
	{
		$groupid = 0;
		
		if(isset($groupname))
		{
			//检查在应用数据库是否已经存在
			$sql = 'SELECT F_id FROM t_group WHERE F_groupname = "'.$groupname.'" LIMIT 1';
			$query = $this->db->query($sql);
			if($query->num_rows() <= 0)
			{
				//聊天组添加到应用数据库
				$sql = 'INSERT INTO t_group(`F_groupname`) VALUES("'.$groupname.'")';
				$this->db->query($sql);
				$groupid = $this->db->insert_id();
			}
		}
		return $groupid;
	}
	
	/**
	 * 获取全部的组
	 * @return	$list	array	二维
	 *		F_id		组ID
	 *		F_groupname	组名
	 */
	public function get_group_list()
	{
		$list = array();
		$where = '1';
		if($this->F_group_id != 0)
		{
			$where .= ' AND F_id = '.(int)$this->F_group_id;
		}
		$sql = 'SELECT * FROM t_group WHERE '.$where;
		$query = $this->db->query($sql);
		if($query->num_rows() > 0)
		{
			$list = $query->result_array();
		}
		return $list;
	}
	
	/**
	 * 更新组
	 * @parame	$parames	array
	 *		group_id	int
	 *		group_name	string
	 * @return	$result	int	0失败，1成功
	 */
	public function modify_group($parames = array())
	{
		$result = 0;
		if(isset($parames['group_id'],$parames['group_name']))
		{
			$F_id = (int)$parames['group_id'];
			$F_groupname = (string)$parames['group_name'];
			//确认F_groupname是否已存在
			$this->db->select('F_id');
			$this->db->where('F_id !=', $F_id);
			$this->db->where('F_groupname =', $F_groupname);
			$query = $this->db->get('t_group');
			if($query->num_rows() <= 0)
			{
				//更新
				$data = array(
					'F_groupname' => $F_groupname
				);
				$this->db->where('F_id', $F_id);
				$this->db->update('t_group', $data);
				if($this->db->affected_rows() > 0)
				{
					$result = 1;
				}
			}
		}
		return $result;
	}

	/**
	 * 删除一个组
	 * @parame	$groupid	int
	 * @return $result	int	1成功，0失败
	 */
	public function delete_group($groupid)
	{
		$result = 0;
		
		if(isset($groupid))
		{
			$groupid = (int)$groupid;
			//检查在应用数据库是否已经存在
			$sql = 'DELETE FROM t_group WHERE F_id = '.$groupid.' LIMIT 1';
			$query = $this->db->query($sql);
			if($this->db->affected_rows() > 0)
			{
				$result = 1;
			}
		}
		return $result;
	}

}

/**
 * End of file m_group.php
 */
/**
 * Location: ./app/model/m_group.php
 */