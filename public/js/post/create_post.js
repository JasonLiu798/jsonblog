$(document).ready(function(){

	tinymce.init({
		//toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright | image link | code | preview",
		language : 'zh_CN',
		object_resizing: true,
		nowrap : false,
		selector: "textarea",
		tools: "inserttable",
		toolbar: [
			"undo redo | bold italic underline strikethrough | blockquote image link | alignleft aligncenter alignright | forecolor backcolor | code charmap | emoticons ",
			"link | insertdatetime | preview | save | uploadimg"
		],
		plugins : 'advlist autolink link image lists charmap print preview insertdatetime charmap code textcolor table emoticons save uploadimg',
		insertdatetime_formats: ["%Y{{Lang::get('tools.YEAR')}}%m{{Lang::get('tools.MONTH')}}%d{{Lang::get('tools.DAY')}} %H:%M","%Y{{Lang::get('tools.YEAR')}}%m{{Lang::get('tools.MONTH')}}%d{{Lang::get('tools.DAY')}}","%H:%M"],
		save_enablewhendirty: true,
		upload_action: "http://"+window.location.host+'/img/post/content/upload',//required
		upload_file_name: 'uploadimg',//required
		save_onsavecallback: function() {console.log("Save");}

	});





	//$("#create_post_form").ajaxForm(options);
	$('#save_post').click(function(){
		$('#is_draft').attr('value',false);
		//$('#create_post_form').submit();
		//console.log('tiny:'+tinymce.getInstanceById('post_content').getBody().innerHTML);
		//console.log('tiny:'+tinymce.get('post_content').getContent());
		var saveOptions = {
			data:{
				post_content:tinymce.get('post_content_ta').getContent()
			},
			success: function (data) {
				if (data.status) {
					console.log('保存成功');
					//location.href="http://"+window.location.host+"/post/single/"+data.post_id;
				} else {
					console.log('errorcode:' + data.errorcode + ',error:' + data.error);
				}
			}
		};
		//var queryString = $('#create_post_form').formSerialize();
		//console.log('FormSeri:'+queryString);
		$("#create_post_form").ajaxSubmit(saveOptions);

		//$("#dlg_form input").map(function(){
		//	return ($(this).attr("name")+'='+$(this).val());
		//}).get().join("&") ;

	});

	$('#save_draft').click(function(){
		$('#is_draft').attr('value',true);
		var draftOptions = {
			success: function (data) {
				if( data.status ){
					alert('保存成功');
				}else{
					console.log('errorcode:' +data.errorcode + ',error:'+data.error );
				}
			}
		};
		$("#create_post_form").ajaxSubmit(draftOptions);
	});



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
        }
        var exist = $.inArray(id,ids);
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
            	

                var new_tags_arr = new Array();
				var new_tags_str ='';
				//已添加的新标签
                $('#newtags .tag').each(function(){
					new_tags_arr.push( $(this).attr('name') );
					new_tags_str += $(this).attr('name');
                });

                if( new_tags_arr.length>=5 ){
                	$('#post_tag_alert').text('标签最多添加5个哦！');
                	return false;
                }
                var exist = $.inArray( txt, new_tags_arr );
				var add_tag_name = txt.trim();
				//已经存在 新添加的
                if(exist<0){
                	$.ajax({
                        url: "http://"+window.location.host+"/tag/api/create",
                        async: false,
                        data: { new_tag_name: encodeURI(encodeURI(add_tag_name)) },
                        success: function (data) {
							if(data.status ){
								console.log('Tag 创建成功');
								var tag_id = data.term_id;
								add_tag_callback(tag_id,add_tag_name);
							} else {
								if(data.errorcode == NOLOGIN ){
									console.log('no login');
								}else if(data.errorcode == TAG_EXIST ){
									console.log('tag exist,direct show');
									//库中已存在 已有的标签，直接显示
									var tag_id = data.error;
									add_tag_callback(tag_id,add_tag_name);
									//alert(data.error);
								}else{
									alert(data.error);
								}
							}
                        },
                        error: function (msg) {
                            alert(msg.responseText);
                        }
                    });
                    $(this).val('');
                }else{//在新标签列表已经存在
                    $(this).val('');
                }
            }
            return false;
        }
    });

	function add_tag_callback(tag_id,tag_name){
		$('#newtags').append('<span name='+tag_name+' class="tag tag_new" value="'+tag_id+'">'+tag_name+'&nbsp;X</span>');
		//ACTUALLY SUBMIT FORM TEXT
		if( $('#post_tag_ids').val().length ==0 ){
			$('#post_tag_ids').val( tag_id);
		}else{
			$('#post_tag_ids').val( $('#post_tag_ids').val()+','+tag_id);
		}
		console.log('POST TAG IDS:'+ $('#post_tag_ids').val() );
	}
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
    var main_space = "http://"+window.location.host+"/img/space450x250.jpg";
	var prev_space = "http://"+window.location.host+"/img/space250x150.jpg";
	 
//	var main_width = 600;
//	var main_height = 300;
    /**
     * 上传封面图片
     */
    $('#upload_cover_img').click(function(){
    	if( uploading ){
    		alert('正在上传！');
    		return;
    	}
    	
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
    	var main_img = $('#up_cover_img');
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
            	$("#up_cover_img").attr("src", data.url );
                $("#img_preview").attr("src", prev_space );
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
        $("#cover_img_preview_inpage").attr("src",final_img_url);
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
            	$("#up_cover_img").attr("src", main_space );
                $("#img_preview").attr("src",  prev_space );

            },
            error: function (msg) {
                alert(msg.responseText);
            }
        });
    	 
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
                    //$('#cover_img_name').val( img_name );
                    img_name_before_cut_url = data.url;
                    img_name_after_cut_url = '';
                    is_upload = true;
                    uploading = false;
                    setTimeout(function(){
                    	jcrop_api = add_img_processor();
                	},500);
                    iid = data.iid;
                    $('#cover_img_id').val(iid);
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
