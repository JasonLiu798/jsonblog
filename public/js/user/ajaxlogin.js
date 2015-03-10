/**
 * Created by liujianlong on 15/2/9.
 */

$().ready(function(){
    $('#login_diag_submit').click(function(){
        var loginOptions = {
            //data:{
            //    post_content:tinymce.get('post_content_ta').getContent()
            //},
            success: function (data) {
                if (data.status) {
                    console.log('登录成功');
                    $('#logindiag').modal('hide');
                    var user = data.msg;
                    console.log('user:'+data.msg);
                    $('#navright_user_profile_name').html(user.user_login+"<b class='caret'></b>");
                    $('#navright_nologin').hide();
                    $('#navright_login').show();
                    //location.href="http://"+window.location.host+"/post/single/"+data.post_id;
                } else {
                    //验证失败或密码错误
                    console.log(data.msg);
                    var errarr = eval( data.msg );
                    //console.log(typeof errarr );//var errmsg_arr = new Array(errmsg);
                    $("#login_diag_alert_ul").empty();
                    for(var i=0;i<errarr.length;i++){//item;item=data.msg[i++];){
                        //console.log(errarr[i] );
                        $('#login_diag_alert_ul').append("<li>"+errarr[i] +"</li>");
                    }
                    $('#login_diag_alert').show();
                }
            }
        };
        $("#ajax_login_form").ajaxSubmit(loginOptions);

    });

});


