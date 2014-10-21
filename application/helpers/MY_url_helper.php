<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 利用js在top window中跳转.
 * 
 * @parame $uri	string
 */
function top_redirect($uri = '') {
	echo("<script> top.location.href='" . $uri . "'</script>");
	exit;
} 

/**
 * 获取public下面的资源url.
 * 
 * @parame $file	string	public下面的完全路径
 * @return string 资源的url
 */
function get_public_url($file) {
	return base_url('public/' . $file);
} 

/**
 * 获取public/js下面的资源url.
 * 
 * @parame $file	string	public/js下面的完全路径
 * @return string js资源的url
 */
function get_js_url($file) {
	return base_url('public/js/' . $file);
} 

/**
 * 获取public/css下面的资源url.
 * 
 * @parame $file	string	public/css下面的完全路径
 * @return string css资源的url
 */
function get_css_url($file) {
	return base_url('public/css/' . $file);
} 

/**
 * 获取public/image下面的资源url.
 * 
 * @parame $file	string	public/image下面的完全路径
 * @return string image资源的url
 */
function get_image_url($file) {
	return base_url('public/image/' . $file);
} 

/**
 * 获取login_valid url.
 * 
 */
function get_login_valid_url() {
	return base_url('c_login/login_valid');
} 

/**
 * 获取login url.
 * 
 */
function get_login_url() {
	return base_url();
} 

/**
 * 获取login out url.
 * 
 */
function get_login_out_url() {
	return base_url('c_login/login_out');
} 

/**
 * 获取index url.
 * 
 */
function get_index_url() {
	return base_url('c_index/index');
} 

/**
 * 获取cap url.
 * 
 */
function get_cap_url() {
	return base_url('c_login/get_cap');
} 

/**
 * 获取show add custom url.
 * 
 */
function get_show_add_custom_url() {
	return base_url('c_custom/show_add_custom');
} 

/**
 * 获取add custom url.
 * 
 */
function get_add_custom_url() {
	return base_url('c_custom/add_custom');
} 

/**
 * 获取get custom list data url.
 * 
 */
function get_custom_list_data_url() {
	return base_url('c_custom/get_custom_list');
} 


function get_show_custom_chat_list_url() {
	return base_url('c_custom/show_custom_chat_list');
} 

function get_reset_customs_pwd_url() {
	return base_url('c_custom/reset_custom_pwd');
} 

function get_delete_customs_url() {
	return base_url('c_custom/delete_customs');
} 

function get_group_list_url() {
	return base_url('c_group/get_group_list');
} 

function modify_group_url() {
	return base_url('c_group/modify_group');
} 

function get_delete_group_url() {
	return base_url('c_group/delete_group');
} 

function get_add_group_url() {
	return base_url('c_group/add_group');
} 

function get_user_chat_list_url() {
	return base_url('c_custom/show_student_to_custom_chat_list');
} 

function get_custom_chat_list_url() {
	return base_url('c_custom/get_custom_chat_list');
} 

function get_student_to_custom_chat_list_url() {
	return base_url('c_custom/get_student_to_custom_chat_list');
} 

function get_score_list_url() {
	return base_url('c_score/get_custom_score_list');
} 

function get_score_delete_url() {
	return base_url('c_score/delete_custom_score');
} 

function get_student_ask_data_url() {
	return base_url('c_statistic/get_student_ask_data');
} 

function get_role_detail_url() {
	return base_url('c_role/get_role_detail');
} 

function get_update_role_url() {
	return base_url('c_role/update_role');
} 

function get_add_role_url(){
	return base_url('c_role/add_role');
}

function get_delete_role_url(){
	return base_url('c_role/delete_role');
}

function get_manager_add_url(){
	return base_url('c_manager/manager_add');
}

function get_manager_delete_url(){
	return base_url('c_manager/manager_delete');
}

function get_manager_modify_url(){
	return base_url('c_manager/manager_modify');
}

/**
 * End of file MY_url_helper.php
 */
/**
 * Location: ./system/helpers/MY_url_helper.php
 */