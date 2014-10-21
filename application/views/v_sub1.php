<body>
	<div id="tabs" class="easyui-tabs" style="margin:0px;" fit="true" border="false">  
		<div title="<?php echo $page_title?>" style="padding:10px;" data-options="iconCls: 'icon-mini-add'" >
			<?php if(check_privilege("test")){?>
			<div id="list">子菜单一内容</div>
			<?php }?>
			<div id="list">子菜单一1内容</div>
		</div>
	</div>