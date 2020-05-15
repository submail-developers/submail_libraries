ALTER TABLE `ls_admin_user` ADD COLUMN security_code VARCHAR(10) DEFAULT NULL;
ALTER TABLE ls_article ADD COLUMN `color` VARCHAR(10) DEFAULT '' COMMENT '颜色';
ALTER TABLE ls_article ADD COLUMN `carousel` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '幻灯片';
ALTER TABLE ls_article ADD COLUMN   `remarks` VARCHAR(200) DEFAULT NULL COMMENT '审核备注';
ALTER TABLE ls_articlecate  ADD COLUMN `list_template` VARCHAR(20) DEFAULT 'articlelist' COMMENT '列表页模板';
ALTER TABLE `ls_comment`  ADD COLUMN  `jubao` INT(2) DEFAULT 0;
ALTER TABLE `ls_comment`  ADD COLUMN `status` TINYINT(1) DEFAULT 1 NULL;
INSERT INTO `ls_system` (`name`, `value`) VALUE ('watermark','a:9:{s:6:"switch";s:1:"1";s:9:"mark_type";s:1:"1";s:4:"font";s:2:"25";s:9:"font_path";s:11:"simfang.ttf";s:4:"code";s:7:"#66ccff";s:8:"position";s:1:"9";s:4:"text";s:6:"LaySNS";s:3:"img";s:23:"/public/images/logo.png";s:5:"token";s:1:"0";}');
ALTER TABLE ls_user  ADD COLUMN `qqid` VARCHAR(11) DEFAULT '' COMMENT 'QQ号';
ALTER TABLE ls_user   ADD COLUMN `freezepoint` INT(10)  DEFAULT 0 COMMENT '冻结积分';
ALTER TABLE ls_user   ADD COLUMN  `alipayid` VARCHAR(64) DEFAULT '' COMMENT '支付宝';
ALTER TABLE ls_user   ADD COLUMN `weixinpayid` VARCHAR(64) DEFAULT '' COMMENT '微信账号';
ALTER TABLE ls_user   ADD COLUMN `forget_passwd_token` VARCHAR(32) DEFAULT '' NULL;
ALTER TABLE ls_user   ADD COLUMN `forget_passwd_endtime` INT(10) DEFAULT 0 NULL;
ALTER TABLE ls_usergrade  ADD COLUMN  `type` TINYINT(1) DEFAULT 1 NULL COMMENT '1 积分用户组 2自定义组';
ALTER TABLE ls_artcomment  ADD COLUMN   `status` TINYINT(1) DEFAULT 1 NULL;
ALTER TABLE `ls_nav` ADD COLUMN `tid` INT(11) DEFAULT 0;
ALTER TABLE `ls_nav_cms` ADD COLUMN `tid` INT(11) DEFAULT 0;
ALTER TABLE `ls_nav` ADD COLUMN `description` VARCHAR(255) DEFAULT '' NULL;
ALTER TABLE `ls_nav_cms` ADD COLUMN `description` VARCHAR(255) DEFAULT '' NULL;
UPDATE `ls_system` SET `value` = '2.54' WHERE `id` = '2';

CREATE TABLE `ls_sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(8) DEFAULT NULL,
  `mobile` varchar(11) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `error_num` int(1) DEFAULT '0' COMMENT '所属帖子',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `expiry_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='短信验证码表';