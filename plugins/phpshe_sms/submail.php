<?php

/**
 * Smsbao.php
 * 短信宝插件安装，增加密码字段
 */
include('./common.php');
$sql_set['setting_key'] = 'sms_password';
$sql_set['setting_value'] = '';
$db->pe_insert('setting',$sql_set);
echo '赛邮云插件安装成功，请删除submail.php文件';
