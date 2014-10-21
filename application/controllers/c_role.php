<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_Role extends MY_Controller {

	public function __construct() {
		parent :: __construct();
	}
	
	//展示角色页
	public function show_role($parames = array())
	{
		//检查是否有登录
		$data = $this->_check_login();
		if(is_array($data) && isset($data['redirect_url']))	//未登录
		{
			top_redirect($data['redirect_url']);
		}
		else	//已登录
		{
			//检查权限
			if(check_privilege($this->_get_current_class_method(__CLASS__,__METHOD__)))
			{
				//get page title
				$page_title = $this->_get_current_page_title(__FILE__,__CLASS__,__METHOD__);

				//
				$this -> load -> model('m_permission', 'mpermission');
				$permission_data_list = $this -> mpermission ->get_permission_data();
				$role_list = $this -> mpermission ->get_all_role();
				
				//data
				$data = array(
					'page_title'=>$page_title,
					'role_list_json'=>json_encode($role_list),
					'all_permission_data_list_json'=>json_encode($permission_data_list),
					'role_detail_url'=>get_role_detail_url(),
					'update_role_url'=>get_update_role_url(),
					'add_role_url'=>get_add_role_url(),
					'delete_role_url'=>get_delete_role_url(),
				);

				$this->_output_view("v_role", $data);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

	//获取角色信息
	function get_role_detail($parames = array())
	{
		//检查是否有登录
		$data = $this->_check_login();
		if(is_array($data) && isset($data['redirect_url']))	//未登录
		{
			top_redirect($data['redirect_url']);
		}
		else	//已登录
		{
			//检查权限
			if(check_privilege($this->_get_current_class_method(__CLASS__,__METHOD__)))
			{
				//
				$this -> load -> model('m_permission', 'mpermission');
				$role_id = isset($parames['role_id'])?(int)$parames['role_id']:0;
				$permission_data = $this -> mpermission ->get_role_permission_data($role_id);
				
				//data
				$data = array();
				if(is_array($permission_data) && count($permission_data) > 0)
				{
					$data = array(
						'permission_data_json'=>json_encode($permission_data),
					);
				}

				$this->_ajax_echo($data);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}
	
	//更新一个角色
	public function update_role($parames = array())
	{
		
		//检查是否有登录
		$data = $this->_check_login();
		if(is_array($data) && isset($data['redirect_url']))	//未登录
		{
			top_redirect($data['redirect_url']);
		}
		else	//已登录
		{
			//检查权限
			if(check_privilege($this->_get_current_class_method(__CLASS__,__METHOD__)))
			{
				//
				$role_id = isset($parames['role_id'])?$parames['role_id']:null;
				$permision = isset($parames['permision'])?$parames['permision']:null;
				$this -> load -> model('m_permission', 'mpermission');
				$result = $this -> mpermission ->update_role_permission_data($role_id,$permision);
				
				//data
				$data = array(
					'result'=>$result,
				);

				$this->_ajax_echo($data);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
		
	}
	
	//添加一个角色
	public function add_role($parames = array())
	{
		
		//检查是否有登录
		$data = $this->_check_login();
		if(is_array($data) && isset($data['redirect_url']))	//未登录
		{
			top_redirect($data['redirect_url']);
		}
		else	//已登录
		{
			//检查权限
			if(check_privilege($this->_get_current_class_method(__CLASS__,__METHOD__)))
			{
				//
				$this -> load -> model('m_permission', 'mpermission');
				$result = $this -> mpermission ->add_a_role_data($parames);
				
				//data
				$data = array(
					'result'=>$result,
				);

				$this->_ajax_echo($data);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
		
	}
	
	//删除一个角色
	public function delete_role($parames = array())
	{
		
		//检查是否有登录
		$data = $this->_check_login();
		if(is_array($data) && isset($data['redirect_url']))	//未登录
		{
			top_redirect($data['redirect_url']);
		}
		else	//已登录
		{
			//检查权限
			if(check_privilege($this->_get_current_class_method(__CLASS__,__METHOD__)))
			{
				//
				$this -> load -> model('m_permission', 'mpermission');
				$role_id = isset($parames['role_id'])?$parames['role_id']:0;
				$result = $this -> mpermission ->delete_role_data($role_id);
				
				//data
				$data = array(
					'result'=>$result,
				);

				$this->_ajax_echo($data);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
		
	}

}

/* End of file c_role.php */
/* Location: ./application/controllers/c_role.php */