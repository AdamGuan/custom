<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_index extends MY_Controller {

	public function __construct() {
		parent :: __construct();
	}
	
	//后台首页
	public function index()
	{
		//检查是否有登录
		$data = $this->_check_login();
		if(is_array($data) && isset($data['redirect_url']))	//未登录
		{
			top_redirect($data['redirect_url']);
		}
		else	//已登录
		{
			$config = $this -> my_config['left_tab']; 
			// 左侧数据的数组
			$web_left_menu = array();
			foreach($config as $item) {
				$tab_title = $item['title'];
				$tab_list = $item['list'];
				$tmp = array();
				foreach($tab_list as $it) {
					if(check_privilege($it['url']))
					{
						$tmp[] = array(
							'id' => $it['id'],
							'text' => $it['text'],
							'iconCls' => $it['iconCls'],
							'attributes' => array("url" => base_url($it['url']))
						);
					}
				}
				if(count($tmp) > 0)
				{
					$web_left_menu[] = array('title' => $tab_title, 'list' => json_encode($tmp));
				}
			}
			
			$data = array(
				'login_out_url'=>get_login_out_url(),
				'web_title'=>$this -> my_config['web_title'],
				'web_left_menu' => $web_left_menu, 
				'default_page' => base_url($this -> my_config['default_page'])
			);

			$this->_output_view("v_index", $data);
		}
	}

	public function sub1()
	{
		//检查是否有登录
		$data = $this->_check_login();
		if(is_array($data) && isset($data['redirect_url']))
		{
			top_redirect($data['redirect_url']);
		}
		else
		{
			//get page title
			$page_title = $this->_get_current_page_title(__FILE__,__CLASS__,__METHOD__);

			$data = array('page_title'=>$page_title);
			$this->_output_view("v_sub1", $data);
		}
	}

	public function sub2()
	{
		//检查是否有登录
		$data = $this->_check_login();
		if(is_array($data) && isset($data['redirect_url']))
		{
			top_redirect($data['redirect_url']);
		}
		else
		{
			//get page title
			$page_title = $this->_get_current_page_title(__FILE__,__CLASS__,__METHOD__);

			$data = array('page_title'=>$page_title);
			$this->_output_view("v_sub2", $data);
		}
	}

	public function default_page()
	{
		//检查是否有登录
		$data = $this->_check_login();
		if(is_array($data) && isset($data['redirect_url']))
		{
			top_redirect($data['redirect_url']);
		}
		else
		{
			$data = array('page_title'=>"首页");
			$this->_output_view("v_default_page", $data);
		}
	}

}

/* End of file c_index.php */
/* Location: ./application/controllers/c_index.php */