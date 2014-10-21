
$(document).ready(function(){
	/**
	 * post tag associate
	 */
    $('.tagbox').click(function(){
        $("input[name='post_tag']").focus();
    });
    $('#new_category_alert_box').hide();
    
    /**
     * remove a tag
     */
    $(document).on("click","#newtags span",function(){
    	var remove_tag_id = $(this).attr('value');
    	var remove_tag_name = $(this).attr('name');
    	var idx = $('#post_tag_ids').val().indexOf( remove_tag_id );
    	var remove_tag_len = remove_tag_id.length;
    	var tag_str = $('#post_tag_ids').val();
    	var tag_str_len = tag_str.length; 
    	console.log('Remove tag:'+remove_tag_id +',idx '+idx+',name:'+ remove_tag_name );
    	//console.log(idx);
    	
//		123,56,8
//		first idx=0    	
//		mid [5,6] idx=4,len=8,
//		last [8] idx=7,len=8-1=7,idx-1=6
    	if(idx==0){//first tag
    		$('#post_tag_ids').val( $('#post_tag_ids').val().substring(remove_tag_len+1	) );
    	}else if( idx>0 && idx == tag_str_len -remove_tag_len ){//last tag
    		$('#post_tag_ids').val( tag_str.substring(0, idx - 1 ) );
    	}else if( idx>0 && idx< tag_str_len - remove_tag_len ){//in the middle    		
    		$('#post_tag_ids').val( tag_str.substring(0, idx  - 1 )+ tag_str.substring(idx+remove_tag_len));
    	}else{
    		console.log('remove idx error!');
    	}
        $(this).remove();
    	
    	console.log( 'AF RM TAG_ID:' + $('#post_tag_ids').val() );
    });
    
    /**
     * add a old tag
     */
    $('.old span').click(function(){
        var ids=new Array();
        var txt=$(this).attr('name');
        var value = $(this).attr('value');
        var id=$(this).attr('id');
        $('#newtags .tag').each(function(){
            ids+=$(this).attr('id')+','
        });
        if(ids==''){
            ids=new Array();
        }else{
            ids = ids.split(",");
        }
        if(ids.length>5){
        	$('#post_tag_alert').val('标签最多添加5个哦！');
            return false;
        };
        var exist=$.inArray(id,ids);
        if(exist<0){
            $('#newtags').append('<span id='+id+' name='+txt+' value="'+value+'" class="tag tag_new">'+txt+'&nbsp;X</span>');
            if( $('#post_tag_ids').val().length ==0 ){
            	$('#post_tag_ids').val( value);
            }else{
            	$('#post_tag_ids').val( $('#post_tag_ids').val()+','+value);
            }
            
            console.log( "POST_TAG_IDS:"+ $('#post_tag_ids').val() );
        }
    });
    
    /**
     * add a post tag by input text 
     */
    $('#post_tag').bind('keyup',function(event){
    	if( event.keyCode == 188 ){
    		$('#post_tag_alert').text('标签不能包含英文逗号！');
            return false;
    	}
//    	Chinese input method space  
//    	if( event.keyCode == 32 ){
//    		$('#post_tag_alert').text('标签不能包含空格！');
//            return false;
//    	}
        if( event.keyCode == 13 ){//|| event.keyCode==32){//回车
            var txt=$(this).val();
            if(txt!=''){
            	if(txt.indexOf(',')>=0 ){
            		$('#post_tag_alert').text('标签不能包含英文逗号，请删除逗号后提交！');
            		return false;
            	}
//console.log('space index'+txt.indexOf(' '));
            	if( txt.indexOf(' ')>=0 ){
            		$('#post_tag_alert').text('标签不能包含空格，请删除空格后提交！');
            		return false;
            	}
            	if( txt.length>32){
            		$('#post_tag_alert').text('标签最长为32个字符，请删除多余字符后提交！');
            		return false;
            	}
            	
            	//TAG EXSISTS
                var txts=new Array();
                $('#newtags .tag').each(function(){
                    txts+=$(this).attr('name')+','
                });
                if(txts==''){
                    txts=new Array();
                }else{
                    txts = txts.split(",");
                }
                if(txts.length>5){
                	$('#post_tag_alert').text('标签最多添加5个哦！');
                	return false;
                };
                var exist=$.inArray(txt,txts);
                //NOT EXIST
                if(exist<0){
                	$.ajax({
                        url: "http://"+window.location.host+"/tag/api/create",
                        async: false,
                        data: { new_tag_name: encodeURI(encodeURI(txt)) },
                        success: function (data) {
                        	var tag_id = data.term_id;
            				$('#newtags').append('<span name='+txt+' class="tag tag_new" value="'+tag_id+'">'+txt+'&nbsp;X</span>');
                            //ACTUALLY SUBMIT FORM TEXT
                            if( $('#post_tag_ids').val().length ==0 ){
                            	$('#post_tag_ids').val( tag_id);
                            }else{
                            	$('#post_tag_ids').val( $('#post_tag_ids').val()+','+tag_id);
                            }
                            console.log('POST TAG IDS:'+ $('#post_tag_ids').val() );
                            //alert("Data: " + data + "\nStatus: " + status);
                        },
                        error: function (msg) {
                            alert(msg.responseText);
                        }
                    });
                    $(this).val('');
                }else{//TAG EXIST
                    $(this).val('');
                }
            }
            return false;
        }
    });
    
    
    /**
     * enter not submit form
     */
    $('#post_tag').bind('keydown',function(event){
    	var e = e || event;
		var keyNum = e.which || e.keyCode;
		return keyNum==13 ? false : true;
    });
    
    
    $('#save_new_category').click(function(){
    	var new_category_name = $('#new_category_name').val();
    	var new_category_parent = $('#new_category_parent').val();
    	
    	console.log('new category:'+new_category_name+',parent'+new_category_parent );
    	
    	$.ajax({
            url: "http://"+window.location.host+"/category/api/create",
            async: false,
            data: {
    			new_catagory_name: encodeURI(encodeURI(new_category_name)),
    			new_category_parent:new_category_parent
    		},
            success: function (data) {
            	var parent;
	    		var new_diag_parent;
    			if(new_category_parent =='0'){//无父标签
    	    		parent = $('#category').find('>option:last');
    	    		new_diag_parent = $('#new_category_parent').find('>option:last');
    	    	}else{//父标签为已有节点
    	    		parent = $('#category'+new_category_parent);
    	    		new_diag_parent = $('#new_category_parent'+new_category_parent);
    	    		var space = '&nbsp;&nbsp;';
    	        	var space_count = countSubstr( parent.html() , space ) + 1;
    	        	for(i=0;i<space_count;i++){
    	        		new_category_name=space+new_category_name;
    	        	}
    	    	}
    			//alert("Data: " + data.term_id + "\nStatus: " + status);
				var new_category_id = data.term_id;
    			console.log('parent:'+parent.html()+',text:'+new_category_name);
    			parent.after('<option id="category'+new_category_id+'" value="'+new_category_id+'">'+new_category_name+'</option>');
    			new_diag_parent.after('<option id="new_category_parent'+new_category_id+'" value="'+new_category_id+'">'+new_category_name+'</option>');
    			$('#new_category_alert_box').hide();
    			$('#create_category_diag').modal('hide');
            },
            error: function (msg) {
            	//alert("Data: " + data.msg + "\nStatus: " + status);
            	$('#new_category_alert_box').show();
            	$('#new_category_alert_text').text(msg.responseText);
                
            }
        });
    });
    $('#create_category_diag').on('hidden.bs.modal', function (e) {
    	$('#new_category_alert_box').hide();
    });
    	
//    $('new_category_button').click(function(){
//    	
//    });
    //check new category name exists
    $('#new_category_name').blur(function(){
    	var new_category_name = $('#new_category_name').val();
    	$.ajax({
            url: "http://"+window.location.host+"/term/api/chkname",
            // http://www.lblog.com/term/api/chkname?term_name=TEST1
            //async: false,
            data: {
            	term_name: encodeURI(encodeURI(new_category_name))
    		},
            error: function (msg) {
            	$('#new_category_alert_box').show();
            	$('#new_category_alert_text').text(msg.responseText);
            }
        });
    });
    
    function countSubstr(mainStr, subStr)
    {
        var count = 0;
        var offset = 0;
        do
        {
            offset = mainStr.indexOf(subStr, offset);
            //console.log('offset:'+offset);
            if(offset != -1)
            {
                count++;
                offset += subStr.length;
            }
        }while(offset != -1)
        return count;
    }
    
    var jcrop_api;
    var iid;
    var img_name_before_cut_url;
    var img_name_after_cut_url;
    var is_cut=false;
    var is_upload=false;
    var uploading = false;
    /**
     * 上传封面图片
     */
    $('#upload_cover_img').click(function(){
    	if( uploading ){
    		alert('正在上传！');
    		return;
    	}
    	uploading = true;
    	if(jcrop_api!=null){
    		console.log('upload destory jcrop!');
    		jcrop_api.destroy();
    	}
    	var upfile = $('#up_cover_img_file').val();
    	if( upfile == ''|| upfile == null){
    		alert("请选择上传文件");
    		return;
    	}
    	//check file type by extend name
    	if( check_file_type(upfile)<0 ){
    		alert('图片类型不符合要求，只能上传jpg,gif,png,bmp类型图片！');
    	}
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
    	$.ajax({
            url: "http://"+window.location.host+"/img/post/cover/cut",
            async: false,
            dataType:'json',
            data: {
            	x: $('#x').val(),
            	y:$('#y').val(),
            	w:$('#w').val(),
            	h:$('#h').val(),
            	cover_img_name:img_name },
            success: function (data) {
            	//var img_url = data.url;
            	console.log('get cut img url:'+data.url);
            	$("#up_cover_img").attr("src", data.url );
                $("#img_preview").attr("src", data.url );
                var img_name = data.url.substring( data.url.lastIndexOf('/')+1 );
                //img_name_before_cut_url='';
            	img_name_after_cut_url = data.url;
            	
            	
                console.log('af cut img name:'+img_name);
                $('#cover_img_name').val( img_name );
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
    	$("#up_cover_img").attr("src", img_name_before_cut_url );
    	$("#img_preview").attr("src", img_name_before_cut_url );
    	
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
    	//异步保存
    	$.ajax({
            url: "http://"+window.location.host+"/img/post/cover/save",
            async: false,
            dataType:'json',
            data: {
            	iid:iid,
            	img_name:final_img_name
            	},
            success: function (data) {
            	alert(data.msg);
            	
            	$("#up_cover_img").attr("src", data.url );
                $("#img_preview").attr("src", data.url );
                
                if ( typeof (data.error) != 'undefined' ) {
                    if (data.error != '') {
                        alert("出错了:"+data.error);
                    }else {
                        alert("出错了" +data.msg);
                    }
                }
            },
            error: function (msg) {
                alert(msg.responseText);
            }
        });
    	
    	//清空所有变量
//    	img_name_before_cut_url='';
//    	img_name_after_cut_url='';
    	var main_space = "http://"+window.location.host+"/img/space450x250.jpg";
    	var prev_space = "http://"+window.location.host+"/img/space250x150.jpg";
    	$("#up_cover_img").attr("src", main_space );
    	$("#img_preview").attr("src", prev_space );
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
    	$("#up_cover_img").attr("src", main_space );
    	$("#img_preview").attr("src", prev_space );
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
        // Grab some information about the preview pane
        //$preview = $('#preview-pane'),
    	$preview = $('#img_preview'),
        //$pcnt = $('#preview-pane .preview-container'),
        //$pimg = $('#img_preview'),//$('#preview-pane .preview-container img'),
        xsize = $preview.width(),
        ysize = $preview.height();
    	
///console.log('init',[xsize,ysize]);
		var jcrop_api = $.Jcrop('#up_cover_img',{
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
                url: "http://"+window.location.host+"/img/post/cover/upload", //用于文件上传的服务器端请求地址
                secureuri: false, //是否需要安全协议，一般设置为false
                fileElementId: 'up_cover_img_file', //文件上传域的ID
                dataType: 'json',//'json', //返回值类型 一般设置为json
                success: 
                function (data, status){
                	console.log("IMG URL:"+data.url);
                    $("#up_cover_img").attr("src", data.url );
                    $("#img_preview").attr("src", data.url );
                    var img_name = data.url.substring( data.url.lastIndexOf('/')+1 );
                    console.log('img name:'+img_name);
                    $('#cover_img_name').val( img_name );
                    img_name_before_cut_url = data.url;
                    img_name_after_cut_url = '';
                    is_upload = true;
                    uploading = false;
                    setTimeout(function(){
                    	jcrop_api = add_img_processor();
                	},500);
                    iid = data.iid;
                    $('cover_img_id').val(iid);
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
