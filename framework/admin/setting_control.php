<?php
/***********************************************************
	Filename: admin/setting_control.php
	Note	: 设置中心页
	Version : 4.0
	Web		: www.phpok.com
	Author  : qinggan <qinggan@188.com>
	Update  : 2012-10-31 22:29
***********************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class setting_control extends phpok_control
{
	function __construct()
	{
		parent::control();
	}

	function index_f()
	{
		$this->view("index");
	}
}
?>