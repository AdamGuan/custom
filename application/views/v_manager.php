<body>
	<div id="tabs" class="easyui-tabs" style="margin:0px;" fit="true" border="false">  
		<div title="<?php echo $page_title?>" style="padding:10px;" data-options="iconCls: 'icon-mini-add'" >
			<div id="managerHeader"><a id="addManager" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-add'">创建新的管理员</a></div>
			
			<table id="managerList"></table>
		</div>
	</div>
	

	<script type="text/javascript">
	
	//模块定义 
	var managerModule = function($){

		//
		var doDelete = function(role_id){
			
			$.ajax({
				type: "POST",
				url: "<?php echo $manager_delete_url;?>",
				data: "manager_id="+role_id,
				success: function(msg){
					//影藏loading
					$.messager.progress('close');

					var data = eval("("+msg+")");
					if(typeof(data.result) != 'undefined' && data.result > 0)
					{
						location.reload();
					}
					else
					{
						$.messager.alert('操作','删除失败!','error');
					}
				}
			});
			
		};
		
		//show delete confirm
		var showDeleteManager = function(id,name){
			$.messager.confirm('操作通知', '删除管理员:'+name, function(r){
				if(r)
				{
					managerModule.doDelete(id);
				}
			});
		};
		
		//show modify win
		var showModifyMangerWin = function(uid,name,role_id,group_id,role_name){
			
			//显示loading
			$.messager.progress();
			var html = '<div id="managerDetailWin">';

			var content_name = '<input id="managerName" type="text" style="width:300px" value="'+name+'"><script>$(\'#managerName\').textbox({iconAlign:\'left\',readonly:true,iconCls:\'icon-man\'});<\/script>';

			var content_pwd = '<br /><input id="managerPwd" type="text" style="width:300px" value="" prompt=" 密码为空则不变"><script>$(\'#managerPwd\').textbox({iconAlign:\'left\',iconCls:\'icon-lock\'});<\/script>';

			var content_role = '<br />角色：<input id="role_list" name="role_list" value=""><script>$(\'#role_list\').combobox({data: <?php echo $role_list_json;?>,valueField:\'F_role_id\',textField:\'F_role_name\'});$(\'#role_list\').combobox(\'select\','+parseInt(role_id)+');<\/script>';
			
			var content_group = '';
			if(role_name != 'super admin')
			{
				content_group = '<br />地区：<input id="group_list" name="group_list" value=""><script>$(\'#group_list\').combobox({data: <?php echo $group_list_json;?>,valueField:\'F_id\',textField:\'F_groupname\'});$(\'#group_list\').combobox(\'select\','+parseInt(group_id)+')<\/script>';
			}

			var content_btn = '<br /><a id="manager_modify_save" href="javascript:managerModule.doModifyManager('+uid+');">保存</a><script>$(\'#manager_modify_save\').linkbutton({iconCls: \'icon-save\'});<\/script>';

			html += content_name+content_pwd+content_role+content_group+content_btn+'</div>';
			
			$("#managerList").after(html);

			$('#managerDetailWin').window({
				fit:true,
				modal:true,
				collapsible:false,
				minimizable:false,
				resizable:false,
				title:"修改管理员",
				onClose:function(){
					$("#managerDetailWin").window('clear');
					$("#managerDetailWin").window('destroy',true);
					$("#managerDetailWin").remove();
				}
			});
			$('#managerDetailWin').window('open');
			//影藏loading
			$.messager.progress('close');
			
		};
		
		//修改
		var doModifyManager = function(uid){
			var manager_id = uid;
			var user_pwd = $("#managerPwd").val();
			var role_id = $('#role_list').combobox('getValue');
			if(typeof($('#group_list')) != 'undefined')
			{
				var group_id = $('#group_list').combobox('getValue');
			}
			if(typeof(manager_id) != 'undefined')
			{
				//显示loading
				$.messager.progress();

				var sendata = '1=1';
				sendata += '&manager_id='+manager_id;
				if(typeof(user_pwd) != 'undefined' && user_pwd.length > 0)
				{
					sendata += '&user_pwd='+user_pwd;
				}
				if(typeof(role_id) != 'undefined' && role_id > 0)
				{
					sendata += '&role_id='+role_id;
				}
				if(typeof(group_id) != 'undefined' && group_id > 0)
				{
					sendata += '&group_id='+group_id;
				}
				$.ajax({
					type: "POST",
					url: "<?php echo $manager_modify_url;?>",
					data: sendata,
					success: function(msg){
						//影藏loading
						$.messager.progress('close');

						var data = eval("("+msg+")");
						if(typeof(data.result) != 'undefined' && data.result > 0)
						{
							$('#managerDetailWin').window('close');
							$("#managerDetailWin").window('clear');
							$("#managerDetailWin").remove();
							location.reload();
						}
						else
						{
							$.messager.alert('操作','修改失败!','error');
						}
					}
				});
			}
			else
			{
				//影藏loading
				$.messager.progress('close');
				$.messager.alert('操作','修改失败!','error');
			}
		};
		
		//show add win
		var addManager = function(){
			
			//显示loading
			$.messager.progress();
			var html = '<div id="managerAddWin">';

			var content_name = '<input id="managerName" type="text" style="width:300px" value="'+name+'" prompt="用户名"><script>$(\'#managerName\').textbox({iconAlign:\'left\',iconCls:\'icon-man\'});<\/script>';

			var content_pwd = '<br /><input id="managerPwd" type="text" style="width:300px" value="" prompt=" 用户密码为"><script>$(\'#managerPwd\').textbox({iconAlign:\'left\',iconCls:\'icon-lock\'});<\/script>';

			var content_role = '<br />角色：<input id="role_list" name="role_list" value=""><script>$(\'#role_list\').combobox({data: <?php echo $role_list_json;?>,valueField:\'F_role_id\',textField:\'F_role_name\'});<\/script>';
			
			content_group = '<br />地区：<input id="group_list" name="group_list" value=""><script>$(\'#group_list\').combobox({data: <?php echo $group_list_json;?>,valueField:\'F_id\',textField:\'F_groupname\'});<\/script>';

			var content_btn = '<br /><a id="manager_add_save" href="javascript:managerModule.doAddManager();">保存</a><script>$(\'#manager_add_save\').linkbutton({iconCls: \'icon-save\'});<\/script>';

			html += content_name+content_pwd+content_role+content_group+content_btn+'</div>';

			$("#managerList").after(html);
			$('#managerAddWin').window({
				fit:true,
				modal:true,
				collapsible:false,
				minimizable:false,
				resizable:false,
				title:"添加管理员",
				onClose:function(){
					$("#managerAddWin").window('clear');
					$("#managerAddWin").window('destroy',true);
					$("#managerAddWin").remove();
				}
			});
			$('#managerAddWin').window('open');
			//影藏loading
			$.messager.progress('close');
			
		};

		//
		var doAddManager = function(){
			
			var name = $("#managerName").val();
			var pwd= $("#managerName").val();
			var role_id = $("#role_list").combobox('getValue');
			var group_id = $("#group_list").combobox('getValue');
			if(name.length > 0 && pwd.length > 0 && role_id > 0)
			{
				if(typeof(group_id) == "undefined")
				{
					group_id = 0;
				}

				//显示loading
				$.messager.progress();

				$.ajax({
					type: "POST",
					url: "<?php echo $manager_add_url;?>",
					data: "role_id="+role_id+"&username="+name+"&userpwd="+pwd+"&group_id="+group_id,
					success: function(msg){
						//影藏loading
						$.messager.progress('close');

						var data = eval("("+msg+")");
						if(typeof(data.result) != 'undefined' && data.result > 0)
						{
							location.reload();
						}
						else
						{
							$.messager.alert('操作','添加失败!','error');
						}
					}
				});

			}
			else
			{
				$.messager.alert('操作','添加失败!','error');
			}
			
		};
		
		//bind click event
		var addManagerBtnAttchClickEvent = function(){
			$("#addManager").click( function () {managerModule.addManager();});
		};
		
		//显示列表
		var showCustomDatagrid = function(){
			$('#managerList').datagrid({
				data:<?php echo $manager_list_json;?>,
				fitColumns:true,
				rownumbers:true,
				singleSelect:true,
				toolbar: '#managerHeader',
				columns:[[
					{field:'F_user_id',title:'ID',align:'center',hidden:true},
					{field:'F_user_login_name',title:'用户名',align:'center',width:100},
					{field:'F_role_name',title:'角色',align:'center',width:100},
					{field:'F_groupname',title:'所属地区',align:'center',width:100,formatter: function(value,row,index){
						
						if(typeof(value) == 'undefined' || value == 'null' || value == null)
						{
							return '全部地区';
						}
						else
						{
							return value;
						}
					}},
					{field:'F_create_time',title:'创建时间',align:'center',width:100},
					{field:'F_action',title:'操作',align:'center',width:100,formatter: function(value,row,index){
						return '<a href="#" id="manager_edit_'+index+'">操作</a><div id="mm_'+index+'"><div data-options="iconCls:\'icon-remove\'" onClick="managerModule.showDeleteManager(\''+row.F_user_id+'\',\''+row.F_user_login_name+'\');">删除</div><div data-options="iconCls:\'icon-edit\'" onClick="managerModule.showModifyMangerWin(\''+row.F_user_id+'\',\''+row.F_user_login_name+'\',\''+row.F_role_id+'\',\''+row.F_group_id+'\',\''+row.F_role_name+'\');">修改</div></div><script type="text/javascript">$(\'#manager_edit_'+index+'\').menubutton({iconCls: \'icon-edit\',menu: \'#mm_'+index+'\'});<\/script>';
					}},
				]]
			});
		};

		//return obj
		var obj = {
			start:function(){showCustomDatagrid();addManagerBtnAttchClickEvent();},
			showDeleteManager:function(id,name){showDeleteManager(id,name);},
			showModifyMangerWin:function(uid,name,role_id,group_id,role_name){showModifyMangerWin(uid,name,role_id,group_id,role_name);},
			addManager:function(){addManager();},
			doModifyManager:function(uid){doModifyManager(uid);},
			doDelete:function(role_id){doDelete(role_id);},
			doAddManager:function(){doAddManager();},
		};

		//return
		return obj;

	}(jQuery);
	
	//模块调用
	managerModule.start();
	</script>