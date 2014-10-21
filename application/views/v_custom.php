<body>
	<div id="tabs" class="easyui-tabs" style="margin:0px;" fit="true" border="false">  
		<div title="<?php echo $page_title?>" style="padding:10px;" data-options="iconCls: 'icon-mini-add'" >
			<div id="customHeader"><a id="addCustom" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-add'">创建新客服账号</a>&nbsp;&nbsp;<a id="resetAllCustomPwd" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-reload'">重置所有客服密码</a></div>
			
			<table id="customList"></table>

		</div>
	</div>
	

	<script type="text/javascript">
	
	//模块定义 
	var customModule = function($){
		
		//显示添加客服窗口
		var showAddCustom = function(){
			$("#addCustom").click(
				function(){ 
					//显示loading
					$.messager.progress();
					$.ajax({
						type: "POST",
						url: "<?php echo $show_add_custom_url;?>",
						data: "",
						success: function(msg){
							$("body").append(msg);
							//影藏loading
							$.messager.progress('close');
						}
					});
				}
			);
		};

		//给重置所有客服密码 的按钮绑定事件
		var bindResetAllCustomPwd = function(){
			$("#resetAllCustomPwd").click(
				function(){ 
					resetAllCustomPwd();
				}
			);
		};

		//显示客服聊天列表窗口
		var showCustomChatList = function(custom_name){
			//显示loading
			$.messager.progress();
			$.ajax({
				type: "POST",
				url: "<?php echo $show_custom_chat_list_url;?>",
				data: "custom_name="+custom_name,
				success: function(msg){
					$("body").append(msg);
					//影藏loading
					$.messager.progress('close');
				}
			});
		};

		//重置一个客服密码
		var resetCustomPwd = function(custom_name){
			//显示loading
			$.messager.progress();
			$.ajax({
				type: "POST",
				url: "<?php echo $reset_customs_pwd_url;?>",
				data: "user_names="+custom_name,
				success: function(msg){
					var data = eval("("+msg+")");
					if(typeof(data.result) != "undefined" && data.result == 1)
					{
						$.messager.alert('操作通知','重置密码成功!','info');
					}
					else
					{
						$.messager.alert('操作通知','重置密码失败!','error');
					}
					//影藏loading
					$.messager.progress('close');
				}
			});
		};

		//重置所有客服密码
		var resetAllCustomPwd = function(){
			//显示loading
			$.messager.progress();
			$.ajax({
				type: "POST",
				url: "<?php echo $reset_customs_pwd_url;?>",
				data: "all=1",
				success: function(msg){
					var data = eval("("+msg+")");
					if(typeof(data.result) != "undefined" && data.result == 1)
					{
						$.messager.alert('操作通知','重置密码成功!','info');
					}
					else
					{
						$.messager.alert('操作通知','重置密码失败!','error');
					}
					//影藏loading
					$.messager.progress('close');
				}
			});
		};

		//删除一个客服
		var deleteCustom = function(custom_name){
			$.messager.confirm('删除提示', '确定删除客服:'+custom_name, function(r){
				if (r){
					//显示loading
					$.messager.progress();
					$.ajax({
						type: "POST",
						url: "<?php echo $delete_customs_url;?>",
						data: "user_names="+custom_name,
						success: function(msg){
							var data = eval("("+msg+")");
							if(typeof(data.result) != "undefined" && data.result == 1)
							{
								location.reload();
							}
							else
							{
								$.messager.alert('操作通知','删除失败!','error');
							}
							//影藏loading
							$.messager.progress('close');
						}
					});
				}
			});
		};
		
		//显示客服列表
		var showCustomDatagrid = function(){
			$('#customList').datagrid({
				url:"<?php echo $get_custom_list_url;?>",
				fitColumns:true,
				rownumbers:true,
				singleSelect:true,
				toolbar: '#customHeader',
				columns:[[
					{field:'F_custom_id',title:'客服ID',align:'center',hidden:true},
					{field:'F_groupname',title:'客服所属地区',align:'center',width:100},
					{field:'F_custom_name',title:'客服账号',align:'center',width:100},
					{field:'F_custom_createtime',title:'创建时间',align:'center',width:100},
					{field:'F_custom_status',title:'当前状态',align:'center',width:100},
					{field:'F_custom_receive',title:'咨询量(人)',align:'center',width:100},
					{field:'F_custom_replay',title:'回复量(条)',align:'center',width:100},
					{field:'F_look',title:'查看会话',align:'center',width:100,formatter: function(value,row,index){
						return '<a id="look_'+index+'" href="javascript:customModule.showCustomChatList(\''+row.F_custom_name+'\');"></a><script type="text/javascript">$(\'#look_'+index+'\').linkbutton({iconCls: \'icon-search\'});<\/script>';
					}},
					{field:'F_edit',title:'账号编辑',align:'center',width:100,formatter: function(value,row,index){
						return '<a href="#" id="custom_edit_'+index+'">编辑</a><div id="mm_'+index+'"><div data-options="iconCls:\'icon-remove\'" onClick="customModule.deleteCustom(\''+row.F_custom_name+'\');">删除</div><div data-options="iconCls:\'icon-redo\'" onClick="customModule.resetCustomPwd(\''+row.F_custom_name+'\');">重置密码</div></div><script type="text/javascript">$(\'#custom_edit_'+index+'\').menubutton({iconCls: \'icon-edit\',menu: \'#mm_'+index+'\'});<\/script>';
					}},
				]]
			});
		};

		//return obj
		var obj = {
			showAddCustom:function(){showAddCustom();bindResetAllCustomPwd();},
			showCustomDatagrid:function(){showCustomDatagrid();},
			showCustomChatList:function(custom_name){showCustomChatList(custom_name);},
			resetCustomPwd:function(custom_name){resetCustomPwd(custom_name);},
			deleteCustom:function(custom_name){deleteCustom(custom_name);},
		};

		//return
		return obj;

	}(jQuery);
	
	//模块调用
	customModule.showAddCustom();
	customModule.showCustomDatagrid();
	</script>