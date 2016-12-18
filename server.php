<?php
require "redis.php";
require('vendor/autoload.php');
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

date_default_timezone_set('Asia/Shanghai');
$logger = new Logger('my_logger');
$logger->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Logger::INFO));
$logger->pushHandler(new FirePHPHandler());

$redis=new RedisModel();//redis中维护前台id和后台id的对应关系。
$serv = new Swoole\Websocket\Server("0.0.0.0", 9505);
$serv->on('Open', function($server, $req) {
    global $logger;
    $logger->addInfo('连接到服务器的客户id为：',['client_id' => $req->fd]);
});
$serv->on('Message', function($server, $frame) {
    $reqData=json_decode($frame->data,true);
    if($reqData['type']=="userMsg"){
        global $redis;
        $redis->setValue($reqData['message'],$frame->fd);
        global $logger;
        $logger->addInfo('客户注册成功',['client_id' => $frame->fd]);
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
    global $logger;
    $logger->addInfo('断开的客户id为：',['client_id' => $fd]);
    $redis->delByValue($fd,$redis->getAllKeys());//在redis中删除连接。
});
$serv->start();