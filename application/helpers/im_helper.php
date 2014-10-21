<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

//
function im_get_token($org_name,$app_name,$client_id,$client_secret)
{
	$formgettoken="https://a1.easemob.com/".$org_name."/".$app_name."/token";
	$body=array(
		"grant_type"=>"client_credentials",
		"client_id"=>$client_id,
		"client_secret"=>$client_secret
	);
	$patoken=json_encode($body);
	$res = im_curl_request($formgettoken,$patoken);
	$tokenResult = array();
	
	$tokenResult =  json_decode($res, true);
//	var_dump($tokenResult);exit;
	return "Authorization: Bearer ". $tokenResult["access_token"];
}

//
function im_curl_request($url, $body, $header = array(), $method = "POST")
{
	array_push($header, 'Accept:application/json');
	array_push($header, 'Content-Type:application/json');

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);     //接收数据超时
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); //连接超时
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($ch, $method, 1);
	
	switch ($method){ 
		case "GET" : 
			curl_setopt($ch, CURLOPT_HTTPGET, true);
		break; 
		case "POST": 
			curl_setopt($ch, CURLOPT_POST,true); 
		break; 
		case "PUT" : 
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
		break; 
		case "DELETE":
			curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); 
		break; 
	}
	
	curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	if (isset($body{3}) > 0) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
	}
	if (count($header) > 0) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}

	$ret = curl_exec($ch);
	$err = curl_error($ch);

	curl_close($ch);
	//clear_object($ch);
	//clear_object($body);
	//clear_object($header);

	if ($err) {
		return $err;
	}

	return $ret;
}

function im_curl_request_multi($url_list, $body, $headernew = array(), $method = "POST")
{
	$ch_list = array();

	// 创建批处理cURL句柄
	$mh = curl_multi_init();

	foreach($url_list as $key=>$url)
	{
		
		$header = $headernew;

		array_push($header, 'Accept:application/json');
		array_push($header, 'Content-Type:application/json');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);//连接超时
		curl_setopt($ch, CURLOPT_TIMEOUT, 5 );     //接收数据超时
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, $method, 1);
		
		switch ($method){ 
			case "GET" : 
				curl_setopt($ch, CURLOPT_HTTPGET, true);
			break; 
			case "POST": 
				curl_setopt($ch, CURLOPT_POST,true); 
			break; 
			case "PUT" : 
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
			break; 
			case "DELETE":
				curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); 
			break; 
		}
		
		curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		if (isset($body{3}) > 0) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		}
		if (count($header) > 0) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}
		
		// 增加句柄
		curl_multi_add_handle($mh,$ch);

		$ch_list[] = $ch;
	}
	
	// 执行批处理句柄
	$running=null;
	do {
		curl_multi_exec($mh,$running);
	} while ($running > 0);

	//获取内容
	$res=array();
	foreach($ch_list as $ch)
	{
		$res[]=json_decode(curl_multi_getcontent($ch),true);
		//移除句柄
		curl_multi_remove_handle($mh,$ch);
	}
	//close
	curl_multi_close($mh);

	//return
	return $res;
}

function im_do($uri,$method = "POST",$post_data = array(),$query='')
{
	$CI =& get_instance();
	$org_name = $CI->my_config['org_name'];
	$app_name = $CI->my_config['app_name'];
	$client_id = $CI->my_config['client_id'];
	$client_secret = $CI->my_config['client_secret'];
	$im_api_url = $CI->my_config['im_api_url'];

	if(strlen($query) > 0)
	{
		$query = str_replace(" ","+",$query);
		$query = "?".utf8_encode($query);
	}

	$formgettoken = $im_api_url.$org_name."/".$app_name."/".$uri.$query;

	$patoken=json_encode($post_data);
	$header = array(im_get_token($org_name,$app_name,$client_id,$client_secret));

	$res = im_curl_request($formgettoken,$patoken,$header,$method);
	$arrayResult =  json_decode($res, true);
	return $arrayResult;
}

function im_do_multi($uri_list,$method = "POST",$post_data = array(),$query='')
{
	$CI =& get_instance();
	$org_name = $CI->my_config['org_name'];
	$app_name = $CI->my_config['app_name'];
	$client_id = $CI->my_config['client_id'];
	$client_secret = $CI->my_config['client_secret'];
	$im_api_url = $CI->my_config['im_api_url'];

	if(strlen($query) > 0)
	{
		$query = str_replace(" ","+",$query);
		$query = "?".utf8_encode($query);
	}
	
	$formgettokenList = array();
	foreach($uri_list as $uri)
	{
		$formgettokenList[] = $im_api_url.$org_name."/".$app_name."/".$uri.$query;
	}
	$patoken=json_encode($post_data);
	$header = array(im_get_token($org_name,$app_name,$client_id,$client_secret));
	$arrayResult = im_curl_request_multi($formgettokenList,$patoken,$header,$method);
	return $arrayResult;
}

/**
 * End of file im_helper.php
 */
/**
 * Location: ./application/helpers/im_helper.php
 */