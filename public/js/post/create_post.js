
$(document).ready(function(){
	/**
	 * post tag associate
	 */
    $('.tagbox').click(function(){
        $("input[name='post_tag']").focus();
    });
    
    /**
     * remove a tag
     */
    $(document).on("click","#newtags span",function(){
    	var remove_tag = $(this).attr('name');
    	var idx = $('#post_tag_id').val().indexOf( remove_tag );
    	var remove_tag_len = remove_tag.length;
    	var tag_str = $('#post_tag_id').val();
    	var tag_str_len = tag_str.length; 
    	console.log('Remove tag:'+remove_tag +',idx '+idx);
    	console.log(idx);
    	
//		123,56,8
//		first idx=0    	
//		mid [5,6] idx=4,len=8,
//		last [8] idx=7,len=8-1=7,idx-1=6
    	if(idx==0){//first tag
    		$('#post_tag_id').val( $('#post_tag_id').val().substring(remove_tag_len+1	) );
    	}else if( idx>0 && idx == tag_str_len -remove_tag_len ){//last tag
    		$('#post_tag_id').val( tag_str.substring(0, idx - 1 ) );
    	}else if( idx>0 && idx< tag_str_len - remove_tag_len ){//in the middle    		
    		$('#post_tag_id').val( tag_str.substring(0, idx  - 1 )+ tag_str.substring(idx+remove_tag_len));
    	}else{
    		console.log('remove idx error!');
    	}
        $(this).remove();
    	
    	console.log( 'after remove tagstr:' + $('#post_tag_id').val() );
    });
    
    /**
     * add a old tag
     */
    $('.old span').click(function(){
        var ids=new Array();
        var txt=$(this).attr('name');
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
            $('#newtags').append('<span id='+id+' name='+txt+' class="tag tag_new">'+txt+'&nbsp;X</span>');
            if( $('#post_tag_id').val().length ==0 ){
            	$('#post_tag_id').val( txt);
            }else{
            	$('#post_tag_id').val( $('#post_tag_id').val()+','+txt);
            }
            
            console.log( "POST_TAG_IDS:"+ $('#post_tag_id').val() );
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
                if(exist<0){//NOT EXIST
                	
                    $('#newtags').append('<span name='+txt+' class="tag tag_new">'+txt+'&nbsp;X</span>');
                    //$(this).val(+txt+',');
                    //ACTUALLY SUBMIT FORM TEXT
                    
                    if( $('#post_tag_id').val().length ==0 ){
                    	$('#post_tag_id').val( txt);
                    }else{
                    	$('#post_tag_id').val( $('#post_tag_id').val()+','+txt);
                    }
                    console.log( "POST_TAG_IDS:"+ $('#post_tag_id').val() );
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
    	
    	console.log('new category:'+new_category_name+','+new_category_name );
    	$('#create_category_diag').modal('hide');
//    	$.get("term/",function(data,status){
//    	    alert("Data: " + data + "\nStatus: " + status);
//    	  });
//  	
    	$.post("http://"+window.location.host+"/category/api/create",
    		{
    			category_name:new_category_name,
    			category_parent:new_category_parent
    		},
			function(data,status){
    			//alert("Data: " + data + "\nStatus: " + status);
			});
    	//update #new_category_parent and #category
    	// append <option value="1">未分类</option>
    	var new_category_id = -1;
    	var parent;
    	if(new_category_parent =='0'){//无父标签
    		parent = $('#category').find('>option:last');
    	}else{//父标签为已有节点
    		parent = $('#category'+new_category_parent);
    		var space = '&nbsp;&nbsp;';
        	var space_count = countSubstr( parent.html() , space ) + 1;
        	for(i=0;i<space_count;i++){
        		new_category_name=space+new_category_name;
        	}
    	}
    	console.log('parent:'+parent.html()+',text:'+new_category_name);
    	parent.after('<option id="category'+new_category_id+'" value="'+new_category_id+'">'+new_category_name+'</option>');
    	
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
    
});
