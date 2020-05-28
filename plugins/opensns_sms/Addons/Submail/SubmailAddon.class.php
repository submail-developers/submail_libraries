<?php

namespace Addons\Submail;

use Common\Controller\Addon;

/**
 * 赛邮云通信短信插件
 */
class SubmailAddon extends Addon
{
    public $info = array(
        'name' => 'Submail',
        'title' => '赛邮云通信',
        'description' => '赛邮云通信短信插件 https://www.mysubmail.com/',
        'status' => 1,
        'author' => 'submail-dev',
        'version' => '1.0.0'
    );

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    public function sms()
    {
        return true;
    }

    public function sendSms($mobile, $content)
    {
        $config = get_addon_config('Submail');
        $cont = explode('【', $content);
        $data['appid'] = modC('SMS_UID', '', 'USERCONFIG');
        $data['signature'] = modC('SMS_PWD', '', 'USERCONFIG');
        $data['to'] = $mobile;
        $data['content'] = '【' . $config['sign'] . '】' . $cont['0'];
        $res = $this->post('https://api.mysubmail.com/message/send', $data);
        return $res;
    }


    private function post($url, $data, $timeout)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($curl);
        curl_close($curl);
        $ret=json_decode($ret,true);
        if ($ret['status'] == 'success') {
            return true;
        } else {
            return "发送失败! 状态：" . $ret['status'] . ' ' . $ret['msg'];
        }
    }
}
