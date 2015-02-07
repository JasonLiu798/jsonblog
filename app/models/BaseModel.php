<?php
/**
 * Created by PhpStorm.
 * User: liujianlong
 * Date: 15/1/28
 * Time: 下午5:56
 *
 * BaseModel,Common functions
 *
 */
class BaseModel extends Eloquent {

    protected static $TS_PKSET_KEY = "TS_PK_%s_%s#SET";// TS_PK_[classname]_condition
    protected static $MODEL_KEY = "%s#%s";//primarykey#{primarykey}
    protected static $MODEL_CNT_KEY = "%s#COUNT";// classname#COUNT
    protected static $CNT_KEY = "%s-%s#%s#COUNT";// classname-relateclassname#{pk}#count
    public $error;
//    protected static $MODEL_KEY = "";

    /**
     * ------------------------------PK 生成器------------------------------
     */
    /**
     * 获取新PK
     * @param bool $init
     * @return bool|init
     */
    public function get_new_pk( $redis=null ){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $pre_pk = $redis->get( $this->primaryKey );
        $pk = 0;
        if(is_null( $this->primaryKey)){
            return false;
        }
        if(is_null($pre_pk) ){
            //init pid
            if( !$this->init_pk_generator($redis)){
                Log::error( "init $this->table PK $this->primaryKey in redis failed" );
                return false;
            }
        }
        $pk = $redis->incr( $this->primaryKey );
        if($pk){
            return $pk;
        }else{
            return false;
        }
    }

    /**
     * 初始化PK counter
     * @param null $redis
     * @return bool
     */
    public function init_pk_generator($redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $max_pid = DB::table( $this->table )->max( $this->primaryKey ) ;
        if($max_pid){
            $res_set = $redis->set($this->primaryKey ,$max_pid);//如无acid,get max acid 设置
            if(!$res_set){
                return false;
            }
        }
        return true;
    }

    /**
     * ------------------------------校验函数------------------------------
     */
    /**
     * 验证是否有效PK
     * 0<$pid<max pk+1
     * @param $pid
     */
    public function chk_pk_format($pid){
        if(!is_numeric($pid)) {
            return false;
        }
        $redis = LRedis::connection();
        $max_pk = $redis->get( $this->primaryKey );
        if( is_null($max_pk) ){
            //初始化 redis pk
            if( !$this->init_redis_pk($redis)){
                Log::error( "init $this->table PK $this->primaryKey in redis failed" );
                return false;
            }
        }else{
            if($pid > $max_pk || $pid<0){
                return false;
            }
        }
        return true;
    }

    /**
     * ------------------------------所有 PK SET------------------------------
     */
    /**
     * 获取PK set
     * @return array idx=>PK,idx无意义
     */
    public function get_pk_set($redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $key = $this->primaryKey."#set";
        $pk_set_cache = $redis->sMembers($key);
        if(is_null($pk_set_cache)|| (is_array($pk_set_cache)&& count($pk_set_cache)==0) ){
            $init_res = $this->init_pk_set();
            $res = array();
            if( $init_res ){
                foreach( $init_res as $pk ){
                    $pk_arr = (array)$pk;
                    array_push($res,$pk_arr[$this->primaryKey] );
                }
            }
        }else{
            $res = $pk_set_cache;
        }
        return $res;
	}

    /**
     * 初始化主键set
     * @return bool
     */
    public function init_pk_set($redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $key = $this->primaryKey."#set";
        try{
            $pk_set_db = DB::table($this->table)->select($this->primaryKey)->get();
//            gettype($pk_set_db);
            if( is_array($pk_set_db) && count($pk_set_db)>0 ){
                foreach( $pk_set_db as $pk ){
                    $pk_arr = (array)$pk;
                    $redis->sAdd( $key  , $pk_arr[$this->primaryKey]  );
                }
            }
            $res = $pk_set_db;
        }catch(Exception $e){
            $error_msg = $e->getMessage();
            $method = __METHOD__;
            Log::error("{$method}|MSG:{$error_msg}|获取{$this->primaryKey}PK错误");
            $res = false;
        }
        return $res;
    }

    /**
     * 新建pk加入PK set
     * @param $pk
     * @return mixed
     */
    public function add_pk2set($pk,$redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $key = $this->primaryKey."#set";
        return $redis->sAdd($key , $pk );
    }

    /**
     * 删除PK set 中一个pk
     * @param $pk
     * @return mixed
     */
    public function delete_pk2set($pk,$redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $key = $this->primaryKey."#set";
        return $redis->sRem($key , $pk );
    }

