<?php
class M_score extends MY_Model {

	private $F_group_id;

	public function __construct() {
		parent :: __construct();
		$this -> load -> database();
		$this->F_group_id = (int)$this -> session -> userdata('F_group_id');
	}
	
	/**
	 * 获取评价列表
	 * @return	$list	array二维
	 *				F_id			int
	 *				F_from			string
	 *				t_student_name	string
	 *				F_to			string
	 *				F_score			string
	 *				F_content		string
	 *				F_createtime	datetime
	 */
	public function get_custom_score_list($parame)
	{
		
		$list = array();

		if($this->F_group_id != 0)
		{
			//获取地区内的客服列表
			$custom_name_list = array();
			$sql = 'SELECT F_custom_name FROM t_custom WHERE F_groupid = '.$this->F_group_id;
			$query = $this->query($sql);
			if($query->num_rows() > 0)
			{
				foreach($query->result_array() as $item)
				{
					$custom_name_list[] = $item['F_custom_name'];
				}
			}
			
		}
		
		$limit = '';
		if(isset($parame['page'],$parame['rows']))
		{
			$offset = (int)(($parame['page'] -1)*$parame['rows']);
			$size = (int)$parame['rows'];
			if($offset < 0)$offset = 0;
			if($size < 0)$size = 0;
			$limit = 'LIMIT '.$offset.','.$size;
		}

		if(isset($custom_name_list))
		{
			$values = '"'.implode('","',$custom_name_list).'"';
			$sql = 'SELECT t1.*,t_student.t_student_name FROM (SELECT * FROM t_custom_score WHERE F_to IN ('.$values.') ORDER BY F_createtime DESC '.$limit.') as t1 LEFT JOIN t_student ON t1.F_from= t_student.t_id';
		}
		else
		{
			$sql = 'SELECT t1.*,t_student.t_student_name FROM (SELECT * FROM t_custom_score WHERE 1 ORDER BY F_createtime DESC '.$limit.') as t1 LEFT JOIN t_student ON t1.F_from= t_student.t_id';
		}
		$query = $this->db->query($sql);
		if($query->num_rows() > 0)
		{
			$list = $query->result_array();
			foreach($list as $key=>$item)
			{
				$list[$key]['F_score'] = (float)$list[$key]['F_score'];
				if($list[$key]['F_score'] <= 3)
				{
					$list[$key]['F_score'] = '一般';
				}else if($list[$key]['F_score'] > 3 && $list[$key]['F_score'] <= 4)
				{
					$list[$key]['F_score'] = '好';
				}
				else if($list[$key]['F_score'] > 4)
				{
					$list[$key]['F_score'] = '很好';
				}
			}
		}
		return $list;
		
	}

	public function get_custom_score_total()
	{
		
		$total = 0;

		if($this->F_group_id != 0)
		{
			//获取地区内的客服列表
			$custom_name_list = array();
			$sql = 'SELECT F_custom_name FROM t_custom WHERE F_groupid = '.$this->F_group_id;
			$query = $this->query($sql);
			if($query->num_rows() > 0)
			{
				foreach($query->result_array() as $item)
				{
					$custom_name_list[] = $item['F_custom_name'];
				}
			}
			
		}
		
		if(isset($custom_name_list))
		{
			$values = '"'.implode('","',$custom_name_list).'"';
			$sql = 'SELECT count(F_id) as total FROM t_custom_score WHERE F_to IN ('.$values.')';
		}
		else
		{
			$sql = 'SELECT count(F_id) as total FROM t_custom_score WHERE 1';
		}
		$query = $this->db->query($sql);
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$total = $row['total'];
		}
		return $total;
		
	}
	
	/**
	 * 删除评价
	 * @parame	$id	string	","分隔ID
	 * @return	$result	int	1成功，0失败
	 */
	public function del_custom_score($ids)
	{
		
		$result = 0;
		if(isset($ids))
		{
			$ids = explode(",",$ids);
			//查询是否可以删除
			$flag = 0;
			if($this->F_group_id != 0)
			{
				
				$idstring = implode(',',$ids);
				$sql = 'SELECT F_to FROM t_custom_score WHERE F_id IN('.$idstring.')';
				$query = $this->db->query($sql);
				if($query->num_rows() > 0)
				{
					$custom_name_list = array();
					foreach($query->result_array() as $item)
					{
						$custom_name_list[] = $item['F_to'];
					}
					$custom_name_str = '"'.implode('","',$custom_name_list).'"';
					$sql = 'SELECT F_id FROM t_custom WHERE F_groupid = '.$this->F_group_id.' AND F_custom_name IN('.$custom_name_str.')';
					$query = $this->db->query($sql);
					if($query->num_rows() > 0)
					{
						$flag = 1;
					}
				}
			}
			else
			{
				$idstring = implode(',',$ids);
				$flag = 1;
			}

			if($flag == 1 && isset($idstring))
			{
				$sql = 'DELETE FROM t_custom_score WHERE F_id IN('.$idstring.')';
				$query = $this->db->query($sql);
				if($this->db->affected_rows() > 0)
				{
					$result = 1;
				}
			}
		}
		return $result;
		
	}

}

/**
 * End of file m_score.php
 */
/**
 * Location: ./app/model/m_score.php
 */