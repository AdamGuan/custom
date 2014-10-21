<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_Manager extends MY_Controller {

	public function __construct() {
		parent :: __construct();
	}
	
	//展示管理员页
	public function show_manager($parames = array())
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
				$this -> load -> model('m_manager', 'mmanager');
				$manager_list = $this->mmanager->get_all_manager();

				$this -> load -> model('m_group', 'mgroup');
				$group_list = $this->mgroup->get_group_list();

				$this -> load -> model('m_permission', 'mpermission');
				$role_list = $this->mpermission->get_all_role(1);

				//data
				$data = array(
					'page_title'=>$page_title,
					'manager_list_json'=>json_encode($manager_list),
					'group_list_json'=>json_encode($group_list),
					'role_list_json'=>json_encode($role_list),
					'manager_add_url'=>get_manager_add_url(),
					'manager_modify_url'=>get_manager_modify_url(),
					'manager_delete_url'=>get_manager_delete_url(),
				);

				$this->_output_view("v_manager", $data);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

	//修改管理员
	public function manager_modify($parames = array())
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
				$this -> load -> model('m_manager', 'mmanager');
				$result = $this->mmanager->modify_manager($parames);

				//data
				$data = array(
					'result'=>$result
				);

				$this->_ajax_echo($data);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

	//删除管理员
	public function manager_delete($parames = array())
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
				$this -> load -> model('m_manager', 'mmanager');
				$manager_id = isset($parames['manager_id'])?(int)$parames['manager_id']:null;
				$result = $this->mmanager->delete_manager($manager_id);

				//data
				$data = array(
					'result'=>$result
				);

				$this->_ajax_echo($data);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}
	
	//添加管理员
	public function manager_add($parames = array())
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
				$this -> load -> model('m_manager', 'mmanager');
				$result = $this->mmanager->add_a_manager($parames);

				//data
				$data = array(
					'result'=>$result
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

/* End of file c_manager.php */
/* Location: ./application/controllers/c_manager.php */