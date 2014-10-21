<?php

class ImageProcessor {

	
	
	/**
	 * 扩大图像至指定比例
	 * 正常返回true，其他返回错误信息
	 */
public function make_image2rate($file_path,$rate,$img_type){
		if($rate <= 0){
			return "比率数字必须大于等于零！";
		}
		/* 转移至外部检测，方便获取图片类型
		$img_type = $this->is_image_file( $file_path );
		if( strlen($img_type) == 0 ) {
			return "非图片文件！";
		}
		*/
		$src = file_get_contents($file_path);
		// is_image_file->至少有两个字节，不用判空$src
		$error_msg = "";
        $img_src = ImageCreateFromString($src);
        if( $img_src== false){
        	$error_msg = "创建原图片资源失败！";
        }else{
        	$img_src_w = imagesx($img_src);
			$img_src_h = imagesy($img_src);
			//echo 'size:'.$img_w.'x'.$img_h;
			if($img_src_w/$img_src_h == $rate){
				return $file_path;
			}else if ($img_src_w/$img_src_h >$rate){
				//宽高比大，增加高度
				$des_w = $img_src_w;
				$des_h = $img_src_w/$rate;
			}else if ($img_src_w/$img_src_h <$rate){
				//宽高比小，增加宽度
				$des_w = $img_src_h*$rate;
				$des_h = $img_src_h;
			}
			//echo 'des:'.$des_w.'x'.$des_h."\n";
			$img_des = $this->create_image_fill_bg($des_w,$des_h,'white');
			if( is_string($img_des)){
				$error_msg = $img_des;
			}else{
				if( !imagecopyresized( $img_des, $img_src, 0,0, 0,0, $img_src_w ,$img_src_h, $img_src_w,$img_src_h  )){
					$error_msg = "拷贝图片失败！";
				}else{
					$falid = false;
					if( $img_type === 'png'){
						if( !imagepng( $img_des, $file_path ) ){
							$error_msg = "创建png图片失败！";
						}
					}else if ($img_type === 'gif'){
						if( !imagegif( $img_des, $file_path ) ){
							$error_msg = "创建gif图片失败！";
						}
					}else{
						if( !imagejpeg( $img_des, $file_path ) ){
							$error_msg = "创建jpeg图片失败！";
						}
					}
				}//end of create
			}
			imagedestroy($img_des);
			imagedestroy($img_src);
			if( strlen($error_msg) >0 ){
				return $error_msg;
			}
        }
		return true;
	}
	
	
	/**
	 * 切割图片 x,y->w,h，保存至$file_path.'_cover'
	 * @param unknown $file_path
	 * @param int $x
	 * @param int $y
	 * @param unknown $w
	 * @param unknown $h
	 * @return string
	 */
	public function cut_image($file_path,$x,$y,$w,$h){
		$img_type = $this->is_image_file( $file_path );
		if( strlen($img_type) == 0 ) {
			return "非图片文件！";
		}
		$src = file_get_contents($file_path);
        $img_src = @ImageCreateFromString($src);
		$img_src_w = imagesx($img_src);
		$img_src_h = imagesy($img_src);
		$des_w = 600;
		$des_h = 300;
		if( $x> $img_src_w || $y > $img_src_h ){
			imagedestroy($img_src);
			return "裁剪图片超出范围！";
		}
		//缩小范围至边界
		if( $w-$x>$img_src_w ){
			$w = $img_src_w-$x;
		}
		if(  $h-$y> $img_src_h){
			$h = $img_src_h-$y;
		}
		$sx = round($img_src_w/$des_w * $x);
		$sy = round($img_src_h/$des_h * $y);
		$sw = round($img_src_w/$des_w * $w);
		$sh = round($img_src_h/$des_h * $h);
		Log::info('IMG W:'.$img_src_w.','.$img_src_h);
		Log::info('AF CC:['.$sx.','.$sy.'],['.$sw.','.$sh.']');
		$error_msg = "";
		$img_des = $this->create_image_fill_bg($des_w,$des_h,'white');
		//$img_des = imagecreatetruecolor($des_w,$des_h);
		if( is_string($img_des)){
			$error_msg = $img_des;
		}else{
			if( imagecopyresized( $img_des, $img_src, 0,0, $sx,$sy, $des_w,$des_h, $sw,$sh  )==false ){
				$error_msg =  "拷贝图片失败！";
			}else{
				$img_des_path =  $file_path.'_cut';
				if( $img_type === 'png'){
					if( !imagepng( $img_des, $img_des_path ) ){
						$error_msg = "创建png图片失败！";
					}
				}else if($img_type ==='gif'){
					if( !imagegif($img_des , $img_des_path )){
						$error_msg = "创建gif图片失败！";
					}
				}else{
					if( !imagejpeg($img_des , $img_des_path ) ){
						$error_msg = "创建jpeg图片失败！";
					}
				}
			}//end of create
		}
		imagedestroy($img_des);
		imagedestroy($img_src);
		if( strlen($error_msg)>0 ){
			return $error_msg;
		}
		return true;
	}

	
	

