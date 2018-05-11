<?php
	 error_reporting(E_ERROR);
//swoole_timer_tick(2000, function () {
	
	$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC); //异步非阻塞
	 $i=randomkeys(4);

	$client->on("connect", function($cli) use($i) {
	    echo  $i."加入讨论.\n"; 
	    $cli->send("bind-".$i."\n");
	});
	$error=0;

	$client->on("receive", function($cli, $data)  use($i ){
	   if(!empty($data)){  
	       echo $i.'收到新消息:: '.$data."\n"; 
	    } 
	});

	$client->on("error", function($cli) use($i){
		$cli->close($s);
	    exit("error\n");
	});

	$client->on("close", function($cli) use($i){
	    echo $i."离开讨论\n";
	});
	 
	$client->connect('127.0.0.1', 9010, 1);
		
	
	 swoole_timer_tick(500, function ()  use($client,$i,&$error){
			if($client->isconnected() && $error<=3){ $s=rand(1,10);
				$send= $client->send($s);
				if($send){
				 	echo $i."对用户".$s."说：：hello \n" ;
				}else{
					$error++;
				}
			}else{
				$client->isconnected()?$client->close():null;
				exit();
			}
	   });
	
	   
//});

function randomkeys($length)   
{   
   $pattern = '1234567890abcdefghijklmnopqrstuvwxyz   
               ABCDEFGHIJKLOMNOPQRSTUVWXYZ';  
    for($i=0;$i<$length;$i++)   
    {   
        $key .= $pattern{mt_rand(0,35)};    //生成php随机数   
    }   
    return $key;   
}   
 
