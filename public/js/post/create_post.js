
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
console.log('space index'+txt.indexOf(' '));
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
            console.log('offset:'+offset);
            if(offset != -1)
            {
                count++;
                offset += subStr.length;
            }
        }while(offset != -1)
        return count;
    }
    
    var jcrop_api;
    
    /**
     * 上传封面图片
     */
    $('#upload_cover_img').click(function(){
    	if(jcrop_api!=null){
    		console.log('Destory jcrop,type:'+typeof(jcrop_api)+",obj:"+jcrop_api);
    		//dump(jcrop_api);
    		jcrop_api.destroy();
    	}
    	var upfile = $('#up_cover_img_file').val();
    	if( upfile == ''|| upfile == null){
    		alert("请选择上传文件");
    		return;
    	}
    	//ajaxFileUpload();
    	
    	//add_img_processor();
    });
    
    function add_img_processor(){
    	var jcrop_api,
        boundx,
        boundy,
        // Grab some information about the preview pane
        $preview = $('#preview-pane'),
        $pcnt = $('#preview-pane .preview-container'),
        $pimg = $('#img_preview'),//$('#preview-pane .preview-container img'),
        xsize = $pcnt.width(),
        ysize = $pcnt.height();
    
console.log('init',[xsize,ysize]);

    	$('#up_cover_img').Jcrop({
    	      onChange: updatePreview,
    	      onSelect: updatePreview,
    	      aspectRatio: xsize / ysize
    	    },function(){
    	      // Use the API to get the real image size
    	      var bounds = this.getBounds();
    	      boundx = bounds[0];
    	      boundy = bounds[1];
    	      // Store the API in the jcrop_api variable
    	      jcrop_api = this;
    	      // Move the preview into the jcrop container for css positioning
    	      $preview.appendTo(jcrop_api.ui.holder);
    	 });
    	
		function updatePreview(c)
		{
			if (parseInt(c.w) > 0){
				var rx = xsize / c.w;
				var ry = ysize / c.h;
		
				$pimg.css({
					width: Math.round(rx * boundx) + 'px',
					height: Math.round(ry * boundy) + 'px',
					marginLeft: '-' + Math.round(rx * c.x) + 'px',
					marginTop: '-' + Math.round(ry * c.y) + 'px'
				});
			}
		};
    }
    
    /**
     * ajax upload
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
                    setTimeout(function(){
                    	jcrop_api = $.Jcrop('#up_cover_img');//$('#up_cover_img').Jcrop();
                    	//dump(jcrop_api);
                    	//console.log("jcrop type:"+typeof(jcrop_api)+",obj:"+jcrop_api);
                	},500);
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
