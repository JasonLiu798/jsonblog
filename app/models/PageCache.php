<?php

/**
 * Created by PhpStorm.
 * User: liujianlong
 * Date: 15/2/6
 * Time: 下午4:28
 */
class PageCache{

    private static $redis;
    private static $INDEX = 'INDEX';
    private static $LONG_TIME = 3600;//1hour
    private static $MID_TIME = 1800;//half hour
    private static $SHORT_TIME = 300;//
    private static $TWO_MIN = 120;
    private static $ONE_MIN = 60;
    private static $HALF_MIN = 30;

    private static $COMM_CHANGE_CNT;
    private static $POST_CHANGE_CNT;




    function __construct(){
        echo "construct";
        self::$redis = LRedis::connection('viewcache');
    }

    public function get_index(){
        return self::$redis->get( self::$INDEX );
    }

    public function set_index($view){
        self::$redis->SET( self::$INDEX , (string)$view );
        self::$redis->EXPIRE( self::$INDEX , self::$ONE_MIN );
    }


    public function index_update($view=null){
        if(is_null($view)){
            self::$redis->PERSIST( self::$INDEX );
        }else{
            $this->set_index($view);
        }
    }

    public function sidebar_update(){
        echo "update";
    }

    public static function admin_update(){
        echo "admin_update";
    }

}