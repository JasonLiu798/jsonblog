<?php
/**
 * Created by PhpStorm.
 * User: liujianlong
 * Date: 15/1/23
 * Time: 下午5:07
 */
class Tool{
    public static function PRINT_ARR($arr){
        echo "ARRAY:";
        foreach ($arr as $e) {
            echo $e.',';
        }
        echo "\n";
    }
    public static function ARR2STR($arr){
        $res = "ARRAY:";
        $i = 0;
        $len = count($arr) - 1;
        if($len>0) {

            foreach ($arr as $e) {
                if ($i == $len) {
                    $res .= $e;
                } else {
                    $res .= $e . ',';
                }
                $i++;
            }
        }
        $res.= "\n";
        return $res;
    }
}