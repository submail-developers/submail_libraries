<?php
/**
 * 手机短信类
 */

namespace sendmsg;
class Sms
{
    /*
     * 发送手机短信
     * @param unknown $mobile 手机号
     * @param unknown $content 短信内容
    */
    public function send($mobile, $content)
    {
        return $this->mysend_sms($mobile, $content);
    }
    
    private function mysend_sms($mobile, $content)
    {
        $user_id = config('smscf_wj_username'); // 这里填写用户名
        $key = config('smscf_wj_key'); // 这里填接口安全密钥
        if (!$mobile || !$content || !$user_id || !$key)
            return false;
        $content = "【" . config("site_name") . "】" . $content['message'];
        $url = "https://api.mysubmail.com/message/send";

        $send_str['to'] = trim($mobile);
        $send_str['content'] = $content;
        $send_str['appid'] = $user_id;
        $send_str['signature'] = $key;
        $res = json_decode(http_request($url, 'POST', $send_str), true);
        if ($res['status'] == 'success') {
            return true;
        }
        return false;
    }
}
