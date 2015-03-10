<?php
class Constant {

	public static $PAGESIZE = 5;

	public static $NAV_IDX = 'index';
	public static $NAV_MSG = 'msg';
	public static $NAV_ABOUT = 'about';
	public static $NAV_NONE = '';
	public static $NAV_ADMIN = 'ADMIN';

	public static $ADMIN_PAGESIZE = 5;
	public static $REG_YEAR_MONTH = '/[0-9]{4}-((0[1-9]|(10|11|12)))/';
	public static $DIGIT = '/[0-9]+/';

	public static $POST_INDEX_CUT_SIZE = 500;
	public static $POST_ADMIN_CUT_SIZE = 25;
	public static $UTF_8 = 'utf-8';

	/**
	 * DB use
	 */
	public static $CONDITION_COL_NAME = 'CONDITION_COL';
	public static $TS_COL_NAME = 'TS_COL';

	/**
	 * comment type
	 */
//	public static $COMM_NORMAL = 0;
//	public static $COMM_REPLY =

	public static $COMM_UNREAD = 0;
	public static $COMM_READ = 1;

	public static $TERM_CATEGORY = 'category';
	public static $TERM_TAG = 'post_tag';
	public static $TERM_TAG_NOPARENT = 0;

	//public static $UPLOAD_IMG_DIR = dirname(__FILE__).'../';
	public static function get_upload_img_dir() {
		return dirname(__FILE__) . '/../../public/upload/img/';
	}

	public static $SEARCH_SERVER_IP = '127.0.0.1';
	public static $SEARCH_SERVER_PORT = 5050;

	public static $UPLOAD_IMG_DIR = '/upload/img/';

	public static $SEARCH_FUNC = "1#%s,%s,%s\n";
	public static $ADDONE_FUNC = "2#%s\n";

	public static $REIDX_FUNC = 0x11;

	public static $MESSAGE_POST_ID = 0;
	public static $BLOGGERID = 1;


	//POST statistics key
	public static $POST_STAT_KEY = 'POST_STAT';
	public static $CAT_STAT_KEY = 'CAT_STAT';
	public static $TAG_STAT_KEY = 'TAG_STAT';

	public static $CAT_KEY = 'CAT_LIST';
	public static $TAG_KEY = 'TAG_LIST';

	//POST status
	public static $POST_PUBLISH = 'publish';
	public static $POST_DRAFT = 'draft';



	//未分类
	public static $NO_CATEGORY_ID = 1;
	/**
	 * error codes
	 * @var [type]
	 */
	public static $NOLOGIN = 10;
	public static $TAG_EXIST = 50;


}