<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_Api extends MY_Controller {

	public function __construct() {
		parent :: __construct();
	}

	public function msg($parame = array())
	{
		//检查合法
		$this -> load -> model('m_api', 'mapi');
		$type = isset($parame['who_to_who'])?$parame['who_to_who']:-1;
		$name = isset($parame['name'])?$parame['name']:'';
		$key = $this->mapi->get_key($name,$type);
		$sign_check_result = sign_check($key,$parame,$this -> my_config['sign_open']);

		//调用model写入msg
		if(isset($sign_check_result) && $sign_check_result == 1)
		{
			$result = $this->mapi->write_msg($parame);
		}
		//data
		$data = array('responseNo'=>-1);
		if(isset($sign_check_result) && $sign_check_result == 1 && isset($result) && $result > 0)
		{
			$data = array('responseNo'=>0);
		}
		//output
		$this->_ajax_echo($data);
	}

	public function grade($parame = array())
	{
		//检查合法
		$this -> load -> model('m_api', 'mapi');
		$type = 0;
		$name = isset($parame['name'])?$parame['name']:'';
		$key = $this->mapi->get_key($name,$type);
		$sign_check_result = sign_check($key,$parame,$this -> my_config['sign_open']);

		//调用model写入评价
		if(isset($sign_check_result) && $sign_check_result == 1)
		{
			$result = $this->mapi->write_grade($parame);
		}
		//data
		$data = array('responseNo'=>-1);
		if(isset($sign_check_result) && $sign_check_result == 1 && isset($result) && $result > 0)
		{
			$data = array('responseNo'=>0);
		}
		//output
		$this->_ajax_echo($data);
	}

	public function find($parame = array())
	{
		//检查合法
		$this -> load -> model('m_api', 'mapi');
		$type = 0;
		$name = isset($parame['name'])?$parame['name']:'';
		$key = $this->mapi->get_key($name,$type);
		$sign_check_result = sign_check($key,$parame,$this -> my_config['sign_open']);

		if(isset($sign_check_result) && $sign_check_result == 1)
		{
			$custom = $this->mapi->find_custom($parame);
		}
		//data
		$data = array('responseNo'=>-1);
		if(isset($sign_check_result) && $sign_check_result == 1 && is_array($custom) && count($custom) > 0)
		{
			$data = array(
				'responseNo'=>0,
				'custom_name'=>$custom['F_custom_name'],
				'custom_nickname'=>$custom['F_custom_nickname'],
			);
		}
		//output
		$this->_ajax_echo($data);
	}
	
	/**
	 * 获取用户信息
	 * @parame	array
	 *		username	string
	 * @echo
	 */
	public function user_info($parame = array())
	{
		//检查合法
		//调用model
		$this -> load -> model('m_api', 'mapi');
		
		$username = isset($parame['username'])?$parame['username']:'';
		$result = $this->mapi->register_student($parame);

		//data
		$data = array('responseNo'=>-1);
		if(isset($result['userpwd']) && !is_null($result['userpwd']) && strlen($result['userpwd']) > 0)
		{
			$data = array(
				'responseNo'=>0,
				'username'=>$result['username'],
				'userpwd'=>$result['userpwd'],
				'key'=>$result['key'],
			);
		}
		//output
		$this->_ajax_echo($data);
	}

	/**
	 * 获取地区列表
	 * @echo
	 */
	public function local_list($parame = array())
	{
		//检查合法
		$this -> load -> model('m_api', 'mapi');
		$type = 0;
		$name = isset($parame['name'])?$parame['name']:'';
		$key = $this->mapi->get_key($name,$type);
		$sign_check_result = sign_check($key,$parame,$this -> my_config['sign_open']);

		//调用model
		if(isset($sign_check_result) && $sign_check_result == 1)
		{
			$local_list = $this->mapi->get_local_list();
		}

		//data
		$data = array('responseNo'=>-1);
		if(isset($sign_check_result) && $sign_check_result == 1 && is_array($local_list) && count($local_list) > 0)
		{
			$data = array(
				'responseNo'=>0,
				'local_list'=>$local_list
			);
		}
		//output
		$this->_ajax_echo($data);
	}

	/**
	 * 修改客服昵称
	 * @parame	$parames	array
	 *				custom_name		客服账户名
	 *				custom_nickname	客服昵称
	 * @echo
	 */
	public function modify_custom_nickname($parame = array())
	{
		//检查合法
		$this -> load -> model('m_api', 'mapi');
		$type = 1;
		$name = isset($parame['name'])?$parame['name']:'';
		$key = $this->mapi->get_key($name,$type);
		$sign_check_result = sign_check($key,$parame,$this -> my_config['sign_open']);

		//调用model
		if(isset($sign_check_result) && $sign_check_result == 1)
		{
			$result = $this->mapi->modify_custom_nickname($parame);
		}

		//data
		$data = array('responseNo'=>-1);
		if(isset($sign_check_result) && $sign_check_result == 1 && isset($result) && $result == 1)
		{
			$data = array(
				'responseNo'=>0,
			);
		}
		//output
		$this->_ajax_echo($data);
	}

	/**
	 * 修改客服密码
	 * @parame	$parames	array
	 *				custom_name			客服账户名
	 *				custom_pwd_current	客服当前密码
	 *				custom_pwd_new		客服新密码
	 * @echo
	 */
	public function modify_custom_pwd($parame = array())
	{
		//检查合法
		$this -> load -> model('m_api', 'mapi');
		$type = 1;
		$name = isset($parame['name'])?$parame['name']:"";
		$key = $this->mapi->get_key($name,$type);
		$sign_check_result = sign_check($key,$parame,$this -> my_config['sign_open']);

		//调用model
		if(isset($sign_check_result) && $sign_check_result == 1)
		{
			$result = $this->mapi->modify_custom_pwd($parame);
		}

		//data
		$data = array('responseNo'=>-1);
		if(isset($sign_check_result) && $sign_check_result ==1 && isset($result) && $result == 1)
		{
			$data = array(
				'responseNo'=>0,
			);
		}
		//output
		$this->_ajax_echo($data);
	}

	/**
	 * 获取客服信息
	 * @parame	$parames	array
	 *				custom_name		客服账户名
	 *				custom_pwd		客服密码
	 * @echo
	 */
	public function custom_info($parame = array())
	{
		//检查合法
		//调用model
		$this -> load -> model('m_api', 'mapi');
		$info = $this->mapi->get_custom_info($parame);

		//data
		$data = array('responseNo'=>-1);
		if(isset($info) && count($info) > 0)
		{
			$data = array(
				'responseNo'=>0,
				'custom_name'=>$info['F_custom_name'],
				'custom_nickname'=>$info['F_custom_nickname'],
				'local'=>$info['F_groupname'],
				'key'=>$info['key'],
			);
		}
		//output
		$this->_ajax_echo($data);
	}
	
	/**
	 * 添加客服对用户关系
	 * @parame	$parames	array
	 *				custom_name		客服账户名
	 *				user_name		客服当前密码
	 * @echo
	 */
	public function add_custom_relation($parame = array())
	{
		//检查合法
		
		$this -> load -> model('m_api', 'mapi');
		$type = 1;
		$name = isset($parame['name'])?$parame['name']:"";
		$key = $this->mapi->get_key($name,$type);
		$sign_check_result = sign_check($key,$parame,$this -> my_config['sign_open']);

		//调用model
		if(isset($sign_check_result) && $sign_check_result == 1)
		{
			$result = $this->mapi->add_custom_relation($parame);
		}

		//data
		$data = array('responseNo'=>-1);
		if(isset($sign_check_result) && $sign_check_result ==1 && isset($result) && $result == 1)
		{
			$data = array(
				'responseNo'=>0,
			);
		}
		//output
		$this->_ajax_echo($data);
		
	}
	
	/**
	 * 删除客服对用户关系
	 * @parame	$parames	array
	 *				custom_name		客服账户名
	 *				user_name		客服当前密码
	 * @echo
	 */
	public function delete_custom_relation($parame = array())
	{
		//检查合法
		
		$this -> load -> model('m_api', 'mapi');
		$type = 1;
		$name = isset($parame['name'])?$parame['name']:"";
		$key = $this->mapi->get_key($name,$type);
		$sign_check_result = sign_check($key,$parame,$this -> my_config['sign_open']);

		//调用model
		if(isset($sign_check_result) && $sign_check_result == 1)
		{
			$result = $this->mapi->delete_custom_relation($parame);
		}

		//data
		$data = array('responseNo'=>-1);
		if(isset($sign_check_result) && $sign_check_result ==1 && isset($result) && $result == 1)
		{
			$data = array(
				'responseNo'=>0,
			);
		}
		//output
		$this->_ajax_echo($data);
		
	}
	
	/**
	 * 查询某个客服对用户关系
	 * @parame	$parames	array
	 *				custom_name		客服账户名
	 * @echo
	 */
	public function get_custom_relation($parame = array())
	{
		//检查合法
		
		$this -> load -> model('m_api', 'mapi');
		$type = 1;
		$name = isset($parame['name'])?$parame['name']:"";
		$key = $this->mapi->get_key($name,$type);
		$sign_check_result = sign_check($key,$parame,$this -> my_config['sign_open']);

		//调用model
		if(isset($sign_check_result) && $sign_check_result == 1)
		{
			$list = $this->mapi->get_custom_relation($parame);
		}

		//data
		$data = array('responseNo'=>-1);
		if(isset($sign_check_result) && $sign_check_result ==1 && isset($list) && is_array($list))
		{
			$data = array(
				'responseNo'=>0,
				'list'=>$list
			);
		}
		//output
		$this->_ajax_echo($data);
		
	}
	
	/*
	public function test($parame = array())
	{
		
		//检查合法
		$this -> load -> model('m_api', 'mapi');
		$type = 0;
		$name = $parame['name'];
		$key = $this->mapi->get_key($name,$type);
		$sign_check_result = sign_check($key,$parame,$this -> my_config['sign_open']);

		//data
		$data = array('responseNo'=>-1);
		if($sign_check_result == 1)
		{
			$data = array('responseNo'=>0);
		}
		
		//output
		$this->_ajax_echo($data);

	}
	*/
}

/* End of file c_custom.php */
/* Location: ./application/controllers/c_custom.php */
