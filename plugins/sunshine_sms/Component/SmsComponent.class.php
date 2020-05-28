<?php
class SmsComponent {

	static function sendCaptcha($mobile,$captcha,$openid = '') {

		include dirname(dirname(__FILE__)).'/plugin/alidayu/TopSdk.php';

		$settings_product = sunshine_huayueModuleSite::$_SET['alidayu_product'];
		$settings_product = $settings_product ? $settings_product : '快来租我';
		$content = str_replace(['${product}','${code}'],["$settings_product","$captcha"],sunshine_huayueModuleSite::$_SET['alidayu_tpl_id']);
        $content="【".sunshine_huayueModuleSite::$_SET['alidayu_sign_name']."】".$content;
		$phone = $mobile;//要发送短信的手机号码

		$gateway = "https://api.mysubmail.com/message/send";
        $data['project']    =   $template_code;
        $data['vars']   =   json_encode(array('code'=>$code));
        $data['appid']  =   sunshine_huayueModuleSite::$_SET['alidayu_ak'];
        $data['signature']  =  sunshine_huayueModuleSite::$_SET['alidayu_sk'];
        $data['to'] =   $mobile;

        $query = http_build_query($data);
        $options['http'] = array(
            'timeout' => 60,
            'method' => 'POST',
            'header' => 'Content-type:application/x-www-form-urlencoded',
            'content' => $query
        );
        $context = stream_context_create($options);
        $result = file_get_contents($gateway, false, $context);
        $output = trim($result, "\xEF\xBB\xBF");
		$result = json_decode($output, true);

		WeUtility::logging('sunshine_huayue_SmsComponent', var_export($result['msg'], true));
		if($result['status'] == 'success') {
			return true;
		}else {
			return false;
		}
	}
}
