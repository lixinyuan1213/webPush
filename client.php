<?php
//$userName=$_SESSION['user']['name'];
$userName="somebody";
$userName=md5($userName);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>chatdemo</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1, maximum-scale=1, user-scalable=no">
        <link href="https://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
        <style type="text/css">
        <!--
        html, body {
          min-height: 100%; }

        body {
          margin: 0;
          padding: 0;
          width: 100%;
          font-family: "Microsoft Yahei",sans-serif, Arial; }

        .container {
          text-align: center; }

        .title {
          font-size: 16px;
          color: rgba(0, 0, 0, 0.3);
          position: fixed;
          line-height: 30px;
          height: 30px;
          left: 0px;
          right: 0px;
          background-color: white; }

        .content {
          background-color: #f1f1f1;
          border-top-left-radius: 6px;
          border-top-right-radius: 6px;
          margin-top: 30px; }
          .content .show-area {
            text-align: left;
            padding-top: 8px;
            padding-bottom: 168px; }
            .content .show-area .message {
              width: 70%;
              padding: 5px;
              word-wrap: break-word;
              word-break: normal; }
          .content .write-area {
            position: fixed;
            bottom: 0px;
            right: 0px;
            left: 0px;
            background-color: #f1f1f1;
            z-index: 10;
            width: 100%;
            height: 160px;
            border-top: 1px solid #d8d8d8; }
            .content .write-area .send {
              position: relative;
              top: -28px;
              height: 28px;
              border-top-left-radius: 55px;
              border-top-right-radius: 55px; }
            .content .write-area #name{
              position: relative;
              top: -20px;
              line-height: 28px;
              font-size: 13px; }
        -->
        </style>
    </head>
    <body>
        <div class="container">
            <div class="title">websocket前台例子</div>
            <div class="content">
                <div class="show-area"></div>
                <div class="write-area">
                    <div><button class="btn btn-default send" >发送</button></div>
                    <div><input name="name" id="name" type="text" placeholder="input your name"></div>
                    <div>
                        <textarea name="message" id="message" cols="38" rows="4" placeholder="input your message..."></textarea>
                    </div>                    
                </div>
            </div>
        </div>

        <script src="http://libs.baidu.com/jquery/1.9.1/jquery.min.js"></script>
        <script src="https://cdn.bootcss.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        <script>
        $(function(){
            // var num=Math.random();
            // num=Math.floor(num*1000000);
            // num=num.toString();
            // var clientId=Date.parse(new Date()).toString();
            // var=clientId+num;
            var clientId=<?php echo "'".$userName."'";?>;//确保唯一性
            var wsurl = 'ws://11.11.11.15:9505';
            var websocket;
            var i = 0;
            if(window.WebSocket){
                websocket = new WebSocket(wsurl);

                //连接建立
                websocket.onopen = function(evevt){
                    console.log("Connected to WebSocket server.");
                    var msg = {
                        type:"userMsg",
                        to: "",
                        message:clientId
                    };
                    websocket.send(JSON.stringify(msg));
                }
                //收到消息
                websocket.onmessage = function(event) {
                    console.log(event.data);
                    var msg = JSON.parse(event.data); //解析收到的json消息数据,做其他处理
                    $('.show-area').append('<p class="bg-success message"><a name="'+i+'"></a><i class="glyphicon glyphicon-info-sign"></i>'+msg.name+' 说：'+msg.message+'</p>');
                }

                //发生错误
                websocket.onerror = function(event){
                    console.log("Connected to WebSocket server error");
                    //其他处理
                }

                //连接关闭
                websocket.onclose = function(event){
                    console.log('websocket Connection Closed. ');
                    //其他处理
                }

                function send(){
                    var userName=$("#name").val();
                    var messageStr=$("#message").val();
                    var message={name:userName,message:messageStr}
                    var type="text";
                    var to="all";
                    var msg = {
                        message: message,
                        type: type,
                        to:to
                    };
                    try{
                        websocket.send(JSON.stringify(msg)); 
                    } catch(ex) {  
                        console.log(ex);
                    }  
                }

                //按下enter键发送消息
                $(window).keydown(function(event){
                    if(event.keyCode == 13){
                        console.log('user enter');
                        send();
                    }
                });

                //点发送按钮发送消息
                $('.send').bind('click',function(){
                    send();
                });
                
            }
            else{
                alert('该浏览器不支持web socket');
            }
        });    
        </script>        
    </body>
</html>