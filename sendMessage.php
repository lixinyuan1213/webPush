<?php
require('vendor/autoload.php');
use WebSocket\Client;
/**
 * User: Administrator
 * 通用websocket客户端，用于给web前台发送消息，可以单独发送消息（在redis中查询客户端id），可以广播消息。
 */
class sendMessage
{
    private $client;
    function __construct($url="ws://11.11.11.15:9505")
    {
        $this->client = new Client($url);
    }

    /**
     * @param String $message 需要发送的信息（暂时为文本）,array
     * @param String $type 发送的类型，text--文本消息，userMsg--用户注册信息
     * @param String $to  发送给谁
     */
    public function send($message,$to="all",$type="text"){
        $dataArr=array(
           'message'=>$message,
            'to'=>$to,
            'type'=>$type
        );
        $data=json_encode($dataArr,JSON_UNESCAPED_UNICODE);
        $this->client->send($data);
    }
}
$sendModel=new sendMessage("ws://11.11.11.15:9505");
$sendModel->send(array("message"=>"通知类","name"=>"管理员"));//发给所有的用户
//$sendModel->send(array(array("message"=>"通知类","name"=>"管理员"),2);//发给id为2的用户（在redis中查询得到）

/* 定时服务，只能以cli形式执行，不可以在apache里执行
$timeId=swoole_timer_tick(1000*30, function ($timer_id) {
    global $sendModel;
    $sendModel->send(array("message"=>"30秒一次，发送信息","name"=>"管理员"));//发给所有的用户
});
swoole_timer_after(1000*60*5, function () {
    global $timeId;
    swoole_timer_clear($timeId);
});
*/