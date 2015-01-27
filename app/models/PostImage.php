<?php

class PostImage extends Eloquent {
	//index image 600x360
	protected $table = 'postimages';

	protected $primaryKey = 'iid';

	public $timestamps = false;

	public static function get_images($pagesize) {
		$res = PostImage::paginate($pagesize);
		return $res;
	}

	// public static function chk_exist($iid){
	// 	$img = PostImage::find($iid);
	// 	if(is_null($img)){
	// 		return false;
	// 	}else{
	// 		return true;
	// 	}
	// }

	public function post() {
		return $this->hasOne('Post', 'post_cover_img');
	}

	public static function delete_imgandfile($iid) {
		$img = PostImage::find($iid);

		if (!is_null($img)) {
			$post = $img->post;
			if (!is_null($post)) {
				//有关联post
				$post->post_cover_img = 0;
				if (!$post->save()) {
					throw new Exception("更新博客{$post->ID}与图片{$iid}关系失败");
				}
			}
			if (!PostImage::destroy($iid)) {
				throw new Exception("删除图片{$iid}meta信息失败");
			} else {
				$img_path = Constant::get_upload_img_dir() . $img->filename;
				Log::info('Delete image file:' . $img_path);
				if (file_exists($img_path)) {
					if (!unlink($img_path)) {
						throw new Exception("删除图片{$iid}文件失败");
					}
				} else {
					throw new Exception("图片{$iid}文件不存在");
				}
			}
		} else {
			throw new Exception("图片不存在");
		}
	}

	public static function save_img($img_name, $filename, $img_type, $uid, $width, $height, $size) {
//$pid,
		date_default_timezone_set("Asia/Shanghai");
		$add_date = date('Y-m-d H:i:s', time());
		//try {
		DB::table('postimages')->insert(
			array(
				'name' => $img_name,
				'filename' => $filename,
				'uid' => $uid,
				'filetype' => $img_type,
				//'pid'=>$pid,
				'width' => $width,
				'height' => $height,
				'size' => $size,
				'add_date' => $add_date,
			)
		);
// 		}catch(Exception $e){
		// 			Log::info($e->$message);
		// 		}

		$get_last_img_id_sql = "SELECT LAST_INSERT_ID() iid";
		try {
			$img_id = DB::select($get_last_img_id_sql)[0]->iid;
			Log::info('CreateImg:' . $img_id);
		} catch (Exception $e) {
			Log::info($e->getMessage() );
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
	public static function update_cut_img($iid, $filename, $width, $height, $size) {
		DB::table('postimages')->where('iid', $iid)->
		update(
			array('filename' => $filename, 'width' => $width, 'height' => $height, 'size' => $size)
		);
	}

	public static function chk_exist($iid) {
		$cnt = DB::table('postimages')->where('iid', '=', $iid)->count();
		return $cnt;
	}

	/**
	 * 根据iid获取，图像 path
	 * @param unknown $iid
	 * @return string
	 */
	public static function get_img_path($iid) {
		$img = DB::table('postimages')->select('filename')->where('iid', $iid)->get();
		return Constant::get_upload_img_dir() . $img[0]->filename;
	}

	/**
	 * 根据iid获取图像url
	 * @param unknown $iid
	 * @return string
	 */
	public static function get_img_url($iid) {
		$img = DB::table('postimages')->select('filename')->where('iid', $iid)->get();
		return Constant::get_upload_img_dir() . $img[0]->filename;
	}

	public static function get_img_url_by_name($name) {
//<<<<<<< HEAD
		if (is_null($name)) {
			return null;
		}
//=======
		//>>>>>>> FETCH_HEAD
		//$img = DB::table('postimages')->select('filename')->where('iid',$iid)->get();
		return url() . Constant::$UPLOAD_IMG_DIR . $name;
	}

	/**
	 *
	 * @param unknown $iid
	 */
	public static function get_img_name($iid) {

	}

	public static function delete_img($iid) {
		$img_path = DB::table('postimages')->select('filename')->where('iid', $iid)->get();
		if (file_exists($img_path)) {
			$result = unlink($img_path);
		}
		return $result;
	}

	/**
	 * change post img name to url
	 * @param $post
	 */
	public static function post_img_name2url(&$post){
		if(!is_null($post)){
			if (!is_null($post->post_img_name)) {
				$post->cover_img_url = PostImage::get_img_url_by_name($post->post_img_name);
			} else {
				$post->cover_img_url = null;
			}
		}

	}
}