    public function get_pk_set_size($redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $key = $this->primaryKey."#set";
        return $redis->scard($key);
    }


    /**
     * ---------------------------------有序PK相关---------------------------------
     */
    /**
     * 获取按时间排序的pk set
     * 外部做 page,pagesize 的有效性判断，函数内直接使用
     * @param null $redis
     * @return array
     */
    public function get_ts_pk_set($page,$pagesize,$redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $key = sprintf( self::$TS_PKSET_KEY,$this->table );//format:TS_PK_{tablename}#SET

        $page_idx = $this->page2index($page,$pagesize);
        $start = $page_idx['start'];
        $stop = $page_idx['stop'];

        Log::info( "page:{$page},size:{$pagesize},st:{$start},ed:{$stop}");
        //ZRANGE KEY START STOP [WITHSCORES]
        $ts_pk_set_cache = $redis->ZREVRANGE( $key,$start,$stop );
        if( is_null($ts_pk_set_cache)|| (is_array($ts_pk_set_cache)&& count($ts_pk_set_cache)
            ==0) ){
            $actual_size = $this->get_size_db();
            if( $actual_size > 0 ){//实际库内有数据
                $init_res = $this->init_ts_pk_set($redis);
                if($init_res){
                    $res =  $redis->ZREVRANGE( $key,$start,$stop );//再次获取
                }else{
                    //初始化失败
                    $method = __METHOD__;
                    $sub_class = get_class($this);
                    Log::error("{$method}|初始化类 {$sub_class} 时间有序集PK失败");
                    $res = null;
                }
            }else{//库内无数据
                $res = $ts_pk_set_cache;//空array
            }
        }else{
            $res = $ts_pk_set_cache;
        }
        return $res;
    }

    /**
     * @param $pks
     * @param null $redis
     */
    public function get_modles_from_pkset($pks,$redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $res = array();
        if( is_array($pks) && count($pks)>0){
            foreach($pks as $pk){
//				$post = $this->get_one_post_nocontent($pk,$redis);
                $model = $this->get_model($pk,$redis);
                if(!is_null($model)){
                    array_push($res,$model);
                }else{
                    $errmsg = "获取到空model";
                    $this->error = $errmsg;
                    $method = __METHOD__;
                    Log::error("{$method}|MSG:{$errmsg}");
                }
            }
        }
        return $res;
    }
    /**
     * 获取时间排序pk set 大小
     * @param null $redis
     * @return mixed
     */
    public function get_ts_pk_set_size($condition=0,$redis = null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $key = strtoupper(sprintf( self::$TS_PKSET_KEY,$this->table ));
        //format:TS_PK_{tablename}#SET
        return $redis->ZCARD($key);
    }


    /**
     * 初始化时间排序主键set
     * @return bool
     */

