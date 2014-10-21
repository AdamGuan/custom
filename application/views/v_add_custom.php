	<div id="showAddCustom">

	<div id="win">
		<div>
			<input type="text" id="customName" name="customName" value="" />
		</div>
		<div>
			<input type="text" id="customPwd" name="customPwd" value="<?php echo $custom_pwd?>" />
		</div>
		<div>
			<input id="group" name="group" value="">
		</div>
		<a id="customSave" href="#">保存</a>
	</div>

	<script type="text/javascript">
	
	//模块定义 
	var showAddCustomModule = function($){

		var showAddCustom = function(){
			$('#win').window({
				width:300,
				height:300,
				modal:true,
				collapsible:false,
				minimizable:false,
				resizable:false,
				title:"添加客服",
				onClose:function(){
					$("#win").window('clear');
					$("#win").window('destroy',true);
					$("#showAddCustom").remove();
				}
			});
			$('#win').window('open');
		};

		var showGroup = function(){
			$('#group').combobox({
				prompt:'所属地区',
				valueField:'F_id',
				textField:'F_groupname',
				data:<?php echo $group_list_json;?>,
			});
		};

		var setInputStyle = function(){
			$('#customName').textbox({
				iconCls:'icon-man',
				iconAlign:'right',
				prompt:'账号',
			});

			$('#customPwd').textbox({
				iconCls:'icon-lock',
				iconAlign:'right',
				prompt:'密码',
				readonly:true,
			});

			$('#customSave').linkbutton({
				iconCls: 'icon-save'
			});
		};

		var customeSave = function(){
			$("#customSave").click(function(){ 
				//显示loading
				$.messager.progress();
				var name = $("#customName").val();
				var pwd = $("#customPwd").val();
				var groupid = $('#group').combobox('getValue');
				if(name.length > 0 && pwd.length > 0 && typeof(groupid) != "undefined" && groupid.length > 0)
				{
					var url = "<?php echo $add_custom_url;?>";
					$.ajax({
						type: "POST",
						url: url,
						data: "name="+name+"&pwd="+pwd+"&groupid="+groupid,
						success: function(msg){
							//影藏loading
							$.messager.progress('close');
							var data = eval("("+msg+")");
							//添加成功
							if(typeof(data.result) != "undefined" && data.result == 1)
							{
								//关闭添加窗口
								$("#win").window('close');
								//reload page
								$.messager.confirm('操作通知', '添加客服:"'+name+'"成功！', function(r){
									location.reload();
								});
							}
							else
							{
								//弹出失败对话框
								$.messager.alert('操作通知',"添加失败!\n可能重名!",'error');
							}
						}
					});
				}
				else
				{
					//影藏loading
					$.messager.progress('close');
					//显示错误
					$.messager.alert('操作通知',"添加失败!\n信息必须全!",'error');
				}
			});
		};

		//return obj
		var obj = {
			doit:function(){
				showAddCustom();
				showGroup();
				setInputStyle();
				customeSave();
			}
		};

		//return
		return obj;

	}(jQuery);
	
	//模块调用
	showAddCustomModule.doit();
	</script>
	</div>