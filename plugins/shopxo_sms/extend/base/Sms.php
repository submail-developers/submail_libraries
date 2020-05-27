<?php
// +----------------------------------------------------------------------
// | ShopXO 国内领先企业级B2C免费开源电商系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2019 http://shopxo.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Devil
// +----------------------------------------------------------------------
namespace base;

/**
 * 短信驱动
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Sms
{
    // 保存错误信息
    public $error;

    // Access Key ID
    private $accessKeyId = '';

    // Access Access Key Secret
    private $accessKeySecret = '';

    // 签名
    private $signName = '';

    // 模版ID
    private $templateCode = '';

    private $expire_time;
    private $key_code;

    /**
     * [__construct 构造方法]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-07T14:03:02+0800
     * @param    [int]        $param['interval_time'] 	[间隔时间（默认30）单位（秒）]
     * @param    [int]        $param['expire_time'] 	[到期时间（默认30）单位（秒）]
     * @param    [string]     $param['key_prefix'] 		[验证码种存储前缀key（默认 空）]
     */
    public function __construct($param = array())
    {
        $this->interval_time = isset($param['interval_time']) ? intval($param['interval_time']) : 30;
        $this->expire_time = isset($param['expire_time']) ? intval($param['expire_time']) : 30;
        $this->key_code = isset($param['key_prefix']) ? trim($param['key_prefix']).'_sms_code' : '_sms_code';

        $this->signName = MyC('common_sms_sign');
        $this->accessKeyId = MyC('common_sms_apikey');
        $this->accessKeySecret = MyC('common_sms_apisecret');
    }

    private function percentEncode($string) {
        $string = urlencode ( $string );
        $string = preg_replace ( '/\+/', '%20', $string );
        $string = preg_replace ( '/\*/', '%2A', $string );
        $string = preg_replace ( '/%7E/', '~', $string );
        return $string;
    }
    /**
     * 签名
     *
     * @param unknown $parameters
     * @param unknown $accessKeySecret
     * @return string
     */
    private function computeSignature($parameters, $accessKeySecret) {
        ksort ( $parameters );
        $canonicalizedQueryString = '';
        foreach ( $parameters as $key => $value ) {
            $canonicalizedQueryString .= '&' . $this->percentEncode ( $key ) . '=' . $this->percentEncode ( $value );
        }
        $stringToSign = 'GET&%2F&' . $this->percentencode ( substr ( $canonicalizedQueryString, 1 ) );
        $signature = base64_encode ( hash_hmac ( 'sha1', $stringToSign, $accessKeySecret . '&', true ) );
        return $signature;
    }
    /**
     * @param unknown $mobile
     * @param unknown $verify_code
     *
     */
    public function SendCode($mobile, $code, $template_code) {
        // 是否频繁操作
        if(!$this->IntervalTimeCheck())
        {
            $this->error = '防止造成骚扰，请勿频繁发送';
            return false;
        }

        $gateway = "https://api.mysubmail.com/message/xsend";
        $data['project']    =   $template_code;
        $data['vars']   =   json_encode(array('code'=>$code));
        $data['appid']  =   $this->accessKeyId;
        $data['signature']  =   $this->accessKeySecret;
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

        if ($result['status'] != 'success') {
            $this->error = $result['msg'];
            return false;
        }

        // 种session
        $this->KindofSession($code);

        return true;
    }

    /**
     * [KindofSession 种验证码session]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-07T14:59:13+0800
     * @param    [string]      $code [验证码]
     */
    private function KindofSession($code)
    {
        $data = array(
            'code' => $code,
            'time' => time(),
        );
        cache($this->key_code, $data, $this->expire_time);
    }

    /**
     * [CheckExpire 验证码是否过期]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-05T19:02:26+0800
     * @return   [boolean] [有效true, 无效false]
     */
    public function CheckExpire()
    {
        $data = cache($this->key_code);
        if(!empty($data))
        {
            return (time() <= $data['time']+$this->expire_time);
        }
        return false;
    }

    /**
     * [CheckCorrect 验证码是否正确]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-05T16:55:00+0800
     * @param    [string] $code    [验证码（默认从post读取）]
     * @return   [booolean]        [正确true, 错误false]
     */
    public function CheckCorrect($code = '')
    {
        $data = cache($this->key_code);
        if(!empty($data))
        {
            if(empty($code) && isset($_POST['code']))
            {
                $code = trim($_POST['code']);
            }
            return ($data['code'] == $code);
        }
        return false;
    }

    /**
     * [Remove 验证码清除]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-08T10:18:20+0800
     * @return   [other] [无返回值]
     */
    public function Remove()
    {
        cache($this->key_code, null);
    }

    /**
     * [IntervalTimeCheck 是否已经超过控制的间隔时间]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-10T11:26:52+0800
     * @return   [booolean]        [已超过间隔时间true, 未超过间隔时间false]
     */
    private function IntervalTimeCheck()
    {
        $data = cache($this->key_code);
        if(!empty($data))
        {
            return (time() > $data['time']+$this->interval_time);
        }
        return true;
    }
}
?>
