/**
 * Created by liujianlong on 15/2/10.
 */
$().ready(function(){
    $("#cancleReplay").hide();
    add_listener();
});

function add_listener(){
    $('#create_comment_submit').click(function(){
        var createOptions = {
            //data:{
            //    post_content:tinymce.get('post_content_ta').getContent()
            //},
            url: "http://"+window.location.host+"/comment/create",
            success: function (data) {
                if (data.status) {
                    console.log('创建成功');
                    //window.location.href=;
                    //window.navigate("http://"+window.location.host+"/post/single/"+data.post_id+'#comment-'+data.comment_id);
                    //"http://"+window.location.host+"/post/single/"+data.post_id+'
                    //console.log( +','+.length );
                    var url = window.location.href;
                    if( url.lastIndexOf('#') == url.length-1){
                        url = url.substr(0,url.length-1);
                    }
                    console.log( url+'#comment-'+data.comment_id );
                    location.reload( url+'#comment-'+data.comment_id );
                } else {
                    //验证失败或密码错误
                    console.log(data.msg);
                    var errarr = eval( data.msg );
                    //console.log(typeof errarr );//var errmsg_arr = new Array(errmsg);
                    $("#validate_res_ul").empty();
                    for(var i=0;i<errarr.length;i++){//item;item=data.msg[i++];){
                        //console.log(errarr[i] );
                        $('#validate_res_ul').append("<li>"+errarr[i] +"</li>");
                    }
                    $('#validate_res').show();
                }
            }
        };
        $("#comment_add_form").ajaxSubmit(createOptions);
    });
}


function moveCommentForm(thisID,isBack,replay_comment_id,is_child_replay,replay_child_comment_id){
    if(isBack){
        moveDiv( "commentNew","replay_comment");
        $("#cancleReplay").hide();
        $("#comment_replay").attr("value","0");
        $("#child_comment_replay").attr("value","0");
    }else{
        var desID = thisID+"Comment";
        moveDiv( desID,"replay_comment");
        $("#cancleReplay").show();
// 		var num = new RegExp("[0-9]+");
// 		var res = num.exec(thisID);
        //console.log();
        if(is_child_replay){
            //回复子评论的，增加 {回复 @xxx}
            var author = ($( '#'+desID).parent().children('span.child_comment_author').text()).trim();
            console.log(author);
            //var author = $(desID).parent().parent().attr('class');
            //console.log(author);
            var reply_str = '回复 @'+author+' :';
            $('#comment_content').val(reply_str);
            $("#child_comment_replay").attr("value", replay_child_comment_id );
        }else{
            $("#child_comment_replay").attr("value","0");
        }
        $("#comment_replay").attr("value", replay_comment_id );//new RegExp("[0-9]+").exec(thisID)[0] );

        add_listener();
    }
    $("#comment_content").focus();//.select();
    // var pos = $("#comment_content").offset().top - $("#replay_comment").height() - 44;
    var pos = $("#comment_content").offset().top - $("#replay_comment").height() -44;
    $('html,body').animate({scrollTop:pos },ANI_SPEED_FAST);
}

