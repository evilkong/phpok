<?php
/*****************************************************************************************
	文件： {phpok}/model/www/ext_model.php
	备注： 前端扩展字段内容读取及格式化
	版本： 4.x
	网站： www.phpok.com
	作者： qinggan <qinggan@188.com>
	时间： 2014年11月05日 10时57分
*****************************************************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class ext_model extends ext_model_base
{
	private $url_type = 'default';
	function __construct()
	{
		parent::__construct();
	}

	//读取分类下的全部扩展
	function cate()
	{
		$sql = "SELECT * FROM ".$this->db->prefix."ext WHERE module LIKE 'cate-%' ORDER BY taxis ASC,id DESC";
		$rslist = $this->db->get_all($sql,'id');
		if(!$rslist)
		{
			return false;
		}
		//读取分类扩展对应的值
		$ids = array_keys($rslist);
		$sql = "SELECT * FROM ".$this->db->prefix."extc WHERE id IN(".implode(",",$ids).")";
		$clist = $this->db->get_all($sql);
		if($clist)
		{
			foreach($clist as $key=>$value)
			{
				$rslist[$value['id']]['content'] = $value['content'];
			}
		}
		$rslist = $this->_format($rslist);
		$tmplist = false;
		foreach($rslist as $key=>$value)
		{
			if($value['form_type'] == 'url' && $value['content'])
			{
				$tmplist['_url'] = $value['content'];
			}
			if($value['form_type'] == 'editor')
			{
				$value['content'] = $this->lib('ubb')->to_html($value['content'],false);
			}
			$tmplist[$value['module']][$value['identifier']] = $value['content'];
		}
		return $tmplist;
	}

	//格式化内容
	private function _format($rslist)
	{
		if(!$rslist)
		{
			return false;
		}
		$list = false;
		foreach($rslist as $key=>$value)
		{
			$value['ext'] = $value['ext'] ? unserialize($value['ext']) : array();
			if($value['form_type'] == 'upload' && $value['content'] && trim($value['content']))
			{
				$tmp = explode(",",trim($value['content']));
				foreach($tmp as $k=>$v)
				{
					if($v && trim($v))
					{
						$list['res'][] = $v;
					}
				}
			}
			if($value['form_type'] == 'title' && $value['content'] && trim($value['content']))
			{
				$tmp = explode(",",trim($value['content']));
				foreach($tmp as $k=>$v)
				{
					if($v && trim($v))
					{
						$list['title'][] = $v;
					}
				}
			}
			//格式化URL
			if($value['form_type'] == 'url' && $value['content'] && trim($value['content']))
			{
				$tmp = unserialize($value['content']);
				$rslist[$key]['content'] = $this->site['url_type'] == 'rewrite' ? $tmp['rewrite'] : $tmp['default'];
			}
			$rslist[$key] = $value;
		}
		if($list['res'])
		{
			$list['res'] = array_unique($list['res']);
			$ids = implode(",",$list['res']);
			$sql = "SELECT * FROM ".$this->db->prefix."res WHERE id IN(".$ids.")";
			$reslist = $this->db->get_all($sql,'id');
			if($reslist)
			{
				$sql = "SELECT ext.res_id,ext.gd_id,ext.filename,gd.identifier FROM ".$this->db->prefix."res_ext ext ";
				$sql.= "LEFT JOIN ".$this->db->prefix."gd gd ON(ext.gd_id=gd.id) ";
				$sql.= "WHERE ext.res_id IN(".$ids.")";
				$elist = $this->db->get_all($sql);
				if($elist)
				{
					foreach($elist as $key=>$value)
					{
						$reslist[$value['res_id']]['gd'][$value['identifier']] = $value['filename'];
					}
				}
			}
			$list['res'] = $reslist;
		}
		if($list['title'])
		{
			$list['title'] = array_unique($list['title']);
			$ids = implode(",",$list['title']);
			$sql = "SELECT * FROM ".$this->db->prefix."list WHERE id IN(".$ids.") AND status=1";
			$list['title'] = $this->db->get_all($sql,'id');
		}
		foreach($rslist as $key=>$value)
		{
			if($value['form_type'] == 'upload' && $value['content'])
			{
				if($value['ext']['is_multiple'])
				{
					$tmp = explode(',',trim($value['content']));
					$tmplist = false;
					foreach($tmp as $k=>$v)
					{
						if($v && trim($v) && $list['res'][$v])
						{
							$tmplist[] = $list['res'][$v];
						}
					}
					$value['content'] = $tmplist;
				}
				else
				{
					if($list['res'][$value['content']])
					{
						$value['content'] = $list['res'][$value['content']];
					}
				}
			}
			if($value['form_type'] == 'title')
			{
				if($value['ext']['is_multiple'])
				{
					$tmp = explode(',',trim($value['content']));
					$tmplist = false;
					foreach($tmp as $k=>$v)
					{
						if($v && trim($v) && $list['title'][$v])
						{
							$tmplist[] = $list['title'][$v];
						}
					}
					$value['content'] = $tmplist;
				}
				else
				{
					if($list['res'][$value['content']])
					{
						$value['content'] = $list['title'][$value['content']];
					}
				}
			}
			$rslist[$key] = $value;
		}
		return $rslist;
	}
}

?>