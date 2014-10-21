<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_Custom extends MY_Controller {

	public function __construct() {
		parent :: __construct();
	}
	
	//展示客服页
	public function show_custom($parames = array())
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
					'show_add_custom_url'=>get_show_add_custom_url(),
					'get_custom_list_url'=>get_custom_list_data_url(),
					'show_custom_chat_list_url'=>get_show_custom_chat_list_url(),
					'reset_customs_pwd_url'=>get_reset_customs_pwd_url(),
					'delete_customs_url'=>get_delete_customs_url(),
				);

				$this->_output_view("v_custom", $data);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

	//
	/**
	 * 获取客服列表数据
	 * @echo json	二维
	 *		F_custom_id			string		客服在环信中的ID
	 *		F_custom_name		string		客服名
	 *		F_custom_createtime	datetime	客服创建时间
	 *		F_groupname			string		组名
	 *		F_custom_status		string		在线or不在线
	 *		F_custom_receive	int			
	 *		F_custom_replay		int
	 */
	public function get_custom_list($parames = array())
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
				
				$this -> load -> model('m_custom', 'mcustom');
				$custom_list = $this->mcustom->get_custom_list();
				if(is_array($custom_list) && count($custom_list) > 0)
				{
					$F_custom_name_list = array();
					foreach($custom_list as $key=>$item)
					{
						$F_custom_name_list[] = $item['F_custom_name'];
					}
					$custom_msg_info  = $this->mcustom->get_customs_info_from_im($F_custom_name_list);
					foreach($custom_list as $key=>$item)
					{
						$custom_list[$key]['F_custom_status'] = -1;
						$custom_list[$key]['F_custom_receive'] = -1;
						$custom_list[$key]['F_custom_replay'] = -1;
						if(isset($custom_msg_info[$key]))
						{
							$custom_list[$key]['F_custom_status'] = $custom_msg_info[$key]['F_custom_status'];
							$custom_list[$key]['F_custom_receive'] = $custom_msg_info[$key]['F_custom_receive'];
							$custom_list[$key]['F_custom_replay'] = $custom_msg_info[$key]['F_custom_replay'];
						}
						$custom_list[$key]['F_look'] = '';
						$custom_list[$key]['F_edit'] = '';
					}
				}
				$this->_ajax_echo($custom_list);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

	/**
	 * 添加客服
	 * @parame	$parames	array
	 *		name	string	客服用户名
	 *		pwd		string	密码，明文
	 *		groupid	int		组ID
	 */
	public function add_custom($parames = array())
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
				$this -> load -> model('m_custom', 'mcustom');
				$result = $this->mcustom->create_custom($parames);
				if($result == 0)	//失败
				{
					$this->_ajax_echo(array('result'=>0));
				}
				else	//成功
				{
					$this->_ajax_echo(array('result'=>1));
				}
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}
	
	//展示添加客服页
	public function show_add_custom($parames = array())
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
				if(count($group_list) == 1)
				{
					$group_list[0]['selected'] = true;
				}
				
				//data
				$data = array(
					'group_list_json'=>json_encode($group_list),
					'group_list_total'=>count($group_list),
					'custom_pwd'=>$this->my_config['custom_pwd'],
					'add_custom_url'=>get_add_custom_url(),
				);
				
				//output
				$this->_output_view("v_add_custom", $data,true);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}


	//显示某个客服的聊天记录
	public function show_custom_chat_list($parames = array())
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
				//data
				$data = array(
					'custom_name'=>$parames['custom_name'],
					'user_chat_list_url'=>get_user_chat_list_url(),
					'custom_chat_list_url'=>get_custom_chat_list_url(),
				);
				//output
				$this->_output_view("v_custom_chat_list", $data,true);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

	//获取某个客服的聊天记录
	public function get_custom_chat_list($parames = array())
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
				$this -> load -> model('m_custom', 'mcustom');
				$list = $this->mcustom->get_custom_receive_chat($parames);
				$total = $this->mcustom->get_custom_receive_chat_total($parames);
				//data
				$data = array('rows'=>$list,'total'=>$total);
				//output
				$this->_ajax_echo($data);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
		
	}

	/**
	 * 重置客服密码
	 * @parame	$parames	array
	 *		user_names	string	客服用户名[optional]
	 *		all			int		1:重置所有客服[optional]
	 */
	public function reset_custom_pwd($parames = array())
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
				$this -> load -> model('m_custom', 'mcustom');
				if(isset($parames['all']) && $parames['all'] == 1)
				{
					$user_name_list = array();
					$all_custom_list = $this->mcustom->get_custom_list();
					foreach($all_custom_list as $item)
					{
						$user_name_list[] = $item['F_custom_name'];
					}
				}
				else
				{
					$user_names = $parames['user_names'];
					$user_name_list = explode(",",$user_names);
				}

				$result = $this->mcustom->reset_customs_passwd($user_name_list);
				if($result == 0)	//失败
				{
					$this->_ajax_echo(array('result'=>0));
				}
				else	//成功
				{
					$this->_ajax_echo(array('result'=>1));
				}
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

	/**
	 * 删除客服
	 * @parame	$parames	array
	 *		user_names	string	客服用户名
	 */
	public function delete_customs($parames = array())
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
				$this -> load -> model('m_custom', 'mcustom');
				$user_names = $parames['user_names'];
				$user_name_list = explode(",",$user_names);
				foreach($user_name_list as $user_name)
				{
					$this->mcustom->delete_custom($user_name);
				}
				//成功
				$this->_ajax_echo(array('result'=>1));
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

	//显示某个用户发送给某个客服聊天记录
	public function show_student_to_custom_chat_list($parames = array())
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
				$this -> load -> model('m_custom', 'mcustom');
				$chat_list = $this->mcustom->get_student_sendto_custome_chat($parames['user_name'],$parames['custom_name']);
				//data
				$data = array(
					'custom_name'=>$parames['custom_name'],
					'user_name'=>$parames['user_name'],
					'userid'=>$parames['userid'],
					'student_to_custom_chat_list_url'=>get_student_to_custom_chat_list_url()."?user_name=".$parames['userid']."&custom_name=".$parames['custom_name'],
				);
				//output
				$this->_output_view("v_student_chat_list", $data,true);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

	//获取某个用户发送给某个客服聊天记录
	public function get_student_to_custom_chat_list($parames = array())
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
				$this -> load -> model('m_custom', 'mcustom');
				$list = $this->mcustom->get_student_sendto_custome_chat($parames);
				$total = $this->mcustom->get_student_sendto_custome_chat_total($parames);

				//data
				$data = array('rows'=>$list,'total'=>$total);
				//output
				$this->_ajax_echo($data);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

}

/* End of file c_custom.php */
/* Location: ./application/controllers/c_custom.php */