    /**
     * @param int $condition[转换为位模式]
     * 0=无过滤条件，
     * 1=条件1
     * 2=条件2
     * 3=条件1&条件2
     * 4=条件3
     * ...
     * @param null $redis
     * @return bool|int
     */
    public function init_ts_pk_set($condition=0,$redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $res = true;
        //判断参数
        if(!is_int($condition) || $condition<0){
            $this->error = "参数错误，必须为正整数";
            $res =  false;
        }else{
            try{
                /**
                 * 判断子类是否定义了 实际排序列$TS_COL 和 $CONDITION_COL过滤条件
                 * $TS_COL 必须定义
                 * conditon =0不判断$CONDITION_COL
                 */
                //判断$TS_COL是否定义
                $ts_col_res = false;
                $classname = get_class($this);
                $ts_col_isset_eval = "\$ts_col_res = isset( $classname::\$"
                    .Constant::$TS_COL_NAME .');';
                eval($ts_col_isset_eval);
                if( $ts_col_res ){//如果有才进行condition判断
                    //$TS_PKSET_KEY = "TS_PK_%s_%s#SET";
                    $key = strtoupper( sprintf( self::$TS_PKSET_KEY,$classname,$condition ));
                    //获取time sort col name
                    $time_col = null;
                    $get_time_sort_col_eval = "\$time_col = ". get_class($this) ."::\$"
                        .Constant::$TS_COL_NAME.";";
                    eval( $get_time_sort_col_eval );

                    $condition_arr = array();
                    if($condition>0){
                        $condition_col_res = false;
                        $condition_col_isset_eval = "\$condition_col_res = isset( ".get_class($this)."::\$"
                            .Constant::$CONDITION_COL_NAME .');';
                        eval($condition_col_isset_eval);
                        if($condition_col_res){//定义了过滤数组
                            //获取条件数组
                            $condition_col = null;
                            $get_condition_col_eval = "\$condition_col = ". get_class($this) ."::\$"
                                .Constant::$CONDITION_COL_NAME.";";
                            eval( $get_condition_col_eval);
//                            echo "CC\n";
//                            print_r( $condition_col );

                            //过滤数组存在，且成员数量大于0
                            if( is_array($condition_col) && count($condition_col)>0){
                                //判断条件数组大小
                                if( count( $condition_col ) < floor(log($condition)/log(2))+1 ){
                                    $res = false;
                                    $this->error = "过滤条件数组count小于 log(condition)/log2+1";
                                }else{
                                    $i = 0;
                                    //$selected_condition_arr = array();
                                    while($condition !=0){
                                        $is_set = $condition%2;
                                        if( $is_set == 1){
                                            array_push($condition_arr,$condition_col[$i]);
                                        }
                                        $i++;
                                        $condition = $condition >>1;
                                    }
                                }
                            }
                        }else{
                            //conditon >0 ,但condition arr未定义
                            $res = false;
                            $this->error = "conditon >0 ,但condition arr未定义";
                        }
                    }
//                    echo "CA:\n";
//        print_r($condition_arr);
                    if( $res ){
                        /**
                         * 合成语句
                         */
                        if( ($size = count( $condition_arr ))>0 ){
                            $i = 0;
                            $where_raw = '';
                            foreach($condition_arr as $item){
                                if($i == $size-1){
                                    $where_raw .= sprintf(" %s ",$item );
                                }else{
                                    $where_raw .= sprintf(" %s and ",$item );
                                }
                                $i++;
                            }
                            echo $where_raw;
                            $pk_set_db = DB::table($this->table)->select( $this->primaryKey , $time_col )
                                ->whereRaw($where_raw)
                                ->get();
//                    $queries = DB::getQueryLog();
//                    $last_query = end($queries);
//                    var_dump($pk_set_db);
//                    echo $last_query['query'];
                            // 			Log::info('post date:'.$last_query['query']);
                        }else{
                            /**
                             * 不需要过滤
                             */
                            $pk_set_db = DB::table($this->table)->select( $this->primaryKey , $time_col )->get();
                        }
                        if( is_array($pk_set_db) && count($pk_set_db)>0 ){
                            foreach( $pk_set_db as $pk ){
                                $pk_arr = (array)$pk;
                                // ZADD key score member [[score member] [score member] ...]
                                $redis->ZAdd( $key  ,
                                    strtotime( $pk_arr[ $time_col ]),//score 时间
                                    $pk_arr[$this->primaryKey] );// member pk
                            }
                        }
                        $res = count($pk_set_db);
                    }
                }else{
                    //未定义time 列
                    $method = __METHOD__;
                    $sub_class = get_class($this);
                    Log::error("{$method}|子类:{$sub_class}未定义排序键值");
                    $res = false;
                }
            }catch(Exception $e){
                $error_msg = $e->getMessage();
                $method = __METHOD__;
                Log::error("{$method}|MSG:{$error_msg}|初始化时间排序主键set异常");
                $res = false;
            }
        }
        return $res;
    }



/*



//            eval($filter_col_isset_eval.$ts_col_isset_eval );
//            $filter_col = null;
//            $time_col = null;

            if( $ts_col_res ){

//                echo 'time col:'.$time_col;
                if( $filter_col_res ){
                    $filter_col_eval = "\$filter_col = ". get_class($this) ."::\$"
                        .Constant::$TS_INIT_FILTER_COL_NAME
                        .";";
                    eval( $filter_col_eval);
//                    print_r($filter_col);
                }
                $res = false;
                /**
                 * 需要过滤
                 *
                if( is_array( $filter_col )&& ($size = count( $filter_col ))>0 ){
                    $i = 0;
                    $where_raw = '';
                    foreach($filter_col as $colname=>$colvalue){
                        if($i == $size-1){
                            $where_raw .= sprintf(" %s %s ",$colname,$colvalue);
                        }else{
                            $where_raw .= sprintf(" %s %s and ",$colname,$colvalue);//$colname
                            //.$colvalue.' and ';
                        }
                        $i++;
                    }
//                    echo $where_raw;
                    $pk_set_db = DB::table($this->table)->select( $this->primaryKey , $time_col )
                        ->whereRaw($where_raw)
                        ->get();
//                    $queries = DB::getQueryLog();
//                    $last_query = end($queries);
//                    var_dump($pk_set_db);
//                    echo $last_query['query'];
                    // 			Log::info('post date:'.$last_query['query']);
                }else{
                    /**
                     * 不需要过滤
                     *
                    $pk_set_db = DB::table($this->table)->select( $this->primaryKey , $time_col )->get();
                }
*/


//            gettype($pk_set_db);





