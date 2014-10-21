<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//web title
$config['web_title'] = '微客服管理后台';

//left tab
$config['left_tab']	= array(
	array(
		'title'=>'客服管理',
		'list' => array(
			array('url'=>'c_custom/show_custom','text'=>'客服账号','id'=>1,'iconCls'=>'icon-blank'),
			array('url'=>'c_score/show_custom_score','text'=>'用户评价','id'=>2,'iconCls'=>'icon-blank')
		)
	),
	array(
		'title'=>'数据统计',
		'list' => array(
			array('url'=>'c_statistic/show_student_ask','text'=>'咨询趋势','id'=>3,'iconCls'=>'icon-blank'),
		)
	),
	array(
		'title'=>'其它',
		'list' => array(
			array('url'=>'c_group/show_group','text'=>'地区管理','id'=>4,'iconCls'=>'icon-blank'),
			array('url'=>'c_role/show_role','text'=>'角色管理','id'=>5,'iconCls'=>'icon-blank'),
			array('url'=>'c_manager/show_manager','text'=>'管理员管理','id'=>6,'iconCls'=>'icon-blank'),
		)
	),
);

//default page
$config['default_page'] = 'c_index/default_page';

//captcha
$config['captcha_img_path'] = 'public/image/captcha/';
$config['captcha_img_url_suffix'] = 'public/image/captcha/';
$config['captcha_expire_timestamp'] = 7200;
$config['captcha_img_width'] = 150;
$config['captcha_img_height'] = 30;

//chat api
$config['im_api_url'] = "https://a1.easemob.com/";
$config['org_name'] = "adam-api";
$config['app_name'] = "test";
$config['client_id'] = "YXA6nkcLcEI6EeSTVrselB9xPA";
$config['client_secret'] = "YXA6_Yzu1PniIb_swq86vmrUOMC9Skk";

//客服密码
$config['custom_pwd'] = '123456';

//权限
$config['permission'] = array(
	array('desc'=>'客服账号','value'=>'c_custom/show_custom'),
	array('desc'=>'客服列表','value'=>'c_custom/get_custom_list'),
	array('desc'=>'添加客服','value'=>'c_custom/add_custom'),
	array('desc'=>'显示添加客服','value'=>'c_custom/show_add_custom'),
	array('desc'=>'显示客服聊天记录','value'=>'c_custom/show_custom_chat_list'),
	array('desc'=>'重置客服密码','value'=>'c_custom/reset_custom_pwd'),
	array('desc'=>'删除客服','value'=>'c_custom/delete_customs'),
	array('desc'=>'显示用户发送给客服信息','value'=>'c_custom/show_student_to_custom_chat_list'),
	array('desc'=>'用户评价','value'=>'c_score/show_custom_score'),
	array('desc'=>'用户评价列表','value'=>'c_score/get_custom_score_list'),
	array('desc'=>'用户评价删除','value'=>'c_score/delete_custom_score'),
	array('desc'=>'咨询趋势','value'=>'c_statistic/show_student_ask'),
	array('desc'=>'获取咨询趋势数据','value'=>'c_statistic/get_student_ask_data'),
);

//api
$config['sign_open'] = 1;

//-1未登录，-2验证码错误，-3用户名或密码错误

/* End of file my_config.php */
/* Location: ./application/config/my_config.php */
