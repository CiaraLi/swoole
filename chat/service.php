<?php

//create server
$server=new swoole_server('127.0.0.1','9010');
$list=[]; 

$server->set(array(
   // 'reactor_num' => 2, //reactor thread num
    'worker_num' => 4,    //worker process num
   // 'backlog' => 128,   //listen backlog
    'max_request' => 50,
   // 'dispatch_mode' => 1,
));
//listen
$server->on('connect',function($server,$fd) { 
	echo ' 欢迎新用户 :'.$fd." \n"; 
	$list[$fd]=
	$server->send($fd,"欢迎！您的ID :".$fd." \n");
});

$server->on('receive',function($server,$fd,$from_id,$data) use(&$list){
	echo '接受到'.$fd.'的消息：::'.$data." \n";
	$data=trim($data);
	if($data=='exit'){
		$server->close($fd);
	}elseif(strpos($data,'bind-')===0 && strlen($data)>5){
		$list[$fd]=explode('-',$data)[1];
	}else{
		if(is_numeric(trim($data))){
			$send=intval(trim($data)); 
			$sendto=empty($list[$send])? $send: $list[$send];
			$from=empty($list[$fd])? $fd: $list[$fd];
			if($server->exist($send)){
				$server->send($send,' 用户'.$from."对你说 :: hello"." \n");
			}elseif($send==$fd){
				$server->send($send,"您对自己说 :: hello"." \n");
			}else{
		 		$server->send($fd,' 用户'.$sendto."已离开"." \n");
			}
		 }else{
		 	empty(trim($data))?"":$server->send($fd,"Server::".$data.' ok'." \n");
		}
	 }
});

$server->on('close',function($server,$fd) use(&$list) { 
	 $list[$fd]='';
	echo ' 欢迎下次光临:'.$fd." \n"; 
});
$server->start();