    protected function page2index($page,$pagesize){
        return array("start"=>($page-1)*$pagesize,"stop"=>$page*$pagesize - 1);
    }








    /**
     * 按页获取
     * @param $page
     * @param $pagesize
     * @param null $redis
     */
    public function get_page($page,$pagesize,$redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        self::$MODELKEY;
    }

    /**
     *
     * @param null $redis
     */
    public function get_models($redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $pk_set = $this->get_pk_set($redis);
        $res = array();
        if(is_array($pk_set) && count($pk_set)>0){
            foreach($pk_set as $pk){
                $modle = $this->get_model($pk,$redis);
                if(!is_null($modle)){
                    array_push($res,$modle);
                }
            }
        }
        $size = $this->get_size_db();
        if( count($res)!= $size){
            $res = $this->get_models_db();
        }
        return $res;
    }

    /**
     * 获取单个modle，先从缓存取，没有则从库中取
     * @param $pk
     * @param null $redis
     * @return mixed|null
     */
    public function get_model($pk,$redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $key  = strtoupper(sprintf( self::$MODEL_KEY ,  $this->primaryKey,$pk));
        $res_json = $redis->get($key);
        if(is_null($res_json)){
            $res = null;
            $classname = get_class($this);
            $get_modle_eval = "\$res = $classname::find($pk);";
            eval($get_modle_eval);
//            echo $get_modle_eval."\n";
            if(!is_null($res)){
                $redis->set($key, serialize($res) );
            }
        }else{
            $res = unserialize($res_json);
        }
        return $res;
    }

    /**
     * 从库中取所有modles
     */
    public function get_models_db(){
        $classname = get_class($this);
        $modles = null;
        $get_models_eval = "\$modles = $classname::all();";
        try{
            eval($get_models_eval);
            $res = $modles;
        }catch(Exception $e){
            $error_msg = $e->getMessage();
            $method = __METHOD__;
            Log::error("{$method}|MSG:{$error_msg}|从库获取modles失败");
            $res = null;
        }
        return $res;
    }



    /**
     * --------------------------STAT INFOS------------------------------------
     */
    /**
     * 获取modle数量
     */
    public function get_size($redis = null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $classname = get_class($this);
        // classname-relateclassname#{pk}#count
        $key = strtoupper(sprintf(self::$MODEL_CNT_KEY, $classname));
        $cnt = $redis->get($key);
        if(is_null($cnt)){
            //初始化 cache
            $res = $this->get_size_db();
            if($res<0){
                $errmsg = $this->error."库中获取数量失败";
                $this->error = $errmsg;
                $method = __METHOD__;
                Log::error("{$method}|$errmsg");
            }else{
                $redis->set($key,$res);
            }
        }else{
            $res = $cnt;
        }
        return $res;
    }


    /**
     * 获取 model size
     * @return bool
     */
    public function get_size_db(){
        try{
            $res = DB::table($this->table)->count();
        }catch(Exception $e){
            $error_msg = $e->getMessage();
            $method = __METHOD__;
            $errmsg = "MSG:{$error_msg}|初始化post-term关系缓存失败";
            $this->error = $errmsg;
            Log::error("{$method}|$errmsg");
            $res = -1;
        }
        return $res;
    }

    /**
     * size 加1
     * @param null $redis
     */
    public function incr_size($redis = null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $classname = get_class($this);
        // classname-relateclassname#{pk}#count
        $key = strtoupper(sprintf(self::$MODEL_CNT_KEY, $classname));
//        $exist = $redis->EXISTS($key);
        if( $redis->EXISTS($key) ){
            $res = $redis->incr($key);
        }else{
            //不存在则初始化
            $cnt = $this->get_size_db();
            if($cnt>=0){
                $res = $redis->INCRBY($key,$cnt);
            }else{
                $res = $cnt;
            }
        }
        return $res;
    }

    /**
     * size 减1
     * @param null $redis
     * @return bool|int
     */
    public function decr_size($redis = null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $classname = get_class($this);
        $key = strtoupper(sprintf(self::$MODEL_CNT_KEY, $classname));
        $init = false;
        if( $redis->EXISTS($key) ){
            $res = $redis->decr($key);
            if($res<0){
                $init = true;
            }
        }else{
            //不存在则初始化
            $init = true;
        }
        if($init) {
            $cnt_db = $this->get_size_db();
            if ($cnt_db > 0) {
                $redis->decrby($key, $cnt_db);
                $res = $cnt_db;
            }else if($cnt_db == 0) {
                $res = -1;
                $method = __METHOD__;
                $errmsg = "$key =0，can't decr";
                $this->error = $errmsg;
                Log::error("{$method}| $errmsg");
            }else{
                $res = $cnt_db;
                $method = __METHOD__;
                $errmsg = "数据库异常";
                $this->error = $errmsg;
                Log::error("{$method}|$errmsg");
            }
        }
        return $res;
    }


