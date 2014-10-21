<?php  
echo "Program starts at ". date('h:i:s') . ".\r\n";  
$timeout = 3;   
$sockets = array();  //socket句柄数组  
//一次发起多个请求  
$delay = 0;  
while ($delay++ < 3)   
{  
    $sh = stream_socket_client("localhost:80", $errno, $errstr, $timeout,  
            STREAM_CLIENT_ASYNC_CONNECT|STREAM_CLIENT_CONNECT);   
    /* 这里需要稍微延迟一下，否则下面fwrite中的socket句柄不一定能真正使用 
       这里应该是PHP的一处bug，查了一下，官方bug早在08年就有人提交了 
       我的5.2.8中尚未解决，不知最新的5.3中是否修正 
    */  
//    usleep(10);   
    if ($sh) {   
        $sockets[] = $sh;   
        $http_header = "GET /custom/public/document/test_server.php?n={$delay} HTTP/1.0\r\n";  
        $http_header .= "Host: localhost\r\n";  
        $http_header .= "Accept: */*\r\n";  
        $http_header .= "Accept-Charset: *\r\n";   
        $http_header .= "\r\n";   
        fwrite($sh, $http_header);  
    } else {   
        echo "Stream failed to open correctly.\r\n";  
    }   
}   
//非阻塞模式来接收响应  
$result = array();   
$read_block_size = 8192;  
while (count($sockets))   
{   
    $read = $sockets;   
    $n = stream_select($read, $w=null, $e=null, $timeout);   
    //if ($n > 0) //据说stream_select返回值不总是可信任的  
    if (count($read))   
    {  
        /* stream_select generally shuffles $read, so we need to 
           compute from which socket(s) we're reading. */  
        foreach ($read as $r)   
        {   
            $id = array_search($r, $sockets);   
            $data = fread($r, $read_block_size);   
            if (strlen($data) == 0)   
            {   
                echo "Stream {$id} closes at " . date('h:i:s') . ".\r\n";  
                fclose($r);   
                unset($sockets[$id]);   
            } else {   
                if (!isset($result[$id])) $result[$id] = '';  
                $result[$id] .= $data;   
            }   
        }   
    } else {   
        echo "Time-out!\r\n";  
        break;  
    }   
}   
print_r($result);  