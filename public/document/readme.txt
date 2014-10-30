版本:v1

url+sign计算(以下为伪代码):

	//$key为获取的key
	$key;

	//$url为要请求的api url
	$url;

	//$parames为是POST或GET请求的参数数组
	//$parames里面应包含一个name参数, 参数值:如果是客服则为客服名(环信中的账户名)，如果是用户则为用户名(环信中的账户名)
	$parames;
	
	//函数ksort 给数组按照key排序
	ksort($parames);
	
	//函数http_build_query 组装参数$parames为url参数的形式即 a=1&b=2&c=3 这种
	$str = http_build_query($parames);	
	
	//md5加密得到sign
	$sign = md5($str."&key=".$key);
	
	//得到url
	$url = $url."?sign=".$sign
========================================================================================

(用户/客服 使用) (sign要)
写入消息：
	地址:
		'http://115.28.232.58/custom/c_api/msg';
	请求方式:
		POST
	参数:
		from		string		环信用户名(谁发出)
		to		string		环信用户名(谁接收)
		msg_id:		string		环信生成的消息ID
		timestamp:	string		环信发消息返回的时间戳（毫秒）
		who_to_who:	int		1:客服发给用户,0:用户发给客服
	返回:
		json格式
			{
				"responseNo":int
			}

		responseNo	错误代码:
			[0|-1]	0写入成功，-1写入失败
	其它说明:

========================================================================================

(用户使用) (sign要)
用户给客服评价：
	地址:
		'http://115.28.232.58/custom/c_api/grade';
	请求方式:
		POST
	参数:
		from		string		用户(环信用户名)
		to		string		客服(环信用户名)
		score:		float		评价分数
		content:	string		评价内容
	返回:
		json格式
			{
				"responseNo":int
			}

		responseNo	错误代码:
			[0|-1]	0成功，-1失败
	其它说明:

========================================================================================

(用户使用) (sign要)
用户查找一个客服：
	地址:
		'http://115.28.232.58/custom/c_api/find';
	请求方式:
		POST
	参数:
		local		string		地区(如：广东 or 湖南)
	返回:
		json格式
			{
				"responseNo":int,
				"custom_name":string,
				"custom_nickname":string,
			}

		responseNo	错误代码:
			[0|-1]	0成功，-1失败
		custom_name	客服（环信用户名）
		custom_nickname	客服昵称
	其它说明:
		会优先查找对应地区在线中的一个客服，如果没有任何在线，则随机查找一个对应地区的客服，如果这个地区没有任何一个客服则返回-1

========================================================================================

(用户使用)
获取用户信息：
	地址:
		'http://115.28.232.58/custom/c_api/user_info';
	请求方式:
		POST
	参数:
		username		string		用户名(用户用心的那个email地址账号)
		userpwd		 	string		用户密码(用户中心的那个32位密码)
	返回:
		json格式
			{
				"responseNo":INT,
				"username":string,
				"userpwd":string,
				"key":string,
				"user_avatar_url":string
			}

		responseNo	错误代码:
			[0|-1]	0成功，-1失败
		username	用户账户
		userpwd		用户密码
		key		计算sign用
		user_avatar_url	用户头像url
	其它说明:
		
		1 .如果没有注册就会注册后返回用户信息
		2. 如果注册了就直接返回用户信息。

========================================================================================

(用户使用) (sign要)
获取地区列表：
	地址:
		'http://115.28.232.58/custom/c_api/local_list';
	请求方式:
		GET/POST
	参数:
	返回:
		json格式
			{
				"responseNo":INT,
				"local_list":[],
			}

		responseNo	错误代码:
			[0|-1]	0成功，-1失败
		local_list	地区列表,一维array
	其它说明:

========================================================================================

(客服使用) (sign要)
修改客服昵称：
	地址:
		'http://115.28.232.58/custom/c_api/modify_custom_nickname';
	请求方式:
		GET/POST
	参数:
		custom_name	string	客服名(环信用户名)
		custom_nickname	string	客服昵称
	返回:
		json格式
			{
				"responseNo":INT,
			}

		responseNo	错误代码:
			[0|-1]	0成功，-1失败
	其它说明:
		custom_nickname 非空，长度大于登录1 ，小于等于20

========================================================================================

(客服使用) (sign要)
修改客服密码：
	地址:
		'http://115.28.232.58/custom/c_api/modify_custom_pwd';
	请求方式:
		GET/POST
	参数:
		custom_name		string	客服名(环信用户名)
		custom_pwd_current	string	客服当前密码
		custom_pwd_new		string	客服新密码
	返回:
		json格式
			{
				"responseNo":INT,
			}

		responseNo	错误代码:
			[0|-1]	0成功，-1失败
	其它说明:
		custom_pwd_new正则： /^[0-9a-zA-Z\-_]{6,20}$/i

========================================================================================

(客服使用)
获取客服信息：
	地址:
		'http://115.28.232.58/custom/c_api/custom_info';
	请求方式:
		GET/POST
	参数:
		custom_name	string	客服名(环信用户名)
		custom_pwd	string	客服密码
	返回:
		json格式
			{
				"responseNo":INT,
				"custom_name":string,
				"custom_nickname":string,
				"local":string,
				"key":string,
			}

		responseNo	错误代码:
			[0|-1]	0成功，-1失败
		custom_name	客服名 (环信用户名)
		custom_nickname	客服昵称
		local		所属地区
		key		计算sign用
	其它说明:
		custom_pwd_new正则： /^[0-9a-zA-Z\-_]{6,20}$/i

========================================================================================

(客服使用) (sign要)
添加客服与用户的关系：
	地址:
		'http://115.28.232.58/custom/c_api/add_custom_relation';
	请求方式:
		GET/POST
	参数:
		custom_name	string	客服名(环信用户名)
		user_name	string	用户名(环信用户名)
	返回:
		json格式
			{
				"responseNo":INT
			}

		responseNo	错误代码:
			[0|-1]	0成功，-1失败
	其它说明:
	
========================================================================================

(客服使用) (sign要)
删除客服与用户的关系：
	地址:
		'http://115.28.232.58/custom/c_api/delete_custom_relation';
	请求方式:
		GET/POST
	参数:
		custom_name	string	客服名(环信用户名)
		user_name	string	用户名(环信用户名)
	返回:
		json格式
			{
				"responseNo":INT
			}

		responseNo	错误代码:
			[0|-1]	0成功，-1失败
	其它说明:
	
========================================================================================

(客服使用) (sign要)
查询客服与用户的关系：
	地址:
		'http://115.28.232.58/custom/c_api/get_custom_relation';
	请求方式:
		GET/POST
	参数:
		custom_name	string	客服名(环信用户名)
	返回:
		json格式
			{
				"responseNo":INT,
				"list":[{"add_datetime":datetime,"user_name":string,"register_name":string}]
			}

		responseNo	错误代码:
			[0|-1]	0成功，-1失败
		list		与客户有关系的用户列表（二维）
			add_datetime	添加时间
			user_name		用户名(环信用户名)
			register_name	注册名
	其它说明:
	
========================================================================================

(客服使用) (sign要)
客服查询用户的信息：
	地址:
		'http://115.28.232.58/custom/c_api/get_other_user_info';
	请求方式:
		GET/POST
	参数:
		user	string	用户名(环信用户名)
	返回:
		json格式
			{
				"responseNo":INT,
				"user_register_name":string,
				"user_avatar_url":string
			}

		responseNo	错误代码:
			[0|-1]	0成功，-1失败
		user_register_name		用户注册名(email地址)
		user_avatar_url			用户头像url
	其它说明:
	
========================================================================================
