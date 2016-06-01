CREATE TABLE IF NOT EXISTS `qinggan_fav` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '����ID',
  `user_id` int(10) unsigned NOT NULL COMMENT '��ԱID',
  `thumb` varchar(255) NOT NULL COMMENT '����ͼ',
  `title` varchar(255) NOT NULL COMMENT '����',
  `note` varchar(255) NOT NULL COMMENT 'ժҪ',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '���ʱ��',
  `lid` int(11) NOT NULL COMMENT '��������',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='��Ա�ղؼ�' AUTO_INCREMENT=1 ;


ALTER TABLE  `qinggan_site` DROP  `email_charset` ,
DROP  `email_server` ,
DROP  `email_port` ,
DROP  `email_ssl` ,
DROP  `email_account` ,
DROP  `email_pass` ,
DROP  `email_name` ,
DROP  `email` ,
DROP  `biz_billing` ,
DROP  `html_root_dir` ,
DROP  `html_content_type` ,
DROP  `biz_etpl` ;

ALTER TABLE  `qinggan_adm` ADD  `mobile` INT( 255 ) NOT NULL COMMENT  '�ֻ���';

ALTER TABLE  `qinggan_module_fields` ADD  `search` TINYINT( 1 ) NOT NULL DEFAULT  '0' COMMENT  '0��֧������1��ȫƥ������2ģ��ƥ������3��������',
ADD  `search_separator` VARCHAR( 10 ) NOT NULL COMMENT  '�ָ����������������ʱ��Ч';


ALTER TABLE  `qinggan_phpok` ADD  `is_api` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '0��֧��API���ã�1֧��',
ADD  `sqlinfo` TEXT NOT NULL COMMENT  'SQL���';

ALTER TABLE  `qinggan_payment_group` ADD  `is_wap` TINYINT( 1 ) NOT NULL DEFAULT  '0' COMMENT  '0��PC�ˣ�1���ֻ���'