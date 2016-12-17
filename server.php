<?php
require "redis.php";
$redis=new RedisModel();//redis中维护前台id和后台id的对应关系。
$serv = new Swoole\Websocket\Server("0.0.0.0", 9505);
$serv->on('Open', function($server, $req) {
    //echo "connection open: ".$req->fd;//cli调试用，正式环境下不要用
});
$serv->on('Message', function($server, $frame) {
    $reqData=json_decode($frame->data,true);
    if($reqData['type']=="userMsg"){
        global $redis;
        $redis->setValue($reqData['message'],$frame->fd);
    }
    if($reqData['type']=="text"){
        if($reqData['to']=="all"){
            foreach($server->connections as $fd){
               $server->push($fd ,json_encode($reqData['message'],JSON_UNESCAPED_UNICODE));//循环广播
            }
        }else{
            $server->push($reqData['to'],json_encode($reqData['message'],JSON_UNESCAPED_UNICODE));
        }
    }
});
$serv->on('Close', function($server,$fd) {
    global $redis;
    $redis->delByValue($fd,$redis->getAllKeys());//在redis中删除连接。
});
$serv->start();