<?php
/**
 * Socket client
 * Created by PhpStorm.
 * User: liujianlong
 * Date: 15/1/30
 * Time: 下午6:30
 */
class SocketClient{

    protected $socket;

    public function __construct(){
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    }

    /**
     * 格式化命令
     * @param $type
     * @param $param
     * @return bool|string
     */
    public function format_command($type,$param){
        switch($type){
            case Constant::$SEARCH_FUNC:
                $command = sprintf(Constant::$SEARCH_FUNC, $param['search_text'], $param['page'],
                    $param['pagesize']);
                break;
            default:
                $command = false;
                break;

        }
        return $command;
    }

    /**
     * 从服务器获取单条结果
     * @param $type
     * @param $param
     * @return null|string
     */
    public function get_from_server($type,$param ){
        $res = null;
        if($this->socket) {
            try {
                $connect = socket_connect($this->socket, Constant::$SEARCH_SERVER_IP,
                    Constant::$SEARCH_SERVER_PORT);
                if($connect){
                    $command = $this->format_command($type,$param);
                    $send_res = socket_write($this->socket, $command);
                    $res = socket_read($this->socket, 1024, PHP_NORMAL_READ);
                }else{
                    $err = '连接服务器失败';
                }
                socket_close($this->socket);
            } catch (ErrorException $e) {
                $err = $e->getMessage();
                $res = null;
            }
        }
        return $res;
    }

    /**
     * 从服务器获取多条结果
     * @param $type
     * @param $param
     * @return null|string
     */
    public function mget_from_server( $parameters ){
        $res = array();
        if($this->socket) {
            try {
                $connect = socket_connect($this->socket, Constant::$SEARCH_SERVER_IP,
                    Constant::$SEARCH_SERVER_PORT);
                if($connect){
                    foreach($parameters as $param){
                        $command = $this->format_command($param['type'],$param['param']);
                        $send_res = socket_write($this->socket, $command);
                        $read_res = socket_read($this->socket, 1024, PHP_NORMAL_READ);
                        array_push($res, $read_res);
//                        $res =
                    }
                }else{
                    $err = '连接服务器失败';
                }
                socket_close($this->socket);
            } catch (ErrorException $e) {
                $err = $e->getMessage();
                $res = null;
            }
        }
        return $res;
    }
}