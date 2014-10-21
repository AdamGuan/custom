<?php
class M_login extends MY_Model {

	public function __construct() {
		parent :: __construct();
		$this -> load -> database();
	}
	
	/**
	 * 检查用户名，密码是否正确
	 * @parame	$username	string
	 * @parame	$pwd		string
	 * @return	$result	array	
	 */
	public function check_user_login($username,$pwd)
	{
		$result = array();
		if(isset($username,$pwd))
		{
			$this->db->select('*');
			$this->db->from('t_user');
			$array = array('F_user_login_name' => $username, 'F_user_login_pwd' => sha1($pwd));
			$this->db->where($array); 
			$this->db->limit(1);
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				$result = $query->row_array();
				//获取角色权限
				$sql = 'SELECT * FROM t_role WHERE F_role_id = '.$result['F_role_id'].' LIMIT 1';
				$query = $this->db->query($sql);
				if ($query->num_rows() > 0)
				{
					$row = $query->row_array();
				}
				$result['F_role_id'] = 0;
				$result['F_role_name'] = "";
				$result['F_role_privilege'] = "0";
				if(isset($row,$row['F_role_id']))
				{
					$result['F_role_id'] = $row['F_role_id'];
					$result['F_role_name'] = $row['F_role_name'];
					$result['F_role_privilege'] = $row['F_role_privilege'];
				}
			} 
		}
		return $result;
	}

	/**
	 * 登录页面检查是否登录
	 * 
	 * @return $result	int	1登录，0未登录.
	 */
	public function login_page_check_login() {
		$result = 0;

		$F_user_id = $this -> session -> userdata('F_user_id');
		if(isset($F_user_id))
		{
			//
			$sql = 'SELECT F_user_id FROM t_user WHERE F_user_id ='.(int)$F_user_id.' LIMIT 1';
			$query = $this->db->query($sql);
			if ($query->num_rows() > 0)
			{
				$result = 1;
			} 
		}

		return $result;
	}

	/**
	 * 登出
	 */
	public function login_out() {
		$F_user_id = $this -> session -> userdata('F_user_id');
		if(isset($F_user_id))
		{
			$session_array = array(
					'F_user_login_name'=>'',
					'F_user_id'=>'',
					'F_role_privilege'=>'',
					'F_group_id'=>'',
				);
			$this -> session -> unset_userdata($session_array);
		}
		return 1;
	}

}

/**
 * End of file m_login.php
 */
/**
 * Location: ./app/model/m_login.php
 */