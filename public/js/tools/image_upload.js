$(document).ready(function(){
    var jcrop_api;
    var iid;
    var img_name_before_cut_url;
    var img_name_after_cut_url;
    var is_cut=false;
    var is_upload=false;
    var uploading = false;
    var main_space = "http://"+window.location.host+"/img/space450x250.jpg";
	var prev_space = "http://"+window.location.host+"/img/space250x150.jpg";
	
    /**
     * 上传封面图片
     */
    $('#upload_image').click(function(){
    	if( uploading ){
    		alert('正在上传！');
    		return;
    	}
    	
    	if(jcrop_api!=null){
    		console.log('upload destory jcrop!');
    		jcrop_api.destroy();
    	}
    	var upfile = $('#up_image_file').val();
    	if( upfile == ''|| upfile == null){
    		alert("请选择上传文件");
    		return;
    	}
    	//check file type by extend name
    	if( check_file_type(upfile)<0 ){
    		alert('图片类型不符合要求，只能上传jpg,gif,png,bmp类型图片！');
            return;
    	}
        uploading = true;
    	img_name_before_cut_url=null;
    	img_name_after_cut_url=null;
    	iid=null;
    	is_cut = false;
    	ajaxFileUpload();
    });
    
    /**
     * 裁切图片，发送裁切尺寸至服务器，接收返回图片url并显示，不更新库
     */
    $('#cut_img').click(function(){
    	if( !is_upload ){
    		alert('图片未上传！');
    		return;
    	}
    	$('#cutted').val("true");//设置为剪切过，使用*_cover，否则使用原文件名
    	is_cut = true;
    	if(jcrop_api != null){
    		console.log('af cut,destory jcrop');
    		jcrop_api.disable();
    		jcrop_api.destroy();
    	}
    	var img_name = img_name_before_cut_url.substring( img_name_before_cut_url.lastIndexOf('/')+1 ) ;
    	console.log('Cut img,img name:' + img_name );
    	/*
    	var main_img = $('#upload_main_img');
    	var preview_img = $('#img_preview');
    	var upload_img_pane = $('upload_img_pane');
    	var img_act_w = getNaturalWidth( main_img );
    	var img_act_h = getNaturalHeight( main_img );
    	console.log('img act size:'+img_act_w+'x'+img_act_h); 
    	var w = parseInt( $('#w').val());
    	var h = parseInt( $('#h').val());
    	var x = parseInt( $('#x').val());
    	var y = parseInt( $('#y').val());
    	console.log('xy-wh:'+x+','+y+'-'+ w +'x'+ h);
    	var pw = parseInt( upload_img_pane.attr("width"));
    	var ph = parseInt( upload_img_pane.attr("height"));
    	console.log('pane pw ph:'+pw+','+ph );
    	var fw = w/pw * pw;
    	var fh = h/ph * ph;
    	console.log('img l size:'+ fw +'x'+ fh);
    	var fx = x/pw * pw;
    	var fy = y/ph * ph;
    	main_img.css({
    		width:  Math.round( fw ) + 'px',
    		height: Math.round( fh ) + 'px',
    		marginLeft: '-' + Math.round( fx ) + 'px',
    		marginTop:  '-' + Math.round( fy ) + 'px'
    	}).show();
    	preview_img.attr('src',prev_space);
    	*/
    	$.ajax({
            url: "http://"+window.location.host+"/img/post/cover/cut",
            async: false,
            dataType:'json',
            data: {
            	x:$('#x').val(),
            	y:$('#y').val(),
            	w:$('#w').val(),
            	h:$('#h').val(),
            	cover_img_name:img_name },
            success: function (data) {
            	//var img_url = data.url;
            	console.log('get cut img url:'+data.url);
            	$("#upload_main_img").attr("src", data.url );
                $("#upload_img_preview").attr("src", prev_space );
                var img_name = data.url.substring( data.url.lastIndexOf('/')+1 );
                //img_name_before_cut_url='';
            	img_name_after_cut_url = data.url;
            	
                console.log('af cut img name:'+img_name);
                //$('#cover_img_name').val( img_name );
                if ( typeof (data.error) != 'undefined' ) {
                    if (data.error != '') {
                        alert("出错了:"+data.error);
                    } else {
                        alert("出错了"+data.msg);
                    }
                }
            },
            error: function (msg) {
                alert(msg.responseText);
            }
        });
    	
    });
    
    /**
     * 撤销裁剪图片
     */
    $('#cut_img_back').click(function(){
    	//img_name_before_cut
    	if(!is_cut){
    		alert('图片未裁剪！');
    	}
    	if(!is_upload){
    		alert('图片未上传！');
    	}
    	is_cut = false;
    	$("#upload_main_img").attr("src", img_name_before_cut_url );
    	$("#upload_img_preview").attr("src", img_name_before_cut_url );
    	
    	img_name_after_cut_url = '';
    	setTimeout(function(){
        	jcrop_api = add_img_processor();
    	},500);
    });
    
    /**
     * 保存图片，更新库
     */
    $('#save_img').click(function(){
    	if(!is_upload){
    		alert('图片未上传！');
    	}
    	var final_img_url='';
    	var cut='no';
    	$('#set_cover').val("true");
    	if(is_cut){
    		//已裁切
    		final_img_url = img_name_after_cut_url;
    		cut='no';
    	}else{
    		//未裁切
    		if(jcrop_api != null){
        		console.log('save img,destory jcrop');
        		jcrop_api.disable();
        		jcrop_api.destroy();
        	}
    		final_img_url = img_name_before_cut_url;
    		cut = 'yes';
    	}
    	final_img_name = final_img_url.substring( final_img_url.lastIndexOf('/')+1 );
        //主页面设置图片
        //$("#cover_img_preview_inpage").attr("src",final_img_url);
    	//异步保存
    	$.ajax({
            url: "http://"+window.location.host+"/img/post/cover/save",
            async: false,
            dataType:'json',
            data: {
            	iid:iid,//$('cover_img_id').val(iid) ,
            	img_name:final_img_name
            	},
            success: function (data) {
                if ( typeof (data.error) != 'undefined' ) {
                    if (data.error != '') {
                        alert("出错了:"+data.error);
                    }else {
                        alert("出错了" +data.msg);
                    }
                }
                //alert(data.msg);
            	$("#upload_main_img").attr("src", main_space );
                $("#upload_img_preview").attr("src",  prev_space );

            },
            error: function (msg) {
                alert(msg.responseText);
            }
        });
    	 
    	var main_space = "http://"+window.location.host+"/img/space450x250.jpg";
    	var prev_space = "http://"+window.location.host+"/img/space250x150.jpg";
    	$("#upload_main_img").attr("src", main_space );
    	$("#upload_img_preview").attr("src", prev_space );
    	is_upload=false;
    	is_cut=false;
    	$('#cover_img_diag').modal('hide');//隐藏对话框
    	
    });
    
    /**
     * 取消保存
     */
    $('#cancle_save_img').click(function(){
    	//恢复变量
    	img_name_before_cut_url='';
    	img_name_after_cut_url='';
    	is_upload=false;
    	is_cut=false;
    	var main_space = "http://"+window.location.host+"/img/space450x250.jpg";
    	var prev_space = "http://"+window.location.host+"/img/space250x150.jpg";
    	$("#upload_main_img").attr("src", main_space );
    	$("#upload_img_preview").attr("src", prev_space );
    	$('#cover_img_diag').modal('hide');//隐藏对话框
    });
    
    
    /**
     * TOOL FUNCTION
     */
    /**
     * 检查图片文件扩展名
     */
    function check_file_type(file_name){
    	var res = 0;
    	var imgtype = new Array(".jpg",".gif",".png",".bmp");
    	var upfile_lowcase = file_name.toLowerCase();
    	console.log("up file name:" + upfile_lowcase);
    	var is_img = "";
    	for(i=0;i<imgtype.length;i++){
    		if( upfile_lowcase.indexOf(imgtype[i]) >0){
    			is_img=imgtype[i];
    			break;
    		}
    	}
    	//console.log("is img:"+is_img);
    	if(is_img.length==0){
    		res = -1;
    	}
    	return res;
    }
    
    /**
     * 添加图片剪切控件
     */
    function add_img_processor(){
    	var boundx,boundy,
        $preview = $('#upload_img_preview'),
        xsize = $preview.width(),
        ysize = $preview.height();
    	
///console.log('init',[xsize,ysize]);
		var jcrop_api = $.Jcrop('#upload_main_img',{
			onChange: showPreview,
  	      	onSelect: showPreview,
  	      	onRelease: hidePreview,
  	      	aspectRatio: xsize / ysize
		});
		
		function showPreview(coords){
		    if ( parseInt(coords.w) > 0){
		    	$('#x').val(coords.x);
		    	$('#y').val(coords.y);
		    	$('#w').val(coords.w);
		    	$('#h').val(coords.h);
		    	var $pcnt = $('#preview-pane .preview-container');
		    	var xsize = $pcnt.width();
		    	var ysize = $pcnt.height();
		    	var rx = xsize / coords.w;
		    	var ry = ysize / coords.h;
		    	var bounds = jcrop_api.getBounds();
		    	var boundx = bounds[0];
		    	var boundy = bounds[1];
		    	$preview.css({
		    		width: Math.round(rx * boundx) + 'px',
		    		height: Math.round(ry * boundy) + 'px',
		    		marginLeft: '-' + Math.round(rx * coords.x) + 'px',
		    		marginTop: '-' + Math.round(ry * coords.y) + 'px'
		    	}).show();
		    }
		}
		
		function hidePreview(){
			$preview.stop().fadeOut('fast');
		}
		return jcrop_api;
    }
	
    /**
     * 图片上传
     */
    function ajaxFileUpload() {
    	$.ajaxFileUpload
        (
            {
                url: "http://"+window.location.host+"/admin/image/upload", //用于文件上传的服务器端请求地址
                secureuri: false, //是否需要安全协议，一般设置为false
                fileElementId: 'up_image_file', //文件上传域的ID
                dataType: 'json',//返回值类型
                success:
                function (data, status){
                	console.log("IMG URL:"+data.url);
                    $("#upload_main_img").attr("src", data.url );
                    $("#upload_img_preview").attr("src", data.url );
                    var img_name = data.url.substring( data.url.lastIndexOf('/')+1 );
					console.log('img name:'+img_name);
                    //$('#cover_img_name').val( img_name );
                    img_name_before_cut_url = data.url;
                    img_name_after_cut_url = '';
                    is_upload = true;
                    uploading = false;
                    setTimeout(function(){
                    	jcrop_api = add_img_processor();
                	},500);
                    iid = data.iid;
                    //$('#cover_img_id').val(iid);
                    if (typeof (data.error) != 'undefined') {
                        if (data.error != '') {
                            alert("出错了:"+data.error);
                        } else {
                            alert("出错了"+data.msg);
                        }
                    }
                },
                error: 
                function (data, status, e){
                    alert(e);
                }
            }
        );
    	return false;
    }
    
    
});