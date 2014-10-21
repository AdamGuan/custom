<body>
	<script type="text/javascript" src="<?php echo get_js_url('raphael-min.js');?>"></script>
	<script type="text/javascript" src="<?php echo get_js_url('elycharts.min.js');?>"></script>

	<div id="tabs" class="easyui-tabs" style="margin:0px;" fit="true" border="false">  
		<div title="<?php echo $page_title?>" style="padding:10px;" data-options="iconCls: 'icon-mini-add'" >
			<div id="control">
				<input id="group" name="group" value="<?php echo $group;?>">
				<input id="type" name="type" value="<?php echo $type;?>">
				<span id="btn"></span>
			</div>
			<div id="chart"></div>
		</div>
	</div>
	
	

	<script type="text/javascript">
	
	//模块定义 
	var studentAskModule = function($){

		var setBtn = function(){
			$("#btn").data("btnNum", 0);
			var btnhtml = '<a id="prev" href="javascript:studentAskModule.prevBtnClick();">上周</a><script>$(\'#prev\').linkbutton({});<\/script>';
			$("#btn").append(btnhtml);
		};

		var prevBtnClick = function(){
			var offset = eval($("#btn").data("btnNum")-1);
			var groupid = $("#group").combobox('getValue');
			var typeid = $("#type").combobox('getValue');
			
			//显示loading
			$.messager.progress();
			$.ajax({
				type: "POST",
				url: "<?php echo $student_ask_data_url;?>",
				data: "groupid="+groupid+"&typeid="+typeid+"&offset="+offset,
				success: function(msg){
					var data = eval("("+msg+")");
					if(typeof(data.labels) != "undefined")
					{
						$("#btn").empty();
						$("#btn").data("btnNum",offset);
						var prevText = '上周';
						var nextText = '下周';
						if(typeid == 2)
						{
							prevText = '上月';
							nextText = '下月';
						}
						var btnhtml = '<a id="prev" href="javascript:studentAskModule.prevBtnClick();">'+prevText+'</a>&nbsp;<a id="next" href="javascript:studentAskModule.nextBtnClick();">'+nextText+'</a><script>$(\'#prev\').linkbutton({});$(\'#next\').linkbutton({});<\/script>';
						$("#btn").append(btnhtml);
						studentAskModule.rechart(data.labels,data.tooltips,data.values);
					}
					else
					{
						$.messager.alert('操作通知','获取数据失败!','error');
					}
					//影藏loading
					$.messager.progress('close');
				}
			});
		};

		var nextBtnClick = function(){
			var offset = eval($("#btn").data("btnNum")+1);
			if(offset > 0)
			{
				offset = 0;
			}
			var groupid = $("#group").combobox('getValue');
			var typeid = $("#type").combobox('getValue');
			
			//显示loading
			$.messager.progress();
			$.ajax({
				type: "POST",
				url: "<?php echo $student_ask_data_url;?>",
				data: "groupid="+groupid+"&typeid="+typeid+"&offset="+offset,
				success: function(msg){
					var data = eval("("+msg+")");
					if(typeof(data.labels) != "undefined")
					{
						$("#btn").empty();
						$("#btn").data("btnNum",offset);
						var prevText = '上周';
						var nextText = '下周';
						if(typeid == 2)
						{
							prevText = '上月';
							nextText = '下月';
						}
						if(offset >= 0)
						{
							var btnhtml = '<a id="prev" href="javascript:studentAskModule.prevBtnClick();">'+prevText+'</a><script>$(\'#prev\').linkbutton({});<\/script>';
						}
						else
						{
							var btnhtml = '<a id="prev" href="javascript:studentAskModule.prevBtnClick();">'+prevText+'</a>&nbsp;<a id="next" href="javascript:studentAskModule.nextBtnClick();">'+nextText+'</a><script>$(\'#prev\').linkbutton({});$(\'#next\').linkbutton({});<\/script>';
						}
						
						$("#btn").append(btnhtml);
						studentAskModule.rechart(data.labels,data.tooltips,data.values);
					}
					else
					{
						$.messager.alert('操作通知','获取数据失败!','error');
					}
					//影藏loading
					$.messager.progress('close');
				}
			});
		};

		var setGroup = function(){
			$('#group').combobox({
				data:<?php echo $group_list_json;?>,
				valueField:'id',
				textField:'text',
				onSelect: function(rec){
					//
					var groupid = rec.id;
					var typeid = $("#type").combobox('getValue');
					var offset = $("#btn").data("btnNum");
					
					//显示loading
					$.messager.progress();
					$.ajax({
						type: "POST",
						url: "<?php echo $student_ask_data_url;?>",
						data: "groupid="+groupid+"&typeid="+typeid+"&offset="+offset,
						success: function(msg){
							var data = eval("("+msg+")");
							if(typeof(data.labels) != "undefined")
							{
								studentAskModule.rechart(data.labels,data.tooltips,data.values);
							}
							else
							{
								$.messager.alert('操作通知','获取数据失败!','error');
							}
							//影藏loading
							$.messager.progress('close');
						}
					});
					
				}
			});
		};

		var setType = function(){
			$('#type').combobox({
				data:<?php echo $type_list_json;?>,
				valueField:'id',
				textField:'text',
				onSelect: function(rec){
					//
					var typeid = rec.id;
					var groupid = $("#group").combobox('getValue');
					var offset = $("#btn").data("btnNum");
					
					//显示loading
					$.messager.progress();
					$.ajax({
						type: "POST",
						url: "<?php echo $student_ask_data_url;?>",
						data: "groupid="+groupid+"&typeid="+typeid+"&offset="+offset,
						success: function(msg){
							var data = eval("("+msg+")");
							if(typeof(data.labels) != "undefined")
							{
								var prevText = '上周';
								var nextText = '下周';
								if(typeid == 2)
								{
									prevText = '上月';
									nextText = '下月';
								}

								if(offset >= 0)
								{
									var btnhtml = '<a id="prev" href="javascript:studentAskModule.prevBtnClick();">'+prevText+'</a><script>$(\'#prev\').linkbutton({});<\/script>';
								}
								else if(offset < 0)
								{
									var btnhtml = '<a id="prev" href="javascript:studentAskModule.prevBtnClick();">'+prevText+'</a>&nbsp;<a id="next" href="javascript:studentAskModule.nextBtnClick();">'+nextText+'</a><script>$(\'#prev\').linkbutton({});$(\'#next\').linkbutton({});<\/script>';
								}
								$("#btn").empty();
								$("#btn").append(btnhtml);

								studentAskModule.rechart(data.labels,data.tooltips,data.values);
							}
							else
							{
								$.messager.alert('操作通知','获取数据失败!','error');
							}
							//影藏loading
							$.messager.progress('close');
						}
					});
					
				}
			});
		};

		var chartTemplates = function(){
			$.elycharts.templates['line_basic'] = {
				type : "line",
				margins : [20, 40, 20, 40],
				defaultSeries : {
					plotProps : {
						"stroke-width" : 4
					},
					dot : true,
					dotProps : {
						stroke : "white",
						"stroke-width" : 2
					}
				},
				series : {
					serie1 : {
						color : "green"
					},
					serie2 : {
						color : "red"
					}
				},
				defaultAxis : {
					labels : true
				},
				features : {
					grid : {
						draw : [true, true],
						props : {
							"stroke-dasharray" : "-"
						}
					},
					legend : {
						horizontal : false,
						dotType : "circle",
						dotProps : {
						stroke : "white",
							"stroke-width" : 2
						},
						borderProps : {
							opacity : 0.5,
							fill : "#c0c0c0",
							"stroke-width" : 0
						}
					}
				}
			}
		};

		var chart = function(){
			<?php if($total > 0){?>
			$("#chart").chart({
				template : "line_basic",
				labels : <?php echo $labels;?>,
				tooltips : {
					serie1 : <?php echo $tooltips;?>
				},
				values : {
					serie1 : <?php echo $values;?>
				},
				defaultSeries : {
					fill : true,
					stacked : false,
					highlight : {
						scale : 2
					},
					startAnimation : {
						active : true,
						type : "grow",
						easing : "bounce"
					}
				}
			});
			<?php }?>
		};

		var rechart = function(labels,tooltips,values){
			$("#chart").remove();
			$("#control").after("<div id='chart'></div>");
			
			studentAskModule.chartTemplates();
			$("#chart").chart({
				template : "line_basic",
				labels : labels,
				tooltips : {
					serie1 : tooltips
				},
				values : {
					serie1 : values
				},
				defaultSeries : {
					fill : true,
					stacked : false,
					highlight : {
						scale : 2
					},
					startAnimation : {
						active : true,
						type : "grow",
						easing : "bounce"
					}
				}
			});
		};

		//return obj
		var obj = {
			start:function(){
				setGroup();
				setType();
				setBtn();
				chartTemplates();
				chart();
			},
			rechart:function(labels,tooltips,values){rechart(labels,tooltips,values);},
			chartTemplates:function(){chartTemplates();},
			prevBtnClick:function(){prevBtnClick();},
			nextBtnClick:function(){nextBtnClick();},
		};

		//return
		return obj;

	}(jQuery);
	
	//模块调用
	studentAskModule.start();
	</script>