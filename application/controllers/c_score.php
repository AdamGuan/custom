<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_Score extends MY_Controller {

	public function __construct() {
		parent :: __construct();
	}
	
	//展示客服评价
	public function show_custom_score($parames = array())
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
					'score_list_url'=>get_score_list_url(),
					'score_delete_url'=>get_score_delete_url(),
				);

				$this->_output_view("v_custom_score", $data);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

	/**
	 * 获取评价列表数据
	 * @echo json	二维
	 *		F_id			int
	 *		F_from			string
	 *		F_to			string
	 *		F_score			string
	 *		F_content		string
	 *		F_createtime	datetime
	 */
	public function get_custom_score_list($parames = array())
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
				
				$this -> load -> model('m_score', 'mscore');
				$list = $this->mscore->get_custom_score_list($parames);
				$total = $this->mscore->get_custom_score_total();
				$data = array('rows'=>$list,'total'=>$total);
				$this->_ajax_echo($data);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

	/**
	 * 删除评价
	 */
	public function delete_custom_score($parames = array())
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
				
				$this -> load -> model('m_score', 'mscore');
				$id = isset($parames['id'])?$parames['id']:null;
				$result = $this->mscore->del_custom_score($id);
				$data = array('result'=>$result);
				$this->_ajax_echo($data);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}

}

/* End of file c_score.php */
/* Location: ./application/controllers/c_score.php */