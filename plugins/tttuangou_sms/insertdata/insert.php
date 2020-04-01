<?php
 class connectSQL{
  private $host ;
  private $name ;
  private $pwd ;
  private $db ;
  public function init($host,$name,$passwd,$database,$tb){
   $this->host = $host;
   $this->name = $name;
   $this->pwd = $passwd;
   $this->db = $database;
   $this->table = $tb;
   $this->init_conn();
  }
  private function init_conn(){
   $this->conn=@mysql_connect($this->host,$this->name,$this->pwd);
   if(!$this->conn)
    die("链接MySQL失败".mysql_error());
   else
    //echo"连接MySQL成功!<br>";
    @mysql_select_db($this->db,$this->conn);
   @mysql_query("set names 'utf8'");
  }
  private function mysql_query_rst($sql){
   if($this->conn =='')
    $this->init_conn();
   $this->result = @mysql_query($sql,$this->conn);
   return $this->result;
  }
 
  private function mysql_insert($sql){
   return $this->mysql_query_rst($sql);
  }  
 
  public function toInsert(){
   $sql="INSERT INTO `".$this->db."`.`cenwor_tttuangou_service` (`id`, `type`, `flag`, `name`, `weight`, `count`, `config`, `enabled`, `update`, `surplus`) VALUES ('0', 'sms', 'submail', '赛邮云通道', '100', '0', 'a:6:{s:6:\"driver\";s:6:\"submail\";s:7:\"account\";s:6:\"test\";s:8:\"password\";s:8:\"123456\";s:4:\"sign\";s:4:\"[77]\";s:5:\"bcmax\";s:3:\"222\";s:4:\"sinv\";s:1:\"0\";}', 'false', '1391862128', '2')";
   if( $this->mysql_insert($sql)){
    echo "job done";
   }
   else{
    echo "job failed".$sql;
   }
  }  
 }
 include_once '../setting/settings.php';
 $sql_connect = new connectSQL();
 $sql_connect->init($config['settings']['db_host'], $config['settings']['db_user'], $config['settings']['db_pass'], $config['settings']['db_name'],$config['settings']['db_table_prefix']."tttuangou_service");
 $sql_connect->toInsert();
?>