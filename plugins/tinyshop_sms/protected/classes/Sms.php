<?php

/**
 * Created by PhpStorm.
 * User: Thinkpad
 * Date: 2019/6/4
 * Time: 16:42
 */
class Sms
{
    private $option = array();

    public function __construct() {
        $this->init();
    }

    public function init() {
        $config = Config::getInstance();
        $sms = $config->get('sms');
        $this->option = $sms;
    }


    public function send_sms($phone, $body, $senderInfo = '' ){
        $smsapi = "https://api.mysubmail.com/message/send/";
        $sing = $this->option['sms_sing'];
        $data['content'] = urlencode('【'.$sing.'】'.$body);
        $data['appid']  =   $this->option['sms_account'];
        $data['sign_type']  =  "normal";
        $data['signature']  =   $this->option['sms_password'];
        $data['to'] =   trim($phone);
		$query = http_build_query($data);
		$options['http'] = array(
			'timeout' => 60,
			'method' => 'POST',
			'header' => 'Content-type:application/x-www-form-urlencoded',
			'content' => $query
		);
		$context = stream_context_create($options);
		$result = file_get_contents($smsapi, false, $context);
       echo $result;
    }

}