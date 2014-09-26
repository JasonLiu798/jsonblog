<?php

class ImgController extends BaseController {
	
	public function upload(){
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
	
	
}
