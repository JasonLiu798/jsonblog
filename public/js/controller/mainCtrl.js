/**
 * Created by liujianlong on 15/2/11.
 */
//$().ready(function(){
    angular.module('commentCtrl', [])
        .controller('commentController', function($scope, $http, Comment) {
            // 持有新评论所有表单数据的对象
            $scope.commentData = {};


            $scope.processForm = function() {
                console.log('process form');
                //if( $('#comment_add_form #comment_replay').attr('value') == 0 ){
                //    //新回复
                //
                //}else{
                //    //回复的回复
                //

                var createOptions = {
                    //data: $.param($scope.commentData),
                    url: "http://"+window.location.host+"/comment/create",
                    //data:{
                    //    post_content:tinymce.get('post_content_ta').getContent()
                    //},
                    success: function (data) {
                        if ( data.status ) {
                            console.log('success');
                            //Comment.get()
                            //    .success(
                            //        function(data) {
                            //            $scope.comments = eval(data.msg);
                            //            $scope.loading = false;
                            //    });

                            moveCommentForm( 'replay'+data.reply,false);
                        } else {
                            console.log('fail:'+data.msg);
                            var errarr = eval( data.msg );
                            ////console.log(typeof errarr );//var errmsg_arr = new Array(errmsg);
                            $("#validate_res_ul").empty();
                            for(var i=0;i<errarr.length;i++){//item;item=data.msg[i++];){
                                                             //console.log(errarr[i] );
                                $('#validate_res_ul').append("<li>"+errarr[i] +"</li>");
                            }
                            $('#validate_res').show();
                        }
                        $("#comment_add_form button").enable();
                        //$scope.comment_add_form
                    }
                };
                $("#comment_add_form").ajaxSubmit(createOptions);
            };

            // 调用显示加载图标的变量
            $scope.loading = true;
            // 先获取所有的评论，然后绑定它们到$scope.comments对象         // 使用服务中定义的函数
            // GET ALL COMMENTS ====================================================
            Comment.get()
                .success(function(data) {
                    if(data.status){
                        $scope.comments = eval(data.msg);

                        $scope.loading = false;
                    }else{
                        //加载失败
                        $scope.loading = false;
                    }
                });


        });
/*

 $http({
 method  : 'POST',
 url     : "http://"+window.location.host+"/comment/create",
 data    : $.param( $scope.commentData )  // pass in data as strings
 //headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
 }).success(function(data) {
 if ( data.status ) {
 console.log('success');
 Comment.get()
 .success(
 function(data) {
 $scope.comments = eval(data.msg);
 $scope.loading = false;
 });
 } else {
 console.log('fail:'+data.msg);
 var errarr = eval( data.msg );
 ////console.log(typeof errarr );//var errmsg_arr = new Array(errmsg);
 $("#validate_res_ul").empty();
 for(var i=0;i<errarr.length;i++){//item;item=data.msg[i++];){
 //console.log(errarr[i] );
 $('#validate_res_ul').append("<li>"+errarr[i] +"</li>");
 }
 $('#validate_res').show();
 //$scope.message = data.message;
 }
 });

 */



//$http({
//    method  : 'POST',
//    //url     : "http://"+window.location.host+"/comment/create",
//    data    : $.param($scope.commentData),  // pass in data as strings
//    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
//}).success(function(data) {
//    console.log(data);
//    if (!data.status ) {
//        console.log('success');
//        Comment.get().success(function(data) {
//            $scope.comments = eval(data.msg);
//            $scope.loading = false;
//        });
//    // if not successful, bind errors to error variables
//    //    $scope.errorName = data.errors.name;
//    //    $scope.errorSuperhero = data.errors.superheroAlias;
//    } else {
//    // if successful, bind success message to message
//        console.log(data.msg);
//        $scope.message = data.message;
//    }
//});


// 处理提交表单的函数
//SAVE A COMMENT ======================================================
//$scope.submitComment = function() {
//    $scope.loading = true;
//    // 保存评论。在表单间传递评论
//    Comment.save($scope.commentData)
//        .success(function(data) {
//            // 如果成功，我们需要刷新评论列表
//            Comment.get().success(function(data) {
//                $scope.comments = eval(data.msg);
//                $scope.loading = false;
//            });
//        })
//        .error(function(data) {
//            console.log(data);
//        });
//};

/*
 // 处理删除评论的函数
 // DELETE A COMMENT ====================================================
 $scope.deleteComment = function(id) {
 $scope.loading = true;

 // 使用在服务中创建的函数
 Comment.destroy(id)
 .success(function(data) {

 // 如果成功，我们需要刷新评论列表
 Comment.get()
 .success(function(getData) {
 $scope.comments = getData;
 $scope.loading = false;
 });

 });
 };
 */