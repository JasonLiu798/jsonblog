<?php
/**
 * Created by PhpStorm.
 * User: liujianlong
 * Date: 15/1/28
 * Time: 下午6:10
 */
class TermRelationship extends BaseModel
{

    protected $table = 'term_relationships';
    protected $primaryKey = 'term_relationship_id';

    public $timestamps = false;

    private static $POST_TERM_KEY = "P-T#%s";
    private static $TERM_POST_KEY = "T-P#%s";

    public $err;

    /**
     * 关系函数
     * 多对一，termrelationship-term
     * @return mixed
     */
    public function term()
    {
        return $this->belongsTo('Term','term_id','term_id');
    }

    /**
     * 关系函数
     * 多对一，termrealtionship-post
     * @return mixed
     */
    public function post(){
        return $this->belongsTo('Post','object_id','ID');
    }

    public function get_term($pid,$redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }

    }

    /**
     * init post-term set
     * @param null $redis
     * @return bool true success
     */
    public function init_post_term($redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $res = true;
        // P-T#pid
        try{
            $post_term_db = DB::table($this->table)->select('object_id','term_id')->get();
        }catch(Exception $e){
            $error_msg = $e->getMessage();
            $method = __METHOD__;
            Log::error("{$method}|MSG:{$error_msg}|");
            $res = false;
        }

        if( array($post_term_db) && count($post_term_db)>0 ){
            try{
                foreach($post_term_db as $post_term){
                    $key = sprintf( self::$POST_TERM_KEY, $post_term->object_id );
                    $redis->sadd($key, $post_term->term_id );
                }
            }catch(Exception $e){
                $error_msg = $e->getMessage();
                $method = __METHOD__;
                Log::error("{$method}|MSG:{$error_msg}|初始化post-term关系缓存失败");
                $res = false;
            }
        }
        return $res;
    }

    public function add_one_post_term($post_id,$term_id,$redis=null){

    }

    /**
     * 从缓存post-term-id SET中，取所有term-key，如没有则初始化缓存
     * @param $post_id
     * @param null post_id's term array of term_ids
     */
    public function get_post_term_key($post_id,$redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $key = sprintf( self::$POST_TERM_KEY, $post_id );
//        echo $key;
        $post_term_ids = $redis->SMEMBERS($key);
//        var_dump($post_term_ids);
        if( is_null($post_term_ids)|| count($post_term_ids)==0 ){
            //无缓存或Post无标签，查库确认，并添加缓存
            $term_ids = $this->get_post_term_id_from_db($post_id);
//            echo "A\n";
//            var_dump($term_ids);
            $res = array();
            if( is_array($term_ids)&& count($term_ids)>0){
                /**
                 * 缓存无，从DB取，同时初始化缓存
                 */
                foreach($term_ids as $term_id){
//                    $term_id_arr = (array)$term_id;
                    array_push($res, sprintf( Term::$TERMKEY, $term_id->term_id ) );
                    $redis->SADD($key,$term_id->term_id );
                }
            }
        }else{
            $res = array();
            if( is_array($post_term_ids) && count($post_term_ids)>0){
                foreach($post_term_ids as $term_id){
                    array_push($res, sprintf( Term::$TERMKEY, $term_id) );
                }
            }
        }
        return $res;
    }

    /**
     * 从库获取post-term_id
     * @param $post_id
     * @return null array|PKname=>PK
     */
    public function get_post_term_id_from_db($post_id){
        try{
            $res = DB::table($this->table)->select('term_id' )->where('object_id','=',
                $post_id)->get();
        }catch(Exception $e){
            $error_msg = $e->getMessage();
            $method = __METHOD__;
            Log::error("{$method}|MSG:{$error_msg}|获取post's term id失败");
            $res = null;
        }
        return $res;
    }

    /**
     * @param $post_id
     */
    public function get_post_term($post_id,$redis=null){
        if(is_null($redis)){
            $redis = LRedis::connection();
        }
        $term_keys = $this->get_post_term_key($post_id,$redis );
        $res = array();
        if( count($term_keys)>0){
            $terms_cache = $redis->MGET( $term_keys );
            if( count($terms_cache)!= count($term_keys)){
                //需要初始化 term缓存 [TERMKEY -> json term]
                $terms_db = $this->get_post_terms_db($post_id);
                if( is_array($terms_db) && count($terms_db)>0 ){
                    $res = $terms_db;
                    foreach ($terms_db as $term){
                        $key = sprintf(Term::$TERMKEY,$term->term_id);
                        $redis->SET($key, json_encode($term));
                    }
                }
            }
            if( is_array($terms_cache) && count($terms_cache)>0 ){
                foreach($terms_cache as $term_json){
                    $term = json_decode($term_json);
                    if(!is_null($term)){
                        array_push($res,$term);
                    }else{
                        $this->err = "json解码错误";
                    }

                }
            }
        }//else post无term
        return $res;
    }

    /**
     * DB获取post_id's terms
     * @param $post_id
     * @return null
     */
    public function get_post_terms_db($post_id){
        $res = null;
        try{
            $post = Post::find($post_id);
            if($post){
                $res = $post->terms;
            }
        }catch(Exception $e){
            $method = __METHOD__;
            Log::error("{$method}|DB获取{$post_id} 的term实体失败");
        }
        return $res;
    }



    public function init_term_post($redis = null){

    }

}