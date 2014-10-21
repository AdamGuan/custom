	<div id="showCustomChatList">

	<div id="customChatListWin">
		<div id="customChatListHeader" style="text-align: center;"><?php echo "客服: ".$custom_name;?> - 会话记录</div>
			
			<table id="customChatList"></table>
	</div>

	<script type="text/javascript">
	
	//模块定义 
	var showCustomChatListModule = function($){

		//显示用户聊天列表窗口
		var showUserChatList = function(user_name,custom_name,userid){
			//显示loading
			$.messager.progress();
			$.ajax({
				type: "POST",
				url: "<?php echo $user_chat_list_url;?>",
				data: "user_name="+user_name+"&custom_name="+custom_name+"&userid="+userid,
				success: function(msg){
					$("body").append(msg);
					//影藏loading
					$.messager.progress('close');
				}
			});
		};
		
		//显示客服列表
		var showCustomChatListDatagrid = function(){
			
			$('#customChatList').datagrid({
				url:"<?php echo $custom_chat_list_url;?>?custom_name=<?php echo $custom_name;?>",
				fitColumns:true,
				rownumbers:true,
				singleSelect:true,
				toolbar: '#customChatListHeader',
				pagination: true,
				pageSize: 10,
				pageList: [10,20,30,40,50],
				columns:[[
					{field:'username',title:'用户名',align:'center',width:100},
					{field:'timestamp',title:'咨询时间',align:'center',width:100},
					{field:'msg',title:'会话内容',align:'center',width:100},
					{field:'status',title:'回复状态',align:'center',width:100},
					{field:'action',title:'全部记录查看',align:'center',width:100,formatter: function(value,row,index){
						return '<a id="look_user_chats_'+index+'" href="javascript:showCustomChatListModule.showUserChatList(\''+row.username+'\',\'<?php echo $custom_name;?>\',\''+row.userid+'\');"></a><script type="text/javascript">$(\'#look_user_chats_'+index+'\').linkbutton({iconCls: \'icon-search\'});<\/script>';
					}}
				]]
			});
			
		};
		
		var showCustomChatListWin = function(){
			$('#customChatListWin').window({
//				width:300,
//				height:300,
				fit:true,
				modal:true,
				collapsible:false,
				minimizable:false,
				resizable:false,
				title:"会话记录",
				onClose:function(){
					$("#customChatListWin").window('clear');
					$("#customChatListWin").window('destroy',true);
					$("#showCustomChatList").remove();
				}
			});
			$('#customChatListWin').window('open');
		};
		

		//return obj
		var obj = {
			doit:function(){
				showCustomChatListDatagrid();
				showCustomChatListWin();
			},
			showUserChatList:function(user_name,custom_name,userid){showUserChatList(user_name,custom_name,userid);}
		};

		//return
		return obj;

	}(jQuery);
	
	//模块调用
	showCustomChatListModule.doit();
	</script>
	</div>
