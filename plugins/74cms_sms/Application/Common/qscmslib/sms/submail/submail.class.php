<?php

class submail_sms
{
    protected $_error = 0;
    protected $setting = array();

    public function __construct($setting)
    {
        $this->setting = $setting;
    }

    /**
     * 发送模板短信
     * @param string $type 短信通道 手机号码集合,用英文逗号分开
     * @param array $option ['mobile':手机号码集合,用英文逗号分开,'content':短信内容]
     * @return   boolean
     */
    public function sendTemplateSMS($type = 'captcha', $option)
    {
        $data['appid'] = $this->setting['appkey'];
        $data['signature'] = $this->setting['secretKey'];
        //解析模板内容
        if ($option['data']) {
            foreach ($option['data'] as $key => $val) {
                $method['{' . $key . '}'] = $val;
            }
            $data['content'] = '【' . $this->setting['signature'] . '】' . strtr($option['tpl'], $method);
        } else {
            $data['content'] = '【' . $this->setting['signature'] . '】' . $option['tpl'];
        }
        $data['to'] = $option['mobile'];
        $ret    =   $this->_https_request('https://api.mysubmail.com/message/send',$data);
        $ret = json_decode($ret,true);
        if ($ret['status']=='success') {
            return true;
        } else {
            return false;
        }
    }

    protected function _https_request($url, $data = null)
    {
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl, CURLOPT_USERAGENT, _USERAGENT_);
            curl_setopt($curl, CURLOPT_REFERER, _REFERER_);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
            if (!empty($data)) {
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
            curl_close($curl);
            return $output;
        } else {
            $this->_error = '短信发送失败，请开启curl服务！';
            return false;
        }
    }

    public function getError()
    {
        return $this->_error;
    }
}

?>
