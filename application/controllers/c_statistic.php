<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_Statistic extends MY_Controller {

	public function __construct() {
		parent :: __construct();
	}
	
	//展示客服提问人数
	public function show_student_ask($parames = array())
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
				$this -> load -> model('m_group', 'mgroup');
				$group_list = $this->mgroup->get_group_list();
				foreach($group_list as $key=>$item)
				{
					$group_list[$key]['id'] = $item['F_id'];
					$group_list[$key]['text'] = $item['F_groupname'].' - 客服';
					unset($group_list[$key]['F_id']);
					unset($group_list[$key]['F_groupname']);
				}

				$this -> load -> model('m_statistic', 'mstatistic');
				$groupid = isset($group_list[0],$group_list[0]['id'])?$group_list[0]['id']:0;
				$list = $this->mstatistic->get_ask_persons_every_week($groupid,0);
				$labels = array();
				$tooltips = array();
				$values = array();
				if(is_array($list) && count($list) > 0)
				{
					
					foreach($list as $item)
					{
						$labels[] = $item['week'];
						$values[] = $item['total'];
						$tooltips[] = $item['date'].'<br />'.$item['total'].' 人';
					}
				}
				//
				
				$type_list = array(
					array('id'=>1,'text'=>'周 - 咨询人数'),
					array('id'=>2,'text'=>'月 - 咨询人数'),
				);
				$group = ($groupid > 0)?$group_list[0]['id']:0;
				$type = $type_list[0]['id'];
				
				
				//data
				$data = array(
					'page_title'=>$page_title,
					'labels'=>json_encode($labels),
					'values'=>json_encode($values),
					'tooltips'=>json_encode($tooltips),
					'group_list_json'=>json_encode($group_list),
					'type_list_json'=>json_encode($type_list),
					'group'=>$group,
					'type'=>$type,
					'total'=>count($labels),
					'student_ask_data_url'=>get_student_ask_data_url(),
				);

				$this->_output_view("v_student_ask", $data);
			}
			else	//没有权限
			{
				$this->_ajax_echo(array('非法访问'));
			}
		}
	}
	
	//获取客服提问人数
	public function get_student_ask_data($parames = array())
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
				$labels = array();
				$tooltips = array();
				$values = array();

				$this -> load -> model('m_statistic', 'mstatistic');
				$groupid = isset($parames['groupid'])?$parames['groupid']:0;
				$typeid = isset($parames['typeid'])?$parames['typeid']:1;
				$offset = isset($parames['offset'])?$parames['offset']:0;
				if($typeid == 1)
				{
					$list = $this->mstatistic->get_ask_persons_every_week($groupid,$offset);
					if(is_array($list) && count($list) > 0)
					{
						foreach($list as $item)
						{
							$labels[] = $item['week'];
							$values[] = $item['total'];
							$tooltips[] = $item['date'].'<br />'.$item['total'].' 人';
						}
					}
				}
				else
				{
					$list = $this->mstatistic->get_ask_persons_every_month($groupid,$offset);
					if(is_array($list) && count($list) > 0)
					{
						foreach($list as $item)
						{
							$labels[] = $item['day'];
							$values[] = $item['total'];
							$tooltips[] = $item['date'].'<br />'.$item['total'].' 人';
						}
					}
				}

				//data
				$data = array(
					'labels'=>$labels,
					'values'=>$values,
					'tooltips'=>$tooltips,
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

/* End of file c_statistic.php */
/* Location: ./application/controllers/c_statistic.php */