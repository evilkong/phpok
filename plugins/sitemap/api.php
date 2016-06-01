<?php
/*****************************************************************************************
	文件： plugins/sitemap/api.php
	备注： 站点sitemap读取
	版本： 4.x
	网站： www.phpok.com
	作者： qinggan <qinggan@188.com>
	时间： 2015年01月14日 11时40分
*****************************************************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class api_sitemap extends phpok_plugin
{
	public $me;
	private $max_count = 5000;
	private $changefreq = 'daily';
	function __construct()
	{
		parent::plugin();
		$this->me = $this->plugin_info();
		$this->changefreq = $this->me['param']['changefreq'];
		$this->tpl->assign('plugin',$this->me);
	}

	private function project_all()
	{
		//读取模板目录
		$rslist = $this->model('project')->project_all($this->site['id'],'id',"status=1 AND identifier !='index'");
		if(!$rslist)
		{
			return false;
		}
		$tmplist = false;
		foreach($rslist as $key=>$value)
		{
			//标识
			if($value['identifier'] == 'index')
			{
				continue;
			}
			$tpl_1 = $this->dir_root.$this->site['tpl_id']['dir_tpl'].$value['identifier']."_index.".$this->site['tpl_id']['tpl_ext'];
			$tpl_2 = $this->dir_root.$this->site['tpl_id']['dir_tpl'].$value['identifier']."_list.".$this->site['tpl_id']['tpl_ext'];
			$tpl_3 = $this->dir_root.$this->site['tpl_id']['dir_tpl'].$value['identifier']."_content.".$this->site['tpl_id']['tpl_ext'];
			if($value['tpl_index'])
			{
				$tpl_1 = $this->dir_root.$this->site['tpl_id']['dir_tpl'].$value['tpl_index'].'.'.$this->site['tpl_id']['tpl_ext'];
			}
			if($value['tpl_list'])
			{
				$tpl_2 = $this->dir_root.$this->site['tpl_id']['dir_tpl'].$value['tpl_list'].'.'.$this->site['tpl_id']['tpl_ext'];
			}
			if($value['tpl_content'])
			{
				$tpl_3 = $this->dir_root.$this->site['tpl_id']['dir_tpl'].$value['tpl_content'].'.'.$this->site['tpl_id']['tpl_ext'];
			}
			$tmp = array('id'=>$value['id'],'title'=>$value['title'],'cate'=>$value['cate'],'module'=>$value['module']);
			$tmp['identifier'] = $value['identifier'];
			$tmp['psize'] = $value['psize'] ? $value['psize'] : $this->config['psize'];
			$tmp['is_index'] = $tmp['is_list'] = $tmp['is_content'] = false;
			if(file_exists($tpl_1) || (file_exists($tpl_2) && !$value['module']) || (file_exists($tpl_3) && !$value['module']))
			{
				$tmp['is_index'] = true;
				if($value['module'] && $value['cate'])
				{
					$tmp['is_list'] = true;
				}
			}
			if(file_exists($tpl_2) && $value['module'])
			{
				$tmp['is_list'] = true;
			}
			if(file_exists($tpl_3) && $value['module'])
			{
				$tmp['is_content'] = true;
			}
			if($tmp['is_content'] || $tmp['is_list'] || $tmp['is_index'])
			{
				$tmplist[$value['id']] = $tmp;
			}
		}
		return $tmplist;
	}

	public function baidu()
	{
		$this->google();
	}

	public function google()
	{
		$plist = $this->project_all();
		if(!$plist)
		{
			$rslist = array();
			$rslist[0] = array('url'=>$this->config['url'],'lastmod'=>date("Y-m-d",$this->time),'priority'=>'1.0');
			$this->echo_google_list($rslist);
		}
		//计算项目主题数
		$total = $this->list_total(array_keys($plist));
		if($total>$this->max_count)
		{
			//生成索引
			$rslist = array();
			$listurl = $this->url('plugin','exec','id=sitemap&exec=glist','',true);
			$rslist[0] = array('url'=>$this->url_quote($listurl),'lastmod'=>date("Y-m-d",$this->time));
			$page_total = intval($total/$this->max_count);
			if($total%$this->max_count)
			{
				$page_total++;
			}
			$listurl = $this->url('plugin','exec','id=sitemap&exec=ginfo','',true);
			for($i=1;$i<=$page_total;$i++)
			{
				$url = $listurl.'&pageid='.$i;
				$rslist[$i] = array('url'=>$this->url_quote($url),'lastmod'=>date("Y-m-d",$this->time));
			}
			$this->echo_google_index($rslist);
		}
		else
		{
			$rslist = $this->_list_url($plist);
			$rslist2 = $this->_info_url($plist,0,$this->max_count);
			if($rslist && $rslist2)
			{
				$rslist = array_merge($rslist,$rslist2);
			}
			$this->echo_google_list($rslist);
		}
	}

	public function glist()
	{
		$plist = $this->project_all();
		if(!$plist)
		{
			$rslist = array();
			$rslist[0] = array('url'=>$this->config['url'],'lastmod'=>date("Y-m-d",$this->time),'priority'=>'1.0');
			$this->echo_google_list($rslist);
		}
		$rslist = $this->_list_url($plist);
		$this->echo_google_list($rslist);
	}

	public function ginfo()
	{
		$plist = $this->project_all();
		if(!$plist)
		{
			$rslist = array();
			$rslist[0] = array('url'=>$this->config['url'],'lastmod'=>date("Y-m-d",$this->time),'priority'=>'1.0');
			$this->echo_google_list($rslist);
		}
		$pageid = $this->get('pageid','int');
		$pageid = intval($pageid);
		if(!$pageid) $pageid = 1;
		$offset = ($pageid-1) * $this->max_count;
		$rslist = $this->_info_url($plist,$offset,$this->max_count);
		$this->echo_google_list($rslist);
	}

	//取得内容网址
	private function _info_url($plist,$offset=0,$psize=50)
	{
		$tmplist = false;
		foreach($plist as $key=>$value)
		{
			if($value['is_content'])
			{
				$tmplist[$value['id']] = $value;
			}
		}
		if(!$tmplist)
		{
			return false;
		}
		$ids = array_keys($tmplist);
		$ids = implode(",",$ids);
		$sql = "SELECT id,title,identifier,dateline FROM ".$this->db->prefix."list WHERE project_id IN(".$ids.") ORDER BY id ASC LIMIT ".$offset.",".$psize;
		$tmplist = $this->db->get_all($sql);
		if(!$tmplist)
		{
			return false;
		}
		$rslist = false;
		foreach($tmplist as $key=>$value)
		{
			$tmpid = $value['identifier'] ? $value['identifier'] : $value['id'];
			$url = $this->url($tmpid,'','','www',true);
			$url = $this->url_quote($url);
			$tmp = array('url'=>$url,'lastmod'=>date("Y-m-d",$value['dateline']),'priority'=>'0.7');
			$tmp['title'] = $value['title'];
			$rslist[$key] = $tmp;
		}
		return $rslist;
	}

	//取得项目列表
	private function _list_url($plist)
	{
		$rslist = array();
		$rslist[] = array('url'=>$this->config['url'],'lastmod'=>date("Y-m-d",$this->time),'priority'=>'1.0');
		$cate_all = $this->cate_all();
		$p_lasttime = $this->last_time('project');
		$c_lasttime = $this->last_time('cate');
		foreach($plist as $key=>$value)
		{
			if($value['is_index'])
			{
				$tmp = array('lastmod'=>date("Y-m-d",$this->time),'priority'=>'0.9');
				$tmp['url'] = $this->url($value['identifier'],'','','www',true);
				$tmp['url'] = $this->url_quote($tmp['url']);
				$rslist[] = $tmp;
			}
			if($value['is_list'])
			{
				if(!$value['is_index'])
				{
					//读取项目下的分类
					$total = $this->list_total($value['id']);
					if($total && $total>$value['psize'])
					{
						$page_total = intval($total/$value['psize']);
						if($total%$value['psize'])
						{
							$page_total++;
						}
					}
					else
					{
						$page_total = 1;
					}
					for($i=1;$i<=$page_total;$i++)
					{
						$ext = $i>1 ? $this->config['pageid'].'='.$i : '';
						$url = $this->url($value['identifier'],'',$ext,'www',true);
						$url = $this->url_quote($url);
						$lastmod = $p_lasttime[$value['id']] ? $p_lasttime[$value['id']] : $this->time;
						$lastmod = date("Y-m-d",$lastmod);
						$tmp = array('url'=>$url,'lastmod'=>$lastmod);
						$tmp['priority'] = '0.8';
						$rslist[] = $tmp;
					}
				}
				if($value['cate'])
				{
					$list = array();
					
					$this->_tree($list,$cate_all,$value['cate']);
					foreach($list as $k=>$v)
					{
						$total = $this->list_total($value['id'],'cate_id='.$v['id']);
						$psize = $v['psize'] ? $v['psize'] : $value['psize'];
						if($total && $total>$value['psize'])
						{
							$page_total = intval($total/$psize);
							if($total%$psize)
							{
								$page_total++;
							}
						}
						else
						{
							$page_total = 1;
						}
						for($i=1;$i<=$page_total;$i++)
						{
							$ext = $i>1 ? $this->config['pageid'].'='.$i : '';
							$url = $this->url($value['identifier'],$v['identifier'],$ext,'www',true);
							$url = $this->url_quote($url);
							$lastmod = $c_lasttime[$v['id']] ? $c_lasttime[$v['id']] : ($p_lasttime[$value['id']] ? $p_lasttime[$value['id']] : $this->time);
							$lastmod = date("Y-m-d",$lastmod);
							$tmp = array('url'=>$url,'lastmod'=>$lastmod);
							$tmp['priority'] = '0.8';
							$rslist[] = $tmp;
						}
					}
				}
			}
		}
		return $rslist;
	}

	private function url_quote($url)
	{
		$url = str_replace("&amp;","&",$url);
		return str_replace("&","&amp;",$url);
	}

	private function list_total($ids,$condition='')
	{
		if(!$ids)
		{
			return false;
		}
		if(is_array($ids))
		{
			$ids = implode(",",$ids);
		}
		$sql = "SELECT count(id) FROM ".$this->db->prefix."list WHERE site_id='".$this->site['id']."' AND status=1 AND project_id IN(".$ids.")";
		if($condition)
		{
			$sql .= " AND ".$condition;
		}
		return $this->db->count($sql);
	}

	private function cate_all()
	{
		$sql = "SELECT c.id,c.title,c.identifier,c.psize,c.parent_id FROM ".$this->db->prefix."cate c ";
		$sql.= "LEFT JOIN ".$this->db->prefix."list l ON(c.id=l.cate_id)";
		$sql.= "WHERE c.site_id='".$this->site['id']."' AND c.status=1";
		return $this->db->get_all($sql,'id');
	}

	private function last_time($type='project')
	{
		$by = $type == 'project' ? 'project_id' : 'cate_id';
		$sql = "SELECT max(dateline) as dateline,".$by." pid FROM ".$this->db->prefix."list GROUP BY ".$by;
		$tmplist = $this->db->get_all($sql);
		if(!$tmplist)
		{
			return false;
		}
		$rslist = array();
		foreach($tmplist as $key=>$value)
		{
			$rslist[$value['pid']] = $value['dateline'];
		}
		return $rslist;
	}

	private function _tree(&$list,$catelist,$parent_id=0)
	{
		foreach($catelist as $key=>$value)
		{
			if($value['parent_id'] == $parent_id)
			{
				$list[$value['id']] = $value;
				$this->_tree($list,$catelist,$value['id']);
			}
		}
	}

	private function echo_google_index($rslist)
	{
		header('Content-Type: text/xml;');
		echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
		foreach($rslist as $key=>$value)
		{
			echo '<sitemap>'."\n";
			echo '<loc>'.$value['url'].'</loc>'."\n";
			echo '<lastmod>'.$value['lastmod'].'</lastmod>'."\n";
			echo '</sitemap>'."\n";
		}
		echo '</sitemapindex>';
		exit;
	}

	private function echo_google_list($rslist)
	{
		header('Content-Type: text/xml;');
		echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
		foreach($rslist as $key=>$value)
		{
			echo '<url>'."\n";
			echo '<loc>'.$value['url'].'</loc>'."\n";
			echo '<lastmod>'.$value['lastmod'].'</lastmod>'."\n";
			echo '<changefreq>'.$this->changefreq.'</changefreq>'."\n";
			echo '<priority>'.$value['priority'].'</priority>'."\n";
			echo '</url>'."\n";
		}
		echo '</urlset>';
		exit;
	}
}

?>