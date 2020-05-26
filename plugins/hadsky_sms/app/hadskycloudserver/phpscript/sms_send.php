<?php
if (!defined('puyuetian'))
    exit('403');

//ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);

//验证是否等待了60秒
if ($_COOKIE['APP_PUYUETIAN_SMS_TIME']) {
    exit(json_encode(array('state' => 'no', 'msg' => '请等待60秒后再操作')));
}

//验证码保护
if ($_G['SET']['APP_PUYUETIAN_SMS_VERIFYCODE']) {
    if (!$_G['GET']['VERIFYCODE'] || $_G['GET']['VERIFYCODE'] != $_SESSION['APP_VERIFYCODE_SMS']) {
        $_SESSION['APP_VERIFYCODE_SMS'] = '';
        exit(json_encode(array('state' => 'no', 'msg' => '验证码错误')));
    }
    $_SESSION['APP_VERIFYCODE_SMS'] = '';
}

//验证该IP是否超出了今日最大请求数
if (Cnum($_G['SET']['APP_PUYUETIAN_SMS_IPMAX'])) {
    $rt = $_G['TABLE']['APP_PUYUETIAN_SMS_RECORD']->getCount(array('ip' => getClientInfos('ip'), 'date' => date('Ymd')));
    if ($rt >= $_G['SET']['APP_PUYUETIAN_SMS_IPMAX']) {
        exit(json_encode(array('state' => 'no', 'msg' => '该IP今日请求已达上限')));
    }
}

//接收的手机号
$phonenumber = Cstr($_GET['phonenumber'], FALSE, $_G['STRING']['NUMERICAL'], 11, 11);
if (substr($phonenumber, 0, 1) != 1 || !$phonenumber) {
    exit(json_encode(array('state' => 'no', 'msg' => '手机号不正确')));
}
//验证该手机号是否超出了今日最大请求数
if (Cnum($_G['SET']['APP_PUYUETIAN_SMS_PNMAX'])) {
    $rt = $_G['TABLE']['APP_PUYUETIAN_SMS_RECORD']->getCount(array('pn' => $phonenumber, 'date' => date('Ymd')));
    if ($rt >= $_G['SET']['APP_PUYUETIAN_SMS_PNMAX']) {
        exit(json_encode(array('state' => 'no', 'msg' => '该号码今日请求已达上限')));
    }
}

//创建短信验证码
if (!Cnum($_SESSION['APP_PUYUETIAN_SMS_CODE']) || !Cstr($_SESSION['APP_PUYUETIAN_SMS_PHONE'], FALSE, $_G['STRING']['NUMERICAL'], 11, 11) || $phonenumber != $_SESSION['APP_PUYUETIAN_SMS_PHONE']) {
    $_SESSION['APP_PUYUETIAN_SMS_CODE'] = $code = rand(1000, 9999);
    $_SESSION['APP_PUYUETIAN_SMS_PHONE'] = $phonenumber;
} else {
    $code = $_SESSION['APP_PUYUETIAN_SMS_CODE'];
}

//请求发送短信
if (!empty($_G['SET']['APP_PUYUETIAN_SMS_SMSBAO_USER']) && !empty($_G['SET']['APP_PUYUETIAN_SMS_SMSBAO_PASS']) && !empty($_G['SET']['APP_PUYUETIAN_SMS_SMSBAO_SIGN'])) {

    $data['content'] ='【' . $_G['SET']['APP_PUYUETIAN_SMS_SMSBAO_SIGN'] . '】 您的验证码为：' . $code . '，请妥善保存。';
    $data['to'] =   $phonenumber;
    $data['appid']  =   trim($_G['SET']['APP_PUYUETIAN_SMS_SMSBAO_USER']);
    $data['signature']   =    trim($_G['SET']['APP_PUYUETIAN_SMS_SMSBAO_PASS']);
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

    if ($res['status'] == 'success') {
        $r['state'] = 'ok';
    } else {
        $r['state'] = $res['msg'];
    }

} else {
    $_apiurl = "http://www.hadsky.com/index.php?c=app&a=zhanzhang:index3&s=sendsms&domain={$_G['SYSTEM']['DOMAIN']}&code={$code}&phonenumber={$phonenumber}&sitekey=" . md5($_G['SET']['APP_HADSKYCLOUDSERVER_SITEKEY'] . $_G['SYSTEM']['DOMAIN']);
    $r = json_decode(GetPostData($_apiurl, '', 10), TRUE);
//$r = json_decode(file_get_contents($_apiurl));
}
if ($r['state'] == 'ok') {
    $r = array('state' => 'ok', 'msg' => '发送成功');
    setcookie('APP_PUYUETIAN_SMS_TIME', TRUE, time() + 60);
} else {
    $r = array('state' => 'no', 'msg' => $r['datas']['msg']);
}

//记录发送记录
$_G['TABLE']['APP_PUYUETIAN_SMS_RECORD']->newData(array('pn' => $phonenumber, 'ip' => getClientInfos('ip'), 'state' => $r['state'], 'msg' => $r['msg'], 'date' => date('Ymd'), 'datetime' => date('Y-m-d H:i:s')));
exit(json_encode($r));
