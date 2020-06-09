<?php
/**
 * @copyright (c) 2016 smsbao.com
 * @file smsbao.php
 * @brief **短信发送接口
 * @author linf
 * @date 2016/11/21 11:10:38
 * @version 1.0
 */

/**
 * @class smsbao
 * @brief 短信发送接口 http://api.smsbao.com/sms
 */
class submail extends hsmsBase
{
    private $submitUrl;

    /**
     * @brief 获取config用户配置
     * @return array
     */
    public function getConfig()
    {
        $siteConfigObj = new Config("site_config");

        return array(
            'username' => $siteConfigObj->sms_username,
            'userpwd' => $siteConfigObj->sms_pwd,
            'sign' => $siteConfigObj->sms_userid,
        );
    }

    /**
     * @brief 发送短信
     * @param string $mobile
     * @param string $content
     * @return
     */
    public function send($mobile, $content)
    {
        $config = self::getConfig();

        $data = array(
            'appid' => $config['username'],
            'signature' => $config['userpwd'],
            'content' => '【' . $config['sign'] . '】' . $content,
            'to' => $mobile,
        );
        $query = http_build_query($data);
        $options['http'] = array(
            'timeout' => 60,
            'method' => 'POST',
            'header' => 'Content-type:application/x-www-form-urlencoded',
            'content' => $query
        );
        $context = stream_context_create($options);
        $result = file_get_contents("https://api.mysubmail.com/message/send/", false, $context);
        $output = trim($result, "\xEF\xBB\xBF");
        $res = json_decode($output, true);

        return $this->response($res);
    }

    /**
     * @brief 解析结果
     * @param $result 发送结果
     * @return string success or fail
     */
    public function response($result)
    {
        if (trim($result['status']) == 'success') {
            return 'success';
        } else {
            return $result['msg'];
        }
    }

    /**
     * @brief 获取参数
     */
    public function getParam()
    {
        return array(
            "username" => "用户名",
            "userpwd" => "密码",
            "usersign" => "短信签名",
        );
    }
}
