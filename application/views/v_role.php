<body>
	<div id="tabs" class="easyui-tabs" style="margin:0px;" fit="true" border="false">  
		<div title="<?php echo $page_title?>" style="padding:10px;" data-options="iconCls: 'icon-mini-add'" >
			<div id="roleHeader"><a id="addRole" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-add'">创建新角色</a></div>
			<table id="roleList"></table>
		</div>
	</div>
	

	<script type="text/javascript">
	
	//模块定义 
	var roleModule = function($){
		
		//保存编辑的角色
		var doEditRole = function(role_id){
			if(typeof(role_id) != 'undefined' && role_id > 0)
			{
				//显示loading
				$.messager.progress();

				var permission = '';
				var nodes = $('#tt').tree('getChecked');
				for(var i=0;i<nodes.length;++i)
				{
					if(permission.length <= 0)
					{
						permission += nodes[i].attributes.value;
					}
					else
					{
						permission += ','+nodes[i].attributes.value;
					}
				}

				$.ajax({
					type: "POST",
					url: "<?php echo $update_role_url;?>",
					data: "role_id="+role_id+"&permision="+permission,
					success: function(msg){
						//影藏loading
						$.messager.progress('close');

						var data = eval("("+msg+")");
						if(typeof(data.result) != 'undefined' && data.result > 0)
						{
							$("#roleDetailWin").window('close');
							$("#roleDetailWin").window('clear');
							$("#roleDetailWin").window('destroy',true);
							$("#roleDetailWin").remove();

							$.messager.alert('操作','修改成功!','notice');
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
				$.messager.alert('操作','修改失败!','error');
			}
		};

		//显示role列表
		var showRoleDatagrid = function(){
			$('#roleList').datagrid({
				data:<?php echo $role_list_json;?>,
				fitColumns:true,
				rownumbers:true,
				singleSelect:true,
				toolbar: '#roleHeader',
				columns:[[
					{field:'F_role_id',title:'ID',align:'center',hidden:true},
					{field:'F_role_name',title:'角色',align:'center',width:100},
					{field:'action',title:'操作',align:'center',width:100,formatter: function(value,row,index){
						return '<a href="#" id="role_edit_'+index+'">操作</a><div id="mm_'+index+'"><div data-options="iconCls:\'icon-remove\'" onClick="roleModule.deleteConfirm(\''+row.F_role_id+'\',\''+row.F_role_name+'\');">删除</div><div data-options="iconCls:\'icon-edit\'" onClick="roleModule.showRoleDetailWin(\''+row.F_role_id+'\',\''+row.F_role_name+'\');">修改</div></div><script type="text/javascript">$(\'#role_edit_'+index+'\').menubutton({iconCls: \'icon-edit\',menu: \'#mm_'+index+'\'});<\/script>';
					}},
				]]
			});
		};
		
		//显示角色详细
		var showRoleDetailWin = function(role_id,role_name){
			//显示loading
			$.messager.progress();
			$.ajax({
				type: "POST",
				url: "<?php echo $role_detail_url;?>",
				data: "role_id="+role_id,
				success: function(msg){
					var data = eval("("+msg+")");
					if(typeof(data.permission_data_json) != 'undefined')
					{
						var html = '<div id="roleDetailWin">';

						var content_role_name = '<input id="rolename" type="text" style="width:300px" value="'+role_name+'"><script>$(\'#rolename\').textbox({iconAlign:\'left\',readonly:true,iconCls:\'icon-man\'});<\/script>';

						var content_permission = '<ul id="tt"></ul><script>$(\'#tt\').tree({data: '+data.permission_data_json+',lines:true,checkbox:true});<\/script>';

						var submit_btn = '<a id="role_edit_save" href="javascript:roleModule.doEditRole(\''+role_id+'\');">保存</a><script>$(\'#role_edit_save\').linkbutton({iconCls: \'icon-save\'});<\/script>';

						html += content_role_name+content_permission+submit_btn+'</div>';
						
						$("#roleList").after(html);

						$('#roleDetailWin').window({
							fit:true,
							modal:true,
							collapsible:false,
							minimizable:false,
							resizable:false,
							title:"修改角色",
							onClose:function(){
								$("#roleDetailWin").window('clear');
								$("#roleDetailWin").window('destroy',true);
								$("#roleDetailWin").remove();
							}
						});
						$('#roleDetailWin').window('open');
						//影藏loading
						$.messager.progress('close');
					}
					else
					{
						//影藏loading
						$.messager.progress('close');
						//
						$.messager.alert('角色信息','获取信息失败!','error');
					}
				}
			});
		};

		//添加角色
		var doAddRole = function(){
			//显示loading
			$.messager.progress();

			var role_name = $("#add_rolename").val();
			var permision = '';
			var nodes = $('#add_tt').tree('getChecked');
			for(var i=0;i<nodes.length;++i)
			{
				if(permision.length <= 0)
				{
					permision += nodes[i].attributes.value;
				}
				else
				{
					permision += ','+nodes[i].attributes.value;
				}
			}
			if(role_name.length > 0)
			{
				$.ajax({
					type: "POST",
					url: "<?php echo $add_role_url;?>",
					data: "role_name="+role_name+"&permision="+permision,
					success: function(msg){
						//影藏loading
						$.messager.progress('close');

						var data = eval("("+msg+")");
						if(typeof(data.result) != 'undefined' && data.result > 0)
						{
							$("#roleAddWin").window('close');
							$("#roleAddWin").window('clear');
							$("#roleAddWin").window('destroy',true);
							$("#roleAddWin").remove();

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
				//影藏loading
				$.messager.progress('close');
				$.messager.alert('操作','添加失败!','error');
			}
		};

		//显示添加角色窗口
		var showCreateRoleWin = function(){
			var html = '<div id="roleAddWin">';

			var content_role_name = '<input id="add_rolename" type="text" style="width:300px" value=""><script>$(\'#add_rolename\').textbox({iconAlign:\'left\',iconCls:\'icon-man\'});<\/script>';

			var content_permission = '<ul id="add_tt"></ul><script>$(\'#add_tt\').tree({data: <?php echo $all_permission_data_list_json;?>,lines:true,checkbox:true});<\/script>';

			var submit_btn = '<a id="role_add_save" href="javascript:roleModule.doAddRole();">保存</a><script>$(\'#role_add_save\').linkbutton({iconCls: \'icon-save\'});<\/script>';

			html += content_role_name+content_permission+submit_btn+'</div>';
			
			$("#roleList").after(html);

			$('#roleAddWin').window({
				fit:true,
				modal:true,
				collapsible:false,
				minimizable:false,
				resizable:false,
				title:"添加角色",
				onClose:function(){
					$("#roleAddWin").window('clear');
					$("#roleAddWin").window('destroy',true);
					$("#roleAddWin").remove();
				}
			});
			$('#roleAddWin').window('open');
		};
		
		//监听添加新角色按钮
		var addClick = function(){
			$("#addRole").click(function(){
				roleModule.showCreateRoleWin();
			});
		};
		
		//显示角色删除确认框
		var deleteConfirm = function(role_id,role_name){
			$.messager.confirm('操作通知', '删除角色:'+role_name, function(r){
				if(r)
				{
					roleModule.doDelete(role_id);
				}
			});
		};
		
		//删除角色
		var doDelete = function(role_id){

			if(typeof(role_id) != 'undefined' && role_id > 0)
			{
				//显示loading
				$.messager.progress();
				$.ajax({
					type: "POST",
					url: "<?php echo $delete_role_url;?>",
					data: "role_id="+role_id,
					success: function(msg){
						var data = eval("("+msg+")");
						//成功
						if(typeof(data.result) != 'undefined' && data.result > 0)
						{
							//reload page
							$.messager.confirm('操作通知', '删除成功!', function(r){
								location.reload();
							});
						}
						else	//失败
						{
							$.messager.alert('操作通知','删除失败!可能已不存在.','error');
						}
						//影藏loading
						$.messager.progress('close');
					}
				});
			}
			else
			{
				$.messager.alert('操作通知','删除失败!','error');
			}

		};

		//return obj
		var obj = {
			showRoleDetailWin:function(role_id,role_name){showRoleDetailWin(role_id,role_name);},
			start:function(){showRoleDatagrid();addClick();},
			doEditRole:function(role_id){doEditRole(role_id);},
			showCreateRoleWin:function(){showCreateRoleWin();},
			doAddRole:function(){doAddRole();},
			deleteConfirm:function(role_id,role_name){deleteConfirm(role_id,role_name);},
			doDelete:function(role_id){doDelete(role_id);},
		};

		//return
		return obj;

	}(jQuery);
	
	//模块调用
	roleModule.start();
	
	</script>