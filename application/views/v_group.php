<body>
	<div id="tabs" class="easyui-tabs" style="margin:0px;" fit="true" border="false">  
		<div title="<?php echo $page_title?>" style="padding:10px;" data-options="iconCls: 'icon-mini-add'" >
			<div id="groupHeader"><a id="addGroup" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-add'">创建新的地区</a></div>
			
			<table id="groupList"></table>

		</div>
	</div>

	<div id="groupModifyWin"></div>
	<div id="groupAddWin"></div>

	<script type="text/javascript">
	
	//模块定义 
	var groupModule = function($){
		
		//弹出确认删除框
		var deleteGroup = function(group_id,group_name){
			$.messager.confirm('操作通知', '删除地区:'+group_name, function(r){
				if(r)
				{
					groupModule.doDeleteGroup(group_id);
				}
			});
		};

		//删除组名
		var doDeleteGroup = function(group_id){
			if(typeof(group_id) != 'undefined' && group_id > 0)
			{
				//显示loading
				$.messager.progress();
				$.ajax({
					type: "POST",
					url: "<?php echo $delete_group_url;?>",
					data: "group_id="+group_id,
					success: function(msg){
						var data = eval("("+msg+")");
						//成功
						if(typeof(data.result) != 'undefined' && data.result == '1')
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
		
		//弹出修改窗口
		var editGroup = function(group_id,group_name){
			//modify_group_url
			var html = '<input id="groupname" type="text" value="'+group_name+'" style="width:200px"><a id="groupSaveBtn" href="javascript:groupModule.doEditGroup('+group_id+',\''+group_name+'\');">保存</a><script type="text/javascript">$(\'#groupname\').textbox({});$(\'#groupSaveBtn\').linkbutton({iconCls: \'icon-save\'});<\/script>';
			if(typeof($('#groupModifyWin').attr("opend")) == "undefined")
			{
				$('#groupModifyWin').window({
					width:300,
					height:300,
					modal:true,
					collapsible:false,
					minimizable:false,
					resizable:false,
					title:"地区修改",
					onClose:function(){
						$("#groupModifyWin").window('clear');
					}
				});
				$('#groupModifyWin').html(html);
				$('#groupModifyWin').window('open');
				$('#groupModifyWin').attr("opend",1);
			}
			$('#groupModifyWin').html(html);
			$('#groupModifyWin').window('open');
		};

		//修改组名
		var doEditGroup = function(group_id){
			var group_name = $("#groupname").val();
			if(typeof(group_id) != 'undefined' && typeof(group_name) != 'undefined' && group_name.length > 0 && group_id > 0)
			{
				//显示loading
				$.messager.progress();
				$.ajax({
					type: "POST",
					url: "<?php echo $modify_group_url;?>",
					data: "group_id="+group_id+"&group_name="+group_name,
					success: function(msg){
						var data = eval("("+msg+")");
						//成功
						if(typeof(data.result) != 'undefined' && data.result > 0)
						{
							$('#groupModifyWin').window('close');
							$("#groupModifyWin").window('clear');
							//reload page
							$.messager.confirm('操作通知', '修改成功!', function(r){
								location.reload();
							});
						}
						else	//失败
						{
							$.messager.alert('操作通知','修改失败!可能重名.','error');
						}
						//影藏loading
						$.messager.progress('close');
					}
				});
			}
			else
			{
				$.messager.alert('操作通知','修改失败!','error');
			}
		};

		//弹出添加窗口
		var addGroup = function(){
			//modify_group_url
			var html = '<input id="addGroupname" type="text" value="" style="width:200px"><a id="addGroupSaveBtn" href="javascript:groupModule.doAddGroup();">保存</a><script type="text/javascript">$(\'#addGroupname\').textbox({});$(\'#addGroupSaveBtn\').linkbutton({iconCls: \'icon-save\'});<\/script>';
			if(typeof($('#groupAddWin').attr("opend")) == "undefined")
			{
				$('#groupAddWin').window({
					width:300,
					height:300,
					modal:true,
					collapsible:false,
					minimizable:false,
					resizable:false,
					title:"地区添加",
					onClose:function(){
						$("#groupAddWin").window('clear');
					}
				});
				$('#groupAddWin').html(html);
				$('#groupAddWin').window('open');
				$('#groupAddWin').attr("opend",1);
			}
			$('#groupAddWin').html(html);
			$('#groupAddWin').window('open');
		};

		//添加组名
		var doAddGroup = function(){
			var group_name = $("#addGroupname").val();
			if(typeof(group_name) != 'undefined' && group_name.length > 0)
			{
				//显示loading
				$.messager.progress();
				$.ajax({
					type: "POST",
					url: "<?php echo $add_group_url;?>",
					data: "group_name="+group_name,
					success: function(msg){
						var data = eval("("+msg+")");
						//成功
						if(typeof(data.result) != 'undefined' && data.result > 0)
						{
							$('#groupAddWin').window('close');
							$("#groupAddWin").window('clear');
							//reload page
							$.messager.confirm('操作通知', '添加成功!', function(r){
								location.reload();
							});
						}
						else	//失败
						{
							$.messager.alert('操作通知','添加失败!可能重名.','error');
						}
						//影藏loading
						$.messager.progress('close');
					}
				});
			}
			else
			{
				$.messager.alert('操作通知','添加失败!','error');
			}
		};
		
		//给添加地区按钮绑定事件
		var addGroupBtnAttchClickEvent = function(){
			$("#addGroup").click( function () {groupModule.addGroup();});
		};

		//显示组列表
		var showCustomDatagrid = function(){
			$('#groupList').datagrid({
				url:"<?php echo $group_list_url;?>",
				fitColumns:true,
				rownumbers:true,
				singleSelect:true,
				toolbar: '#groupHeader',
				columns:[[
					{field:'F_id',title:'地区ID',align:'center',hidden:true},
					{field:'F_groupname',title:'客服所属地区',align:'center',width:100},
					{field:'F_edit',title:'操作',align:'center',width:100,formatter: function(value,row,index){
						return '<a href="#" id="group_edit_'+index+'">操作</a><div id="mm_'+index+'"><div data-options="iconCls:\'icon-remove\'" onClick="groupModule.deleteGroup(\''+row.F_id+'\',\''+row.F_groupname+'\');">删除</div><div data-options="iconCls:\'icon-edit\'" onClick="groupModule.editGroup(\''+row.F_id+'\',\''+row.F_groupname+'\');">修改</div></div><script type="text/javascript">$(\'#group_edit_'+index+'\').menubutton({iconCls: \'icon-edit\',menu: \'#mm_'+index+'\'});<\/script>';
					}},
				]]
			});
		};

		//return obj
		var obj = {
			showGroup:function(){showCustomDatagrid();addGroupBtnAttchClickEvent();},
			deleteGroup:function(group_id,group_name){deleteGroup(group_id,group_name);},
			editGroup:function(group_id,group_name){editGroup(group_id,group_name);},
			doEditGroup:function(group_id,group_name){doEditGroup(group_id,group_name);},
			doDeleteGroup:function(group_id){doDeleteGroup(group_id);},
			doAddGroup:function(){doAddGroup();},
			addGroup:function(){addGroup();},
		};

		//return
		return obj;

	}(jQuery);
	
	//模块调用
	groupModule.showGroup();
	</script>