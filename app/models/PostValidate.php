<?php

/**
 * Created by PhpStorm.
 * User: liujianlong
 * Date: 15/1/27
 * Time: 下午3:44
 */
class PostValidate extends Post{
    public static function creat_chk($title,$content){
        $create_validator = Validator::make(
            array(
                '文章标题' => $title,
                '文章内容' => $content

            ),
            array(
                '文章标题' => 'between:6,32',
                '文章内容' => 'required|between:6,16'
                )
        );
    }

    public static function update_chk($title,$content){

    }

}