<body>
	<div id="tabs" class="easyui-tabs" style="margin:0px;" fit="true" border="false">  
		<div title="<?php echo $page_title?>" style="padding:10px;" data-options="iconCls: 'icon-mini-add'" >
			<div id="scoreHeader"><a id="delScores" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-remove'">批量删除评价</a></div>
			<table id="customScoreList"></table>
		</div>
	</div>

	<script type="text/javascript">
	
	//模块定义 
	var customScoreModule = function($){
		
		//删除评价
		var deleteScore = function(id){
			$.messager.confirm('删除提示', '确定删除', function(r){
				if (r){
					//显示loading
					$.messager.progress();
					$.ajax({
						type: "POST",
						url: "<?php echo $score_delete_url;?>",
						data: "id="+id,
						success: function(msg){
							var data = eval("("+msg+")");
							if(typeof(data.result) != "undefined" && data.result == 1)
							{
								$('#customScoreList').datagrid('reload');
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

		//绑定批量删除评价按钮的事件
		var bindDeleteScores = function(){
			$("#delScores").click(
				function(){
					var selectedObjs = $("input:checked[name^='del_scores_']");
					if(selectedObjs.length > 0)
					{
						var idList = [];
						for(var i=0;i<selectedObjs.length;++i)
						{
							idList[i] = $(selectedObjs[i]).attr("value");
						}
						var ids = idList.join(",");
						deleteScore(ids);
					}
					else
					{
						$.messager.alert('操作通知','没有选择!','warn');
					}
				}
			);
		};

		var bindSelectAllScore = function(){
			$("#select_all_del_scores").click(function(){
				var selected = $("#select_all_del_scores").data("selected");
				if(typeof(selected) == 'undefined' || selected == 0)
				{
					$("input[name='select_all_del_scores']").prop("checked",true);
					$("input[name^='del_scores_']").prop("checked",true);
					$("#select_all_del_scores").data("selected",1);
				}
				else
				{
					$("input[name='select_all_del_scores']").prop("checked",false);
					$("input[name^='del_scores_']").prop("checked",false); 
					$("#select_all_del_scores").data("selected",0);
				}
			});
		};
		
		//显示评价列表
		var showCustomScoreDatagrid = function(){
			$('#customScoreList').datagrid({
				url:"<?php echo $score_list_url;?>",
				fitColumns:true,
				rownumbers:true,
				singleSelect:true,
				toolbar: '#scoreHeader',
				pagination: true,
				pageSize: 10,
				pageList: [10,20,30,40,50],
				onLoadSuccess: function(data){
					bindSelectAllScore();
				},
				columns:[[
					{field:'F_id',title:'<input type="checkbox" id="select_all_del_scores" name="select_all_del_scores">',align:'center',formatter: function(value,row,index){
						return '<input type="checkbox" name="del_scores_'+index+'" value="'+value+'">';
					}},
					{field:'t_student_name',title:'用户名',align:'center',width:100},
					{field:'F_createtime',title:'评价时间',align:'center',width:100},
					{field:'F_to',title:'评价对象',align:'center',width:100},
					{field:'F_score',title:'满意度',align:'center',width:100},
					{field:'F_content',title:'评价内容',align:'center',width:100},
					{field:'action',title:'删除',align:'center',width:100,formatter: function(value,row,index){
						return '<a id="del_score_'+index+'" href="javascript:customScoreModule.deleteScore(\''+row.F_id+'\');"></a><script type="text/javascript">$(\'#del_score_'+index+'\').linkbutton({iconCls: \'icon-remove\'});<\/script>';

					}},
				]]
			});
		};

		//return obj
		var obj = {
			showCustomScoreDatagrid:function(){showCustomScoreDatagrid();bindDeleteScores();},
			deleteScore:function(id){deleteScore(id);},
		};

		//return
		return obj;

	}(jQuery);
	
	//模块调用
	customScoreModule.showCustomScoreDatagrid();
	</script>