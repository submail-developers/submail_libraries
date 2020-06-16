<?php
namespace Home\Controller;

use Common\Controller\FrontendController;

class SubmailController extends FrontendController{
    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 安装短信宝插件
     */
    public function index(){
        $data['name']='赛邮云（推荐）';
        $data['alias']='submail';
        $data['remark']='免费申请地址： https://www.mysubmail.com/chs/store';
        $map['id']='1';
        $res=D('sms')->where($map)->save($data);
        //短信模板
        $map_sms_tpl['type']='qscms';
        $data_sms_tpl['type']='submail';
        $sms_tpl_res=D('sms_templates')->where($map_sms_tpl)->save($data_sms_tpl);
        if($res&&$sms_tpl_res){
            echo '赛邮云短信插件安装成功，请将Application/Home/Controller/SubmailController.class.php文件删除';
        }else{
            echo '插件安装失败，请联系赛邮云客服';
        }
    }

}
