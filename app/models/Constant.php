<?php
class Constant {
	
	public static $PAGESIZE = 5;

	public static $ADMIN_PAGESIZE = 5;
	public static $REG_YEAR_MONTH = '/[0-9]{4}-((0[1-9]|(10|11|12)))/';
	public static $DIGIT = '/[0-9]+/';
	
	public static $POST_INDEX_CUT_SIZE = 500;
	public static $POST_ADMIN_CUT_SIZE = 25;
	public static $UTF_8 = 'utf-8';
	
	
	public static $COMM_UNREAD = 0;
	public static $COMM_READ = 1;
	
	public static $TERM_CATEGORY = 'category';
	public static $TERM_TAG = 'post_tag';
	public static $TERM_TAG_NOPARENT = 0;
	
	//public static $UPLOAD_IMG_DIR = dirname(__FILE__).'../';
	public static function get_upload_img_dir(){
		return dirname(__FILE__).'/../../public/upload/img/';
	}
	
	public static $UPLOAD_IMG_DIR = '/upload/img/';
}