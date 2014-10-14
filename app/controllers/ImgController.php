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
	 * 上传封面图片，转化图片为2:1，回传url
	 * @return unknown
	 */
	public function post_cover_upload(){
		$uploaddir = Constant::get_upload_img_dir();
		$upload_file_name = md5( time().$_FILES['up_cover_img_file']['name'] );//generate hash file name
		
		$upload_file = $uploaddir.basename( $upload_file_name );//原文件 path+文件名
		Log::info( 'IMG DESTINATION:'.$upload_file );
		Log::info( 'IMG TMP NAME:'.$_FILES['up_cover_img_file']['tmp_name'] );
		$res_msg="";
		if (!move_uploaded_file($_FILES['up_cover_img_file']['tmp_name'], $upload_file)) {
			//Log::info('Possible file upload attack!');
			$res_msg = array('error'=>'上传失败!');
		}else{
			//转化图片为2:1
			//$img_pr = new ImageProcessor();
			$res = self::$img_pr->make_image2rate( $upload_file, 2);
			//出错信息
			if( is_string($res ) ){
				$res_msg = array('error'=>$res);
				$response = Response::make( json_encode( $res_msg ) );
				$response->header('Content-Type', 'text/html');
				return $response;
			}
			//转化正常
			$img_url = url().Constant::$UPLOAD_IMG_DIR.$upload_file_name;//$_FILES['uploadimg']['name'];
			Log::info('IMG URL:'.$img_url);
			
			$res_msg = array('msg'=>'上传成功！','url' => $img_url);
		}
		$response = Response::make( json_encode( $res_msg ) );
		$response->header('Content-Type', 'text/html');
		return $response;
	}
	
	public function post_cover_cut(){
		$x = Input::get('x');
		$y = Input::get('y');
		$w = Input::get('w');
		$h = Input::get('h');
		$img_name = Input::get('cover_img_name');
		Log::info('CutImg:'.$img_url.'['.$x.','.$y.'],['.$w.','.$h.']');
		$uploaddir = Constant::get_upload_img_dir();
		$src_img_path = $uploaddir.$img_name;
		$res = self::$img_pr->cut_image($src_img_path,$x,$y,$w,$h);
		if( is_string($res) ){
			$res_msg = array( 'error'=>$res );
		}else{
			$img_url = url().Constant::$UPLOAD_IMG_DIR.$img_name.'_cut';
			$res_msg = array('msg'=>'上传成功！','url' => $img_url);
			Log::info( 'Cutted Img URL:'.$img_url );
		}
		$response = Response::make( json_encode( $res_msg ) );
		$response->header('Content-Type', 'text/html');
		return $response;
	}
	
	
	//$image_type = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png', 'image/bmp');
				
	
}
