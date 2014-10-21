<body class="easyui-layout">
<!-- BOF,header-->
<div data-options="region:'north',split:'true',border:false" style="height:50px;line-height:50px;padding-left:5px;background:#b1c242;overflow: hidden;">
	<span style="font-size:24px;font-weight:bold;"><?php echo isset($web_title)?$web_title:'';?></span>
	<span><a href="<?php echo $login_out_url;?>" class="easyui-linkbutton" style="margin-top:10px;margin-right:5px;float:right;">登出</a></span>
</div>
<!-- EOF,header-->

<!-- BOF,left-->
<div data-options="region:'west',split:true" style="width:180px;padding1:1px;overflow:hidden;">
	<div class="easyui-accordion" fit="false" border="false">
		<?php foreach($web_left_menu as $key=>$menu){?>
		<div title="<?php echo $menu['title'];?>" style="overflow:auto;">
			<ul id="tt<?php echo $key;?>" tree="tt"></ul>
		</div>
		<?php }?>
	</div>
</div>
<!-- EOF,left-->

<!-- BOF,center-->
<div id="mainContents" data-options="region:'center',split:true" style="overflow:auto;">
	<div id="iframes">
		<iframe src="<?php echo $default_page;?>" height="100%" width="100%" frameborder="0"></iframe>
	</div>
</div>
<!-- EOF,center-->


<?php if(isset($web_left_menu)){ ?>
<script type="text/javascript" language="javascript">

/**
* 自执行:设置左侧manu data
*/
(function($){
	<?php foreach($web_left_menu as $key=>$menu){?>
	$('#tt<?php echo $key;?>').tree({
		data: <?php echo $menu['list'];?>
	});
	<?php }?>
})(jQuery);

/**
* 自执行:设置左侧manu数的选中状态,以及点击时候的动作
*/
(function($){
	$('ul[tree="tt"]').tree({
		onClick: function(node){
			$.messager.progress();
			//设置所有的树所有节点的icon为空白
			var allTree = $('ul[tree="tt"]');
			
			for(var i=0;i<allTree.length;++i)
			{
				var treeItem = allTree[i];

				var nodes = $(treeItem).tree('getRoots');

				for(var j=0;j<nodes.length;++j)
				{
					$(treeItem).tree('update', {
						target: nodes[j].target,
						iconCls: 'icon-blank'
					});
				}
			}
			//设置当前选中的节点的icon为选中图标
			$(this).tree('update', {
				target: node.target,
				iconCls: 'icon-mini-add'
			});
			//打开窗口
			var iframe = document.createElement("iframe");
			$(iframe).attr("width","100%");
			$(iframe).attr("height","100%");
			$(iframe).attr("frameborder",0);
			$(iframe).attr("src",node.attributes.url);

			if (iframe.attachEvent){
				iframe.attachEvent("onload", function(){
					$.messager.progress('close');
				});
			} else {
				iframe.onload = function(){
					$.messager.progress('close');
				};
			}
			$("#iframes").html(iframe);
		}
	});
})(jQuery);

</script>
<?php } ?>