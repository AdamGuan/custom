	<div id="showUserChatList">

	<div id="userChatListWin">
		<div id="userChatListHeader" style="text-align: center;"><?php echo '用户: '.$user_name.' 发给 客服: '.$custom_name;?> - 会话记录</div>
			
			<table id="userChatList"></table>
	</div>

	<script type="text/javascript">
	
	//模块定义 
	var showUserChatListModule = function($){
		
		//显示chat列表
		var showUserChatListDatagrid = function(){
			$('#userChatList').datagrid({
				url:"<?php echo $student_to_custom_chat_list_url;?>",
				fitColumns:true,
				rownumbers:true,
				singleSelect:true,
				toolbar: '#userChatListHeader',
				pagination: true,
				pageSize: 10,
				pageList: [10,20,30,40,50],
				columns:[[
					{field:'F_timestamp',title:'咨询时间',align:'center',width:100},
					{field:'F_msg',title:'会话内容',align:'center',width:100}
				]]
			});
			
		};
		
		var showUserChatListWin = function(){
			$('#userChatListWin').window({
//				width:300,
//				height:300,
				fit:true,
				modal:true,
				collapsible:false,
				minimizable:false,
				resizable:false,
				title:"会话记录",
				onClose:function(){
					$("#userChatListWin").window('clear');
					$("#userChatListWin").window('destroy',true);
					$("#showUserChatList").remove();
				}
			});
			$('#userChatListWin').window('open');
		};
		

		//return obj
		var obj = {
			doit:function(){
				showUserChatListDatagrid();
				showUserChatListWin();
			}
		};

		//return
		return obj;

	}(jQuery);
	
	//模块调用
	showUserChatListModule.doit();
	</script>
	</div>
