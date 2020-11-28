<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/13 0013
 * Time: 8:34
 */

class  TcpServer
{
    private $port = 8080;
    private $addr = "127.0.0.1";
    private $socket_handle;
    private $back_log = 10;
    private $websocket_key;
    private $current_message_length;

    private $is_shakehanded = false;
    private $mask_key;


    public function __construct($port = 8080, $addr = "127.0.0.1", $back_log = 10)
    {
        $this->port = $port;
        $this->addr = $addr;
        $this->back_log = $back_log;
    }


    /**
     * @throws Exception
     */
    private function createSocket()
    {
        //创建socket套接字
        $this->socket_handle = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!$this->socket_handle) {
            //创建失败抛出异常，socket_last_error获取最后一次socket操作错误码，socket_strerror打印出对应错误码所对应的可读性描述
            throw new Exception(socket_strerror(socket_last_error($this->socket_handle)));
        } else {
            echo "create socket successful\n";
        }
    }


    /**
     * @throws Exception
     */
    private function bindAddr()
    {
        if (!socket_bind($this->socket_handle, $this->addr, $this->port)) {
            throw new Exception(socket_strerror(socket_last_error($this->socket_handle)));
        } else {
            echo "bind addr successful\n";
        }
    }

    private function listen()
    {
        if (!socket_listen($this->socket_handle, $this->back_log)) {
            throw new Exception(socket_strerror(socket_last_error($this->socket_handle)));
        } else {
            echo "socket  listen successful\n";
        }
    }

    /**
     * @throws Exception
     */
    private function accept()
    {
        $client_socket_handle = socket_accept($this->socket_handle);
        if (!$client_socket_handle) {
            echo "socket_accept call failed\n";
            exit(1);
        } else {
            while (true) {
                $bytes_num = socket_recv($client_socket_handle, $buffer, 100, 0);
                if (!$bytes_num) {
                    echo "socket_recv  failed\n";
                    exit(1);
                } else {
                    echo "content from client:" . $buffer . "\n";
                }
            }
        }
    }

    public function startServer()
    {
        try {
            $this->createSocket();
            $this->bindAddr();
            $this->listen();
            $this->accept();
        } catch (Exception $exception) {
            echo $exception->getMessage() . "\n";
        }
    }
}

$server = new TcpServer();
$server->startServer();
