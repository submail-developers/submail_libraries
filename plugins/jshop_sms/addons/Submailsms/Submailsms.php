<?php
namespace addons\Submailsms;	// 注意命名空间规范

use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;

/**
 * mms1086短信通道
 */
class Submailsms extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'Submailsms',	// 插件标识
        'title' => '赛邮云短信插件',	// 插件名称
        'description' => '赛邮云发送短信插件，请勿和其它短信通道一起使用',	// 插件简介
        'status' => 0,	// 状态
        'author' => 'xsqua',
        'version' => '0.1'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 实现的testhook钩子方法
     * @return mixed
     */
    public function sendsms($params)
    {
        $result     = [
            'status' => false,
            'data'   => [],
            'msg'    => '发送失败'
        ];
        $addonModel = new addonsModel();
        $setting    = $addonModel->getSetting($this->info['name']);
        if ($params['params']['code'] == 'seller_order_notice') {
            $params['params']['mobile'] = getSetting('shop_mobile');
            if (!$params['params']['mobile']) {
                $result['msg'] = '商户手机号不存在';
                return $result;
            }
        }

        $smsapi = "https://api.mysubmail.com/message/send/";
        $data['appid'] = $setting['sms_account']; //短信平台帐号
        $data['signature'] = $setting['sms_password']; //短信平台密码
        $data['to'] =   trim($params['params']['mobile']);

        $data['content'] = urlencode('【' . $setting['sms_prefix'] . '】'.$params['params']['content']);
        $query = http_build_query($data);
        $options['http'] = array(
            'timeout' => 60,
            'method' => 'POST',
            'header' => 'Content-type:application/x-www-form-urlencoded',
            'content' => $query
        );
        $context = stream_context_create($options);
        $result = file_get_contents($smsapi, false, $context);
        $output = trim($result, "\xEF\xBB\xBF");
        $res = json_decode($output, true);
        if ($res['status']== 'success') {
           $result['msg']    = '发送成功';
           $result['status'] = true;
       }else{
           $result['msg']    = '发送失败';
           $result['status'] = false;
       }
        return $result;
    }

    public function config($params = [])
    {
        $this->assign('config', $params);
        return $this->fetch('config');
    }

}
