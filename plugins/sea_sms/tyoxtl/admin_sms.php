<?php
header('Content-Type:text/html;charset=utf-8');
require_once(dirname(__FILE__) . "/config.php");
CheckPurview();
if ($action == "set") {
    $weburl = $_POST['smsbao_user'];
    $token = $_POST['smsbao_pass'];
    $token = $_POST['smsbao_sign'];
    $open = fopen("../data/admin/sms.php", "w");
    $str = '<?php ';
    $str .= '$smsbao_user = "';
    $str .= "$smsbao_user";
    $str .= '"; ';
    $str .= '$smsbao_pass = "';
    $str .= "$smsbao_pass";
    $str .= '"; ';
    $str .= '$smsbao_sign = "';
    $str .= "$smsbao_sign";
    $str .= '"; ';
    $str .= " ?>";
    fwrite($open, $str);
    fclose($open);
    ShowMsg("成功保存设置!", "admin_sms.php");
    exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>短信设置</title>
    <link href="img/style.css" rel="stylesheet" type="text/css"/>
    <link href="img/style.css" rel="stylesheet" type="text/css"/>
    <script src="js/common.js" type="text/javascript"></script>
    <script src="js/main.js" type="text/javascript"></script>
</head>
<body>
<script type="text/JavaScript">if (parent.$('admincpnav')) parent.$('admincpnav').innerHTML = '后台首页&nbsp;&raquo;&nbsp;管理员&nbsp;&raquo;&nbsp;短信设置';</script>
<div class="r_main">
    <div class="r_content">
        <div class="r_content_1">
            <form action="admin_sms.php?action=set" method="post">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb_style">
                    <tbody>
                    <tr class="thead">
                        <td colspan="5" class="td_title">短信设置</td>
                    </tr>
                    <tr>
                        <td width="80%" align="left" height="30" class="td_border">
                            <?php require_once("../data/admin/sms.php"); ?>
                            赛邮云用户名：<input name="smsbao_user" value="<?php echo $smsbao_user; ?>">
                            还没有账号？<a href="https://www.mysubmail.com/chs/store" style="color:red;">请点击注册</a>
                        </td>
                    </tr>

                    <tr>
                        <td width="80%" align="left" height="30" class="td_border">
                            赛邮云密码：<input name="smsbao_pass" value="<?php echo $smsbao_pass; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td width="80%" align="left" height="30" class="td_border">
                            赛邮云签名：<input name="smsbao_sign" value="<?php echo $smsbao_sign; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td width="90%" align="left" height="30" class="td_border">
                            * 如果修改无效，请检查/data/admin/sms.php文件权限是否可写。
                        </td>
                    </tr>
                    <tr>
                        <td width="10%" align="left" height="30" class="td_border">
                            <input type="submit" value="确 认" class="btn">
                        </td>
                    </tr>
                    </tbody>
                </table>


            </form>
        </div>
    </div>
</div>
<?php
viewFoot();
?>
</body>
</html>
