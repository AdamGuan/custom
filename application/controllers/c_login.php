<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_Login extends MY_Controller {

	public function __construct() {
		parent :: __construct();
	}
	
	/**
	 * 显示login界面
	 *
	 */
	public function index()
	{
		//检查是否有登录
		$data = $this->_check_login();
		if(is_array($data) && isset($data['redirect_url']))	//未登录
		{
			//获取验证码
			$this -> load -> model('M_captcha', 'mcaptcha');
			$cap = $this->mcaptcha->create_captcha();

			$data = array(
				'web_title'=>$this -> my_config['web_title'],
				'login_valid_url'=>get_login_valid_url(),
				'get_cap_url'=>get_cap_url(),
				'cap'=>$cap,
			);
			$this->_output_view("v_login", $data);
		}
		else	//已登录
		{
			//跳转到首页
			top_redirect(get_index_url());
		}
	}
	
	/**
	 * login验证
	 *
	 */
	public function login_valid($parames = array())
	{
		$data = array();
		//验证cap是否正确
		$this -> load -> model('M_captcha', 'mcaptcha');
		$result = $this->mcaptcha->check_captcha_exists($parames['cap']);
		if($result == 1)
		{
			$result = 0;
			//验证登录
			$this -> load -> model('M_login', 'mlogin');
			$user_info = $this->mlogin->check_user_login($parames['name'],$parames['pwd']);
			if(is_array($user_info) && isset($user_info['F_user_id']))
			{
				$result = 1;
				//设置session
				$privilege = isset($user_info['F_role_privilege'])?$user_info['F_role_privilege']:'0';
				$session_array = array(
					'F_user_login_name'=>$user_info['F_user_login_name'],
					'F_user_id'=>$user_info['F_user_id'],
					'F_group_id'=>$user_info['F_group_id'],
					'F_role_privilege'=>$privilege
				);
				$this->session->set_userdata($session_array);
			}
			else
			{
				//用户名或密码错误
				$data['error'] = -3;
			}
		}
		else
		{
			//验证码错误
			$data['error'] = -2;
		}
		
		if($result == 0)
		{
			//获取验证码
			$this -> load -> model('M_captcha', 'mcaptcha');
			$cap = $this->mcaptcha->create_captcha();
			$data['cap'] = $cap;
		}

		//返回json数据
		if(!isset($data['error']))
		{
			$data['redirect_url'] = get_index_url();
		}
		$this->_ajax_echo($data);
	}
	
	/**
	 * 获取验证码
	 *
	 */
	public function get_cap()
	{
		//获取验证码
		$this -> load -> model('M_captcha', 'mcaptcha');
		$cap = $this->mcaptcha->create_captcha();

		//返回json数据
		$data = array(
			'cap'=>$cap,
		);
		$this->_ajax_echo($data);exit;
	}

	/**
	 * login out
	 *
	 */
	public function login_out()
	{
		//销毁session
		$this -> load -> model('M_login', 'mlogin');
		$cap = $this->mlogin->login_out();
		
		//跳转到首页
		top_redirect(get_index_url());
	}

}

/* End of file c_login.php */
/* Location: ./application/controllers/c_login.php */