    /**
     * 获取关联数量
     * @param $relateclassname
     * @param $pk
     * @param null $redis
     * @return int
     */
    public function get_relate_count($relateclassname,$pk,$redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $classname = get_class($this);
        // classname-relateclassname#{pk}#count
        $key = strtoupper(sprintf(self::$CNT_KEY, $classname, $relateclassname, $pk));
        $cnt = $redis->get($key);
        if(is_null($cnt)){
            //初始化 cache
            $res = $this->get_relate_count_db($classname,$relateclassname,$pk);
            if($res<0){
                $errmsg = "初始化count失败";
                $this->error = $errmsg;
                $method = __METHOD__;
                Log::error("{$method}|$errmsg");
            }else{
                $redis->set($key,$res);
            }
        }else{
            $res = $cnt;
        }
        return $res;
    }

    /**
     * 关联计数器 减1，值为负数，报错
     * @param $relateclassname
     * @param $pk
     * @param null $redis
     */
    public function decr_relate_count($relateclassname,$pk,$redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $classname = get_class($this);
        // classname-relateclassname#{pk}#count
        $key = strtoupper(sprintf(self::$CNT_KEY, $classname, $relateclassname, $pk));
//        $exist = $redis->EXISTS($key);
        $init = false;
        if( $redis->EXISTS($key) ){
            $res = $redis->decr($key);
            if($res<0){
                $init = true;
            }
        }else{
            //不存在则初始化
            $init = true;
//            $cnt = $this->get_relate_count_db($classname,$relateclassname,$redis);
//            if($cnt>0){
//                $res = $redis->decrby($key,$cnt);
//            }else{
//                $res = $cnt;
//                $method = __METHOD__;
//                Log::error("{$method}|{$relateclassname} error");
//            }
        }
        if($init) {
            $cnt_db = $this->get_relate_count_db($classname, $relateclassname, $redis);
            if ($cnt_db > 0) {
                $redis->decrby($key, $cnt_db);
                $res = $cnt_db;
            }else if($cnt_db == 0) {
                $res = -1;
                $method = __METHOD__;
                $errmsg = "$key =0，can't decr";
                $this->error = $errmsg;
                Log::error("{$method}| $errmsg");
            }else{
                $res = $cnt_db;
                $method = __METHOD__;
                $errmsg = "{$relateclassname} error";
                $this->error = $errmsg;
                Log::error("{$method}|$errmsg ");
            }
        }
        return $res;
    }

    public function incr_relate_count($relateclassname,$pk,$redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $classname = get_class($this);
        // classname-relateclassname#{pk}#count
        $key = strtoupper(sprintf(self::$CNT_KEY, $classname, $relateclassname, $pk));
//        $exist = $redis->EXISTS($key);
        if( $redis->EXISTS($key) ){
            $res = $redis->incr($key);
        }else{
            //不存在则初始化
            $cnt = $this->get_relate_count_db($classname,$relateclassname,$redis);
            if($cnt>=0){
                $res = $redis->INCRBY($key,$cnt);
            }else{
                $res = $cnt;
            }
        }
        return $res;
    }

    public function get_relate_count_db($classname,$relateclassname,$pk){
        $res = 0;
        if(class_exists($relateclassname)){
            $model = null;
            $get_cnt_eval = "\$model = $classname::find($pk);";
            eval($get_cnt_eval);
            if(!is_null($model)){
                $relate_models = null;
                $relate_models_eval = "\$relate_models = \$model->{$relateclassname}s;";
//                echo $relate_models_eval."\n";
                eval($relate_models_eval);
                if( !is_null($relate_models)){
                    $res = $relate_models->count();
                }
            }
        }else{
            $res = -1;
        }
        return $res;

    }




    /**
     * 清理redis model缓存
     * @param null $redis
     * @return bool
     */
    public function clear_model_cache($redis= null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $res = false;
        $pk_set = $this->get_pk_set($redis);
        if(is_array($pk_set) && count($pk_set)>0){
            foreach($pk_set as $pk){
                $key = sprintf(self::$MODEL_KEY,$this->primaryKey,$pk);
                $redis->del($key);
                $res = true;
            }
        }
        return $res;
    }













}