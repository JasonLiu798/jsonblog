<?php

class PostImage extends Eloquent  {
	//index image 600x360
	protected $table = 'postimages';
	
	public static function  save_img($img_name, $filename, $img_type,$uid,$pid,$width,$height,$size){
		date_default_timezone_set("Asia/Shanghai");
		$add_date = date('Y-m-d H:i:s',time());
		try {
			DB::table('postimages')->insert(
				array(
					'name'=>$img_name,
					'filename'=>$filename,
					'uid'=>$uid,
					'filetype'=>$img_type,
					'pid'=>$pid,
					'width'=>$width,
					'height'=>$height,
					'size'=>$size,
					'add_date'=>$add_date
				)
			);
		}catch(Exception $e){
			Log::info($e->$message);
		}
		
		$get_last_img_id_sql = "SELECT LAST_INSERT_ID() iid";
		try {
			$img_id = DB::select( $get_last_img_id_sql )[0]->iid;
			Log::info('CreateImg:'.$img_id);
		}catch(Exception $e){
			Log::info($e->$message);
		}
		return $img_id;
	}
	/**
	 * 更新
	 * @param unknown $iid
	 * @param unknown $filename
	 * @param unknown $width
	 * @param unknown $height
	 * @param unknown $size
	 */
	public static function update_cut_img($iid,$filename,$width,$height,$size){
		DB::table('postimages')->where('iid', $iid)->
			update(
				array('filename' => $filename, 'width'=> $width, 'height'=>$height, 'size'=>$size ));
	}
	
	public static function chk_exist($iid){
		$cnt = DB::table('postimages')->where('iid','=',$iid)->count();
		return $cnt;
	}
	
	
}