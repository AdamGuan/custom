<body>
	
	<div id="loginContainer">
		<div style="font-size: 24px;text-align: center;padding-top: 10px;">微客服后台</div>
		<form id="login" method="post" style="margin:0px auto;margin-top: 120px;">
			<div style="margin:0px auto 10px auto;text-align:center;">
				<input class="easyui-textbox" type="text" name="name" prompt="用户名" id="name" data-options="iconCls:'icon-man',iconAlign:'left'" />
			</div>
			<div style="margin:0px auto 10px auto;text-align:center;">
				<input class="easyui-textbox" type="password" name="pwd" prompt="密码" id="pwd" value="" data-options="iconCls:'icon-lock',iconAlign:'left'" />
			</div>
			<div style="margin:0px auto 10px auto;text-align:center;">
				<input class="easyui-textbox" type="text" name="cap" prompt="验证码" id="cap" data-options="" />
			</div>
			<span id="cap_img" style="cursor: pointer; margin-top: -40px; float: right;margin-right: 50px;"><?php echo $cap['image'];?></span>
			<div style="margin:0px auto 10px auto;text-align:center;">
				<a id="btn" href="#" class="easyui-linkbutton" data-options="">登录</a>
			</div>
		</form>
	</div>
	

	<script type="text/javascript" language="javascript">

	var loginModule = function($){

		var cap_text = "<?php echo $cap['word'];?>";

		var doLogin = function(){
			if(valid())
			{
				//显示loading
				$.messager.progress();
				//登录
				$.ajax({
					type: "POST",
					url: "<?php echo $login_valid_url;?>",
					data: "name="+$("#name").textbox('getText')+"&pwd="+$("#pwd").textbox('getText')+"&cap="+$("#cap").textbox('getText'),
					success: function(msg){
						//解析返回数据
						var data = eval("("+msg+")");
						//替换cap
						if(typeof(data.cap) != "undefined")
						{
							cap_text = data.cap.word;
							$("#cap_img").html(data.cap.image);
						}
						
						//影藏loading
						$.messager.progress('close');
						//
						if(typeof(data.redirect_url) != "undefined")
						{
							//跳转网页
							window.location.href=data.redirect_url;
						}
						else
						{
							if(typeof(data.error) != "undefined")
							{
								if(data.error == -2)
								{
									//显示错误信息
									$.messager.alert('登录信息','验证码过期','info');
								}
								else if(data.error == -3)
								{
									//显示错误信息
									$.messager.alert('登录信息','用户名或密码错误','info');
								}
							}
						}
					}
				});
			}
		};

		var login = function(){
			$("#btn").click(
				function(){ 
					doLogin();
				}
			);
		};

		//验证用户名以及密码规则
		var valid = function(){
			var name = $("#name").textbox('getText');
			var pwd = $("#pwd").textbox('getText');
			var cap = $("#cap").textbox('getText');
			if(name.length > 0 && pwd.length > 0 && cap == cap_text)
			{
				return true;
			}
			else
			{
				if(cap != cap_text)
				{
					alert('验证码错误');
//					$.messager.alert('登录信息','验证码错误','info');
				}
				else if(name.length <= 0)
				{
					alert('请填写用户名');
//					$.messager.alert('登录信息','请填写用户名','info');
				}
				else if(pwd.length <= 0)
				{
					alert('请填写密码');
//					$.messager.alert('登录信息','请填写密码','info');
				}
				return false;
			}
		};

		//获取验证码
		var get_cap = function(){
			$("#cap_img").hover(
				function () {
					$(this).css({"cursor":"pointer"});
				}
			);

			$("#cap_img").click(
				function(){ 
					//显示loading
					$.messager.progress();
					//登录
					$.ajax({
						type: "POST",
						url: "<?php echo $get_cap_url;?>",
						success: function(msg){
							//解析返回数据
							var data = eval("("+msg+")");
							//替换cap
							if(typeof(data.cap) != "undefined")
							{
								cap_text = data.cap.word;
								$("#cap_img").html(data.cap.image);
							}
							
							//影藏loading
							$.messager.progress('close');
						}
					});
				}
			);
		};

		var setContainer = function(){
			$('#loginContainer').window({
				title:"请登录",
				width:600,
				height:400,
				iconCls:'icon-pre',
				modal:false,
				collapsible:false,
				minimizable:false,
				maximizable:false,
				closable:false,
				draggable:false,
				resizable:false,
				shadow:false,
				onOpen:function(){
					$('#loginContainer').window('center');
					var top = ($(document).height() - $(".panel").height())/2;
					$(".panel").css("top",top);
				}
			});
		};

		var formBindEvent = function(){
			$("#login").keydown(function(event){
				switch(event.keyCode) {
					case 13:
						doLogin();
						break;
				}
			});
		};

		//return obj
		var obj = {
			login:function(){setContainer();login();get_cap();formBindEvent();}
		};
		//return
		return obj;
	}(jQuery);

	loginModule.login();
	

	</script>