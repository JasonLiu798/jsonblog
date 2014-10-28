<?php

class ImgController extends BaseController {
	
	private static $img_pr;
	public function __construct(){
		if(!isset( self::$img_pr)){
			self::$img_pr = new ImageProcessor();
		}
	}
	
	/**
	 * 上传博文图片
	 * @return string
	 */
	public function post_img_upload(){
		$uploaddir = Constant::get_upload_img_dir();
		$editor = $_POST['editor'];
		$upload_file_name = $_FILES['uploadimg']['name'];
		$upload_file = $uploaddir.basename( $upload_file_name );//原文件名
		Log::info( 'IMG OLD NAME:'.$upload_file );
		Log::info( 'IMG TMP:'.$_FILES['uploadimg']['tmp_name'] );
		if (!move_uploaded_file($_FILES['uploadimg']['tmp_name'], $upload_file)) {
			//echo "File is valid, and was successfully uploaded.\n";
			//} else {
			Log::info('Possible file upload attack!');
		}
		$image_type = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png', 'image/bmp');
		$show_img_script='<script>';
		
		$img_url = url().Constant::$UPLOAD_IMG_DIR.$_FILES['uploadimg']['name'];
		if(in_array( $_FILES['uploadimg']['type'], $image_type)) {
			$show_img_script .= 'window.parent.'.$editor.'.insertContent("<img src=\''.$img_url.'\' />");';
		} else {
			$show_img_script .= 'window.parent.'.$editor.'.insertContent("<a href=\''.$img_url.'\'>'.$upload_file_name.'</a>");';
		}
		$show_img_script .= 'window.parent.'.$editor.'.plugins.uploadimg.finish();';
		$show_img_script .= '</script>';
		return $show_img_script;
	}
	
	/**
	 * 上传封面图片，转化图片为2:1，回传url,iid
	 * @return unknown
	 */
	public function post_cover_upload(){
		$sess_user_json = Session::get('user','default');
		if( strcmp($sess_user_json, 'default') == 0 ){
			// to error page
			$msg = '未登录，或登录超时，请重新上传';
			//Redirect::action('UserController@login');
			$user_id = 0;
		}else{
			$user_id = json_decode($sess_user_json)->uid;
		}
		$uploaddir = Constant::get_upload_img_dir();
		$upload_file_name = md5( time().$_FILES['up_cover_img_file']['name'] );//generate hash file name
		$upload_img_name = $_FILES['up_cover_img_file']['name'];
		$upload_file_path = $uploaddir.basename( $upload_file_name );//原文件 path+文件名
		Log::info( 'IMG PATH:'.$upload_file_path );
		//Log::info( 'IMG TMP NAME:'.$_FILES['up_cover_img_file']['tmp_name'] );
		$res_msg="";
		if (!move_uploaded_file($_FILES['up_cover_img_file']['tmp_name'], $upload_file_path)) {
			$res_msg = array('error'=>'上传失败!');
		}else{
			$img_type = self::$img_pr->is_image_file( $upload_file_path );
			if( strlen($img_type) == 0 ) {
				$res_msg = array('error'=>"非图片文件！"); 
			}else{
				//转换图片为2:1，如小于，则扩大
				$res = self::$img_pr->make_image2rate( $upload_file_path , 2 , $img_type );
				//出错信息
				if( is_string($res ) ){
					$res_msg = array('error'=>$res);
				}else{
					//转换正常
					$img_url = url().Constant::$UPLOAD_IMG_DIR.$upload_file_name;//$_FILES['uploadimg']['name'];
					Log::info('IMG URL:'.$img_url);
					$img_size = filesize($upload_file_path);
					//获取图片宽高
					$res_arr = self::$img_pr->get_width_height_size_nocheck($upload_file_path);
					if( is_string($res_arr ) ){
						$res_msg = array('error'=>$res_arr);
					}else{
						//存库
						//save_img($img_name,$img_url,$img_path,$uid,$pid,$width,$height,$size){
						$iid = PostImage::save_img( $upload_img_name , $upload_file_name , $img_type, $user_id, $res_arr[0], $res_arr[1],$img_size);//暂时不关联blog,pid=0保存博文后关联
						$res_msg = array('msg'=>'上传成功！','url' => $img_url,'iid'=>$iid );
					}//end of get w,h
				}//end of coverate 2:1
			}//end of type test
		}
		$response = Response::make( json_encode( $res_msg ) );
		$response->header('Content-Type', 'text/html');
		return $response;
	}
	
	/**
	 * 图片剪裁
	 * v1.1 生成新文件名，否则会造成浏览器加载缓存或js实现图片剪裁后图片展示
	 * @return unknown
	 */
	public function post_cover_cut(){
		$x = Input::get('x');
		$y = Input::get('y');
		$w = Input::get('w');
		$h = Input::get('h');
		$img_src_name = Input::get('cover_img_name');
		Log::info('CutImg:'.$img_src_name.'['.$x.','.$y.'],['.$w.','.$h.']');
		
		$uploaddir = Constant::get_upload_img_dir();
		$src_img_pathname = $uploaddir.$img_src_name;
		$img_type = self::$img_pr->is_image_file( $src_img_pathname );
		if( strlen($img_type) == 0 ) {
			$res_msg = array('error'=>"非图片文件！");
		}else{
			$res = self::$img_pr->cut_image( $uploaddir, $img_src_name, $img_type, $x,$y,$w,$h);
			if(!$res[0]){
				$res_msg = array('error'=>$res[1]);
			}else{
				$img_url = url().Constant::$UPLOAD_IMG_DIR.$res[1];
				//$res_arr = self::$img_pr->get_width_height_size_nocheck($uploaddir.$res[1]);
				$res_msg = array('msg'=>'上传成功！','url' => $img_url);
				Log::info( 'Cutted Img URL:'.$img_url );
			}
		}
		$response = Response::make( json_encode( $res_msg ) );
		$response->header('Content-Type', 'text/html');
		return $response;
	}
	
	/**
	 * 更新图片信息至数据库
	 */
	public function post_cover_save(){
		//$img_url = Input::get('img_url');
		$sess_user_json = Session::get('user','default');
		if( strcmp($sess_user_json, 'default') == 0 ){
			// to error page
			$msg = '未登录，或登录超时，草稿已保存，请重新登录后再进行编辑';
			//Redirect::action('UserController@login');
			$user_id = 0;
		}else{
			$user_id = json_decode($sess_user_json)->uid;
		}
		$iid = Input::get('iid');
		if( PostImage::chk_exist($iid)!=1 ){
			//图片不存在
			$res_msg = '图片不存在';
		}else{
			$img_name = Input::get('img_name');
			$img_path = Constant::get_upload_img_dir().$img_name;
			//$img_url = url().Constant::$UPLOAD_IMG_DIR.$img_name;
			$res_arr = self::$img_pr->get_width_height_size_nocheck( $img_path );
			Log::info('Save Img:'.$img_path);
			if( is_string($res_arr ) ){
				$res_msg = array( 'error' => $res_arr );
			}else{
				$img_size = filesize( $img_path );
				//update_cut_img($iid,$filename,$width,$height,$size)
				PostImage::update_cut_img( $iid, $img_name, $res_arr[0], $res_arr[1], $img_size);
				$res_msg = array('msg'=>'封面保存成功！');
			}
		}
		$response = Response::make( json_encode( $res_msg ) );
		$response->header('Content-Type', 'text/html');
		return $response;
		
	}
	
	
	//$image_type = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png', 'image/bmp');
				
	
}
