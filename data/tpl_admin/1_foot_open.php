<?php if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");} ?><?php echo $GLOBALS["app"]->plugin_html_ap("body");?>
<?php echo $GLOBALS["app"]->plugin_html_ap("foot");?>
<script type="text/javascript">
$(document).ready(function(){
	//自定义右键
	//var debug
	var r_menu = [[{
		'text':'刷新内容',
		'func':function(){
			$.phpok.reload();
		}
	},{
		'text':'网页属性',
		'func':function(){
			var url = window.location.href;
			top.$.dialog({
				'title':'网址属性',
				'content':'网址：'+url+'<br /><div style="text-indent:36px"><a href="'+url+'" target="_blank" class="red">新窗口打开</a></div>',
				'lock':true,
				'drag':false,
				'fixed':true
			});
		}
	}]];
	$(window).smartMenu(r_menu);
});
</script>

</body>
</html>