	/**
	 * ------------------------TOOL FUNCTIONS------------------------
	 */
	/**
	 * 不判断文件类型，获取图片width,height
	 * @param unknown $file_path
	 * @return string
	 */
	public function get_width_height_size_nocheck($file_path){
		$src = file_get_contents($file_path);
		// is_image_file->至少有两个字节，不用判空$src
		$error_msg = "";
		$img_src = ImageCreateFromString($src);
		if( $img_src == false){
			$error_msg = "创建原图片资源失败！";
		}else{
			$img_src_w = imagesx($img_src);
			$img_src_h = imagesy($img_src);
		}
		if(strlen($error_msg) >0 ){
			return $error_msg;
		}
		return array($img_src_w,$img_src_h);
	}
	/**
	 * 判断文件类型，获取图片width,height
	 * @param unknown $file_path
	 * @return string
	 */
	public function get_width_height_size($file_path , $img_type){
		$img_type = $this->is_image_file( $file_path );
		if( strlen($img_type) == 0 ) {
			return "非图片文件！";
		}
		$src = file_get_contents($file_path);
		// is_image_file->至少有两个字节，不用判空$src
		$error_msg = "";
		$img_src = ImageCreateFromString($src);
		if( $img_src== false){
			$error_msg = "创建原图片资源失败！";
		}else{
			$img_src_w = imagesx($img_src);
			$img_src_h = imagesy($img_src);
		}
		if(strlen($error_msg) >0 ){
			return $error_msg;
		}
		return $array($img_src_w,$img_src_h);
	}
	
	
	/**
	 * 创建 image resource var
	 */
	public function create_image_fill_bg($w,$h,$color){
		$error_msg = "";
		if($color==='white'){
			$r=$g=$b=0xff;
		}else if( $color === 'black'){
			$r=$g=$b=0;
		}else{
			$r=$g=$b=0xff;
		}
		$img_des = imagecreatetruecolor($w,$h);
		if( $img_des == false ){
			$error_msg = "创建目标图片失败！";
		}else{
			$white = imagecolorallocate($img_des, $r, $g, $b);// pass black
			if( $white < 0 ){
				$error_msg = "分配颜色失败！";
			}else{
				if( !imagefill($img_des ,0,0, $white )){
					$error_msg = "填充背景失败！";
				}
			}
		}
		if( strlen($error_msg) >0 ){
			return $error_msg;
		}
		return $img_des;
	}
	
	
	
	
	
	/**
	 * 
	 * @param unknown $file_path
	 * @return unknown
	 */
	public function is_image_file($file_path){
		$type = $this->get_file_type($file_path);
		if($type === 'jpg'|| $type === 'gif' || $type=== 'png' || $type ==='bmp' ){
			return $type;
		}
		return $type;
	}

	/**
	 * 获取文件类型
	 * @param unknown $file_path
	 * @return string
	 */
	public function get_file_type($file_path){
		//echo "File size:".filesize($file_path);
		if(filesize($file_path) <2){
			return "unknown";
		}
		$file = fopen($file_path , "rb");
		$bin = fread($file, 2); //只读2字节
		//echo 'bin:'.$bin;
		$strInfo = @unpack("C2chars", $bin);

		$typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
		fclose($file);
		$fileType = '';

		//echo 'Type code:'.$typeCode;
		switch ($typeCode)
		{
			case 7790:
				$fileType = 'exe';
				break;
			case 7784:
				$fileType = 'midi';
				break;
			case 8297:
				$fileType = 'rar';
				break;
			case 255216:
				$fileType = 'jpg';
				break;
			case 7173:
				$fileType = 'gif';
				break;
			case 6677:
				$fileType = 'bmp';
				break;
			case 13780:
				$fileType = 'png';
				break;
			default:
				$fileType = 'unknown';
		}
		//echo 'file type:'.$fileType;
		return $fileType;
	}
	
	public function change_url2path($url){
		if(strlen($url)<=0){
			return "";
		}else{
			
		}
	}
	
	
	/**
	 * make image of 600*300
	 *
	 public function make_cover_image($file_path){
	 //$t = new ThumbHandler();
	 //$t->setSrcImg("/Library/WebServer/Documents/lblog/public/upload/img/AAA.png");
	 if( !$this->is_image_file( $file_path )) {
	 return "非图片文件！";
	 }
	 //echo $type;
	 //$img = null;
	 $src = file_get_contents($file_path);
	 if(empty($src)){
	 return "图片源为空！";
	 }
	 $img_src = @ImageCreateFromString($src);
	 $img_src_w = imagesx($img_src);
	 $img_src_h = imagesy($img_src);
	 echo 'size:'.$img_w.'x'.$img_h;
	 // $img_src_w = 200;//->600,  600/800 * 800=600
	 // $img_src_h = 600;// -> tmp_h=tmp=600  ,tmp_h=300,tmp_w = 300/600 *
	 $des_w = 600;//600x300
	 $des_h = 300;
	
	 $tmp_w = 0;
	 $tmp_h = 0;
	 if($img_src_w>$des_w){//宽度超标，缩小宽度，同比例缩小高度
	 $tmp_w = $des_w;//宽度缩小到目标宽度
	 $tmp_h = $tmp_w/$img_src_w * $img_src_h;//同比例缩小高度
	 //echo 'wF:'.$tmp_w.'x'.$tmp_h;
	 if($tmp_h > $des_h ){//高度缩小后，仍然超过目标高度，缩小高度至目标值，并再次缩小宽度
	 $tmp = $tmp_h;
	 $tmp_h = $des_h;//高度缩小到目标高度
	 $tmp_w = $tmp_h/$tmp * $tmp_w;
	 //echo 'wS:'.$tmp_w.'x'.$tmp_h;
	 }
	 }else if( $img_src_h > $des_h ){//高度超标
	 $tmp_h = $des_h;
	 $tmp_w = $tmp_h/$img_src_h * $img_src_w;//同比例缩小宽度
	 //echo 'hF:'.$tmp_w.'x'.$tmp_h;
	 if($tmp_w > $des_w ){//宽度缩小后，仍然超过目标宽度
	 $tmp = $tmp_w;
	 $tmp_w = $des_w;
	 $tmp_h = $tmp_w/$tmp * $tmp_h;
	 //echo 'hS:'.$tmp_w.'x'.$tmp_h;
	 }
	 }
	 //echo 'final:'.$tmp_w.'x'.$tmp_h."\n";
	 //创建目标图片
	 $img_des = imagecreate($des_w,$des_h);
	 if( imagecolorallocate($img_des, 0xFF, 0xFF, 0xFF)<0 ){
	 imagedestroy($img_des);
	 imagedestroy($img_src);
	 return "分配颜色失败";
	 }
	 imagecopyresized( $img_des, $img_src, 0,0, 0,0, $tmp_w,$tmp_h, $img_src_w,$img_src_h  );
	 //header('Content-Type: image/jpeg');
	 $img_des_path =  $file_path.'_cover';
	 if( imagejpeg( $img_des, $file_path.'_cover' )==false ){
	 imagedestroy($img_des);
	 imagedestroy($img_src);
	 return "创建图片失败";
	 }
	 imagedestroy($img_des);
	 imagedestroy($img_src);
	 return $img_des_path;
	 }
	 */
}
