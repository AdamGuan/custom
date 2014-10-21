<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_Group extends MY_Controller {

	public function __construct() {
		parent :: __construct();
	}
	
	//展示组页
	public function show_group($parames = array())
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
				
				//data
				$data = array(
					'page_title'=>$page_title,
					'group_list_url'=>get_group_list_url(),
					'modify_group_url'=>modify_group_url(),
					'delete_group_url'=>get_delete_group_url(),
					'add_group_url'=>get_add_group_url(),
				);
				
				$this->_output_view("v_group", $data);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
		
	}
	
	//获取group list
	public function get_group_list($parames = array())
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

				//获取组列表
				$this -> load -> model('m_group', 'mgroup');
				$group_list = $this->mgroup->get_group_list();

				//echo
				$this->_ajax_echo($group_list);
			}
			else	//没有权限
			{
				//echo
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

	/**
	 * 修改group
	 * @parame $parames array
	 *		group_id	int
	 *		group_name	string
	 * @echo	json 1成功，0失败
	 */
	public function modify_group($parames = array())
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
				//获取组列表
				$this -> load -> model('m_group', 'mgroup');
				$result = $this->mgroup->modify_group($parames);

				//echo
				$this->_ajax_echo(array('result'=>$result));
			}
			else	//没有权限
			{
				//echo
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

	/**
	 * 删除一个group
	 * @parame $parames array
	 *		group_id	int
	 * @echo	json 1成功，0失败
	 */
	public function delete_group($parames = array())
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
				$this -> load -> model('m_group', 'mgroup');
				$groupid = isset($parames['group_id'])?$parames['group_id']:null;
				$result = $this->mgroup->delete_group($groupid);

				//echo
				$this->_ajax_echo(array('result'=>$result));
			}
			else	//没有权限
			{
				//echo
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

	/**
	 * 添加一个group
	 * @parame $parames array
	 *		group_name	string
	 * @echo	json >0成功，0失败
	 */
	public function add_group($parames = array())
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
				$this -> load -> model('m_group', 'mgroup');
				$group_name = isset($parames['group_name'])?$parames['group_name']:null;
				$result = $this->mgroup->create_group($group_name);

				//echo
				$this->_ajax_echo(array('result'=>$result));
			}
			else	//没有权限
			{
				//echo
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

}

/* End of file c_group.php */
/* Location: ./application/controllers/c_group.php */