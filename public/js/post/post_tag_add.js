$(document).ready(function(){
    $('.tagbox').click(function(){
        $("input[name='post_tag']").focus();
    });
    
    $(document).on("click","#tags span",function(){
        $(this).remove();
    });
    
    $('.old span').click(function(){
        var ids=new Array();
        var txt=$(this).attr('name');
        var id=$(this).attr('id');
        $('#tags .label').each(function(){
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
            $('#tags').append('<span id='+id+' name='+txt+' class="label label-info">'+txt+'</span>&nbsp;')
        }
    });
    
    /**
     * input text enter add a post tag
     */
    $('#post_tag').bind('keyup',function(event){
    	if(event.keyCode==188){
    		$('#post_tag_alert').text('标签不能包含英文逗号！');
            return false;
    	}
    	if(event.keyCode == 32){
    		$('#post_tag_alert').text('标签不能包含空格！');
            return false;
    	}
        if(event.keyCode==13 ){//|| event.keyCode==32){//回车
            var txt=$(this).val();
            if(txt!=''){
            	if(txt.indexOf(',')>=0 ){
            		$('#post_tag_alert').text('标签不能包含英文逗号，请删除逗号后提交！');
            		return false;
            	}
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
                $('#tags .label').each(function(){
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
                    $('#tags').append('<span name='+txt+' class="label label-info">'+txt+'</span>&nbsp;');
                    $(this).val(+txt+',');
                    //ACTUALLY SUBMIT FORM TEXT 
                    $('').val('');
                }else{//TAG EXIST
                    $(this).val('');
                }
            }
            return false;
        }
    });
    //enter not submit form
    $('#post_tag').bind('keydown',function(event){
    	var e = e || event;
		var keyNum = e.which || e.keyCode;
		return keyNum==13 ? false : true;
    });
});




//$(document).ready(function(){
//	$('#create_post_form').onkeydown = function(e){
//		var e = e || event;
//		var keyNum = e.which || e.keyCode;
//		return keyNum==13 ? false : true;
//	};
//});
