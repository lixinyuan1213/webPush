<?php
class RedisModel extends Exception{
    public $redisModel;
   function __construct($url="127.0.0.1",$port=6379)
   {
       try{
           $redis = new Redis();
           $redis->connect($url,$port);
           $this->redisModel=$redis;
       }catch (Exception $e){
           var_dump($e->getMessage());
       }
   }
   public function getValue($val){
       return $this->redisModel->get($val);
   }
   public function setValue($k,$val){
       $this->redisModel->set($k,$val);
   }
   public function delByKey($k){
       $this->redisModel->delete($k);
   }

    /**
     * @param String $value 需要删除的值
     * @param array $arr 数据库中所有的键
     * author Fox
     */
   public function delByValue($value,$arr=array()){
       foreach ($arr as $val){
           $tempVal=$this->redisModel->get($val);
           if($tempVal==$value){
               $this->redisModel->delete($val);
           }
       }
   }
   public function getAllKeys($arr=array()){
       if(empty($arr)){
           return $this->redisModel->keys("*");
       }else{
           return $this->redisModel->getMultiple($arr);
       }
   }
}
$redis=new RedisModel();
//$redis->setValue("randomStringButIsOnly111","2222");
echo $redis->getValue("randomStringButIsOnly");
//var_dump($redis->getAllKeys());
