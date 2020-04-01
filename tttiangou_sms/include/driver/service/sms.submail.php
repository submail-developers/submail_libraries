<?php
 
class submail_smsServiceDriver extends smsServiceDriver
{
    private $cfg = array();
    private $Gateway = 'https://api.mysubmail.com/';

    public function config($cfg){
        $this->cfg = $cfg;
    }

    public function IMSend($phone,$content){
        if (false != $exids = $this->BC_EXPS($phone,$content,$this->cfg['bcmax'])){
            return '@exps('.count($exids).')';
        }

        if ($this->cfg['sign']) {
            $smsb = $sms = '【'.$this->cfg['sign'].'】'.$content;
        }else{
            $smsb = $sms = $content;
        }
        if(ENC_IS_GBK) $sms = ENC_G2U($sms);

        $phone  =   explode(';',$phone);
        foreach($phone as $k=>$v){
            $multi[$k]['to']=trim($v);
        }
        $url  = $this->Gateway.'message/multisend.json';
        $this->Debug('Request: Started');
        $this->Debug('Send: '.htmlspecialchars($smsb));

        $post['appid']  =   trim($this->cfg['account']);
        $post['signature']  =   trim($this->cfg['password']);
        $post['content']    =   trim($smsb);
        $post['multi']  =   json_encode($multi);

        $result = $this->post($url,$post);

        if (!$result){
            $this->Error('Connected Failed.');
            return $this->result_error('reponse-empty');
        }
        
        foreach($result as $v){
            if($v['status']!='success'){
                $this->Debug('Response: '.htmlspecialchars($v));
            }else{
                $this->Debug('Status: Send Number '.$v['to'].' success.');
            }
        }

        return $this->result_success(
            '发送短信成功',
                array(
                    'status'=>'success'
                )
        );
    }

    public function IMStatus()
    {
        $url  = $this->Gateway.'balance/sms.json';
        $post['appid']  =   trim($this->cfg['account']);
        $post['signature']  =   trim($this->cfg['password']);
        $result = $this->post($url,$post);
        if ($result['status'] =='success'){
            $status = '响应正常';
        }else{
            $status = '响应异常';
        }
        return sprintf('通道状态：%s<br/>短信剩余：%d 条',$result['status'],$result['balance']);
    }
    
    protected function post($api, $data)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array("Content-Type: multipart/form-data")
        ));
        $output = curl_exec($ch);
        curl_close($ch);
        $output = trim($output, "\xEF\xBB\xBF");
        return json_decode($output, true);
    }
}
?>