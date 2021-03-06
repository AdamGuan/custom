<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

function unescape($str) {
	$ret = '';
	$len = strlen($str);
	for ($i = 0; $i < $len; $i ++) {
		if ($str[$i] == '%' && $str[$i + 1] == 'u') {
			$val = hexdec(substr($str, $i + 2, 4));
			if ($val < 0x7f)
				$ret .= chr($val);
			else
			if ($val < 0x800)
				$ret .= chr(0xc0 | ($val >> 6)) .
				chr(0x80 | ($val &0x3f));
			else
				$ret .= chr(0xe0 | ($val >> 12)) .
				chr(0x80 | (($val >> 6) &0x3f)) .
				chr(0x80 | ($val &0x3f));
			$i += 5;
		} else
		if ($str[$i] == '%') {
			$ret .= urldecode(substr($str, $i, 3));
			$i += 2;
		} else
			$ret .= $str[$i];
	} 
	return $ret;
} 

/**
 * js escape php 实现
 * 
 * @param  $string the sting want to be escaped
 * @param  $in_encoding 
 * @param  $out_encoding 
 */
function escape($string, $in_encoding = 'UTF-8', $out_encoding = 'UCS-2') {
	$return = '';
	if (function_exists('mb_get_info')) {
		for($x = 0; $x < mb_strlen ($string, $in_encoding); $x ++) {
			$str = mb_substr ($string, $x, 1, $in_encoding);
			if (strlen ($str) > 1) { // 多字节字符
				$return .= '%u' . strtoupper (bin2hex (mb_convert_encoding ($str, $out_encoding, $in_encoding)));
			} else {
				$return .= '%' . strtoupper (bin2hex ($str));
			} 
		} 
	} 
	return $return;
} 

/**
 * 读取整个文件里面的内容，并返回.
 * 
 * @parame $file	string	包含路径的文件名.
 * @return string or false	如果文件不存在，返回false.
 */
function get_file_content($file) {
	if (file_exists($file)) {
		return file_get_contents($file);
	} else {
		return false;
	} 
} 

function check_privilege($privilege)
{
	$result = 0;
	$CI =& get_instance();

	$F_role_privilege = $CI->session->userdata('F_role_privilege');
	if($F_role_privilege != 'all')
	{
		$privilege_list = explode(",",$F_role_privilege);
	}
	else
	{
		$privilege_list = $F_role_privilege;
	}
	if($privilege_list == 'all' || in_array($privilege,$privilege_list))
	{
		$result = 1;
	}
	return $result;
}

/**
 * 分库，分表算法
 */
function get_hash_db($id)
{
	//分库，分表算法
	$md5_str = md5($id,true);
	$index0 = ord(substr($md5_str,1,1));
	$index = ord(substr($md5_str,0,1))%100;
	$list[] = chr(($index0%10)+ord('0'));     //库
	$list[] = chr(($index/10)+ord('0'));     //表
	$list[] = chr(($index%10)+ord('0'));     //表
	return $list;
}

function generate_pwd($length)
{
	$pwd = '';
	if(isset($length))
	{
		for($i=0;$i<$length;$i++)
		{
			$tmp = rand(0,2);
			switch($tmp)
			{
				case 0:
					$tmp2 = rand(ord('0'),ord('9'));
					$pwd .= chr($tmp2);
					break;
				case 1:
					$tmp2 = rand(ord('A'),ord('Z'));
					$pwd .= chr($tmp2);
					break;
				case 2:
					$tmp2 = rand(ord('a'),ord('z'));
					$pwd .= chr($tmp2);
					break;
			}
		}
	}
	return $pwd;
}

/**
 * 获取到一周的日期列表(Y-m-d)
 */
function get_weeks_date($week_offset = 0)
{
	$date_list = array();
	if($week_offset <= 0)
	{
		$now_timestamp = time();
		$now_week = date('N',$now_timestamp);
		if($week_offset < 0)
		{
			$week_offset += 1;
			
			$now_timestamp = $now_timestamp - ($now_week-$week_offset*7)*3600*24;
		}
		$now_week = date('N',$now_timestamp);
		$now_date = date('Y-m-d',$now_timestamp);
		$base_timestamp = strtotime($now_date);
		for($i=$now_week;$i>=1;--$i)
		{
			$timestamp = $base_timestamp - ($now_week - $i)*3600*24;
			$date_list[] = date('Y-m-d',$timestamp);
		}
		sort($date_list);
	}
	return $date_list;
}

/**
 * 获取到一个月中的每日(Y-m-d)
 */
function get_months_date($month_offset = 0)
{
	$date_list = array();
	if($month_offset <= 0)
	{
		$now_timestamp = time();
		$now_day = date('j',$now_timestamp);
		if($month_offset < 0)
		{
			$month_offset += 1;
			$now_timestamp -= 3600*24*$now_day;
			for($i=-1;$i>=$month_offset;--$i)
			{
				$t = date('t',strtotime($i." month"));
				$now_timestamp -= $t*3600*24;
			}
		}


		$now_day = date('j',$now_timestamp);
		$now_date = date('Y-m',$now_timestamp);
		for($i=1;$i<=$now_day;++$i)
		{
			$day = $i;
			if($day < 10)
			{
				$day = '0'.$day;
			}
			$date_list[] = $now_date."-".$day;
		}
	}
	return $date_list;
}

//sign检查
function sign_check($key,$parames,$open = 0)
{
	
	$result = 0;
	if($open == 1)
	{
		$debug_data = array();
		if(isset($parames['sign']))
		{
			//$debug_data['parames'] = $parames;
			//$debug_data['key'] = $key;

			$sign = $parames['sign'];
			unset($parames['sign']);
			ksort($parames);
			$tmp = md5(http_build_query($parames)."&key=".$key);

			if($tmp == $sign)//sign正确
			{
				$result = 1;
			}
			
			//$debug_data['sign'] = $tmp;
			//debug($debug_data);
		}
	}
	else
	{
		$result = 1;
	}
	return $result;
	
}

function curlrequest($url, $data, $method = 'post') {
	$ch = curl_init(); //初始化CURL句柄
	curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式
	$array = array();
	$array[] = "X-HTTP-Method-Override: $method";
	curl_setopt($ch, CURLOPT_HTTPHEADER, $array); //设置HTTP头信息
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //设置提交的字符串

	$document = curl_exec($ch); //执行预定义的CURL
	if (!curl_errno($ch)) {
		$info = curl_getinfo($ch);
	} else {
	}
	curl_close($ch);

	return $document;
}

/**
 * End of file common_helper.php
 */
/**
 * Location: ./application/helpers/common_helper.php
 */
