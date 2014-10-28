<?php if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");} ?><?php $title="数据库比较工具";?><?php $this->assign("title","数据库比较工具"); ?><?php $this->output("head","file"); ?>
<div class="tips">
	您当前位置：<a href="<?php echo phpok_url(array('ctrl'=>'sqldiff'));?>">数据库比较</a>
	&raquo; 配置要比较的数据库
</div>
<script type="text/javascript">
function check_it()
{
	var host1 = $("#host1").val();
	var port1 = $("#port1").val();
	var user1 = $("#user1").val();
	var data1 = $("#data1").val();
	if(!host1 || !port1 || !user1 || !data1)
	{
		$.dialog.alert('主数据库信息配置不完整！','','error');
		return false;
	}
	var host2 = $("#host2").val();
	var port2 = $("#port2").val();
	var user2 = $("#user2").val();
	var data2 = $("#data2").val();
	if(!host2 || !port2 || !user2 || !data2)
	{
		$.dialog.alert('副数据库信息配置不完整！','','error');
		return false;
	}
	return true;	
}
</script>
<form method="post" action="<?php echo phpok_url(array('ctrl'=>'plugin','func'=>'exec','id'=>$plugin['id'],'exec'=>'rs'));?>" onsubmit="return check_it();">
<table cellspacing="0" width="100%">
<tr>
	
	<td width="50%" valign="top">
		<div class="tips">主数据库</div>
		<div class="table">
			<div class="title">
				数据库引挈：
				<select name="engine1">
					<?php $rslist_id["num"] = 0;$rslist=is_array($rslist) ? $rslist : array();$rslist_id["total"] = count($rslist);$rslist_id["index"] = -1;foreach($rslist AS $key=>$value){ $rslist_id["num"]++;$rslist_id["index"]++; ?>
					<option value="<?php echo $value;?>"><?php echo $value;?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="table">
			<div class="title">
				数据库服务器：
				<span class="note">设置数据库服务器，如果要比较本地数据库，请使用localhost或127.0.0.1</span>
			</div>
			<div class="content">
				<input type="text" id="host1" name="host1" class="default" value="localhost" />
			</div>
		</div>
		<div class="table">
			<div class="title">
				端口：
				<span class="note">设置MySQL的端口号，默认是3306</span>
			</div>
			<div class="content">
				<input type="text" id="port1" name="port1" class="short" value="3306" />
			</div>
		</div>
		<div class="table">
			<div class="title">
				数据库账号：
				<span class="note">设置数据库的账号，本地常常使用root</span>
			</div>
			<div class="content">
				<input type="text" id="user1" name="user1" class="default" value="" />
			</div>
		</div>
		<div class="table">
			<div class="title">
				数据库密码：
				<span class="note"></span>
			</div>
			<div class="content">
				<input type="text" id="pass1" name="pass1" class="default" value="" />
			</div>
		</div>
		<div class="table">
			<div class="title">
				数据库名：
				<span class="note"></span>
			</div>
			<div class="content">
				<input type="text" id="data1" name="data1" class="default" value="" />
			</div>
		</div>
	</td>
	<td valign="top">
		<div class="tips">副数据库</div>
		<div class="table">
			<div class="title">
				数据库引挈：
				<select name="engine2">
					<?php $rslist_id["num"] = 0;$rslist=is_array($rslist) ? $rslist : array();$rslist_id["total"] = count($rslist);$rslist_id["index"] = -1;foreach($rslist AS $key=>$value){ $rslist_id["num"]++;$rslist_id["index"]++; ?>
					<option value="<?php echo $value;?>"><?php echo $value;?></option>
					<?php } ?>
				</select>
			</div>
		</div>

		<div class="table">
			<div class="title">
				数据库服务器：
				<span class="note">设置数据库服务器，如果要比较本地数据库，请使用localhost或127.0.0.1</span>
			</div>
			<div class="content">
				<input type="text" id="host2" name="host2" class="default" value="localhost" />
			</div>
		</div>
		<div class="table">
			<div class="title">
				端口：
				<span class="note">设置MySQL的端口号，默认是3306</span>
			</div>
			<div class="content">
				<input type="text" id="port2" name="port2" class="short" value="3306" />
			</div>
		</div>
		<div class="table">
			<div class="title">
				数据库账号：
				<span class="note">设置数据库的账号，本地常常使用root</span>
			</div>
			<div class="content">
				<input type="text" id="user2" name="user2" class="default" value="" />
			</div>
		</div>
		<div class="table">
			<div class="title">
				数据库密码：
				<span class="note"></span>
			</div>
			<div class="content">
				<input type="text" id="pass2" name="pass2" class="default" value="" />
			</div>
		</div>
		<div class="table">
			<div class="title">
				数据库名：
				<span class="note"></span>
			</div>
			<div class="content">
				<input type="text" id="data2" name="data2" class="default" value="" />
			</div>
		</div>
	</td>
</tr>
</table>
<div class="table">
	<div class="title">
		属性：
		<span class="note"></span>
	</div>
	<div class="content">
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td><label><input type="checkbox" name="onlymain" checked /> 只比较主要数据库</label></td>
			<td>&nbsp; &nbsp;</td>
			<td><label><input type="checkbox" name="onlyerror" checked /> 只显示不一致的字段</label></td>
		</tr>
		</table>
	</div>
</div>
<div class="table">
	<div class="content">
		<input type="submit" value="开始比较" class="submit" />
	</div>
</div>
</form>
<div class="tips">
	<ul>
		<li>本程序仅用于比较副数据库表结构与主数据库之间的差异，并不执行任何其他操作！</li>
		<li>此工具暂时仅测试MySQL数据库，其他类型数据库未测试</li>
		<li>仅用于比较UTF-8编码的数据库，其他语言编码数据库未测试</li>
		<li>表名，字段的比较是不区分大小写</li>
	</ul>
</div>
<?php $this->output("foot","file"); ?>