$(document).ready(function() {
	$.formValidator.initConfig({
		formID : "comment_add_form",
		debug : false,
		submitOnce : true,
		onError : function(msg, obj, errorlist) {
			$("#errorlist").empty();
			$.map(errorlist, function(msg) {
				$("#errorlist").append("<li>" + msg + "</li>")
			});
			alert(msg);
		},
		submitAfterAjaxPrompt : '有数据正在异步验证，请稍等...'
	});
	
	$("#comment_author").formValidator({
		onShow : "请输入用户名",
		onFocus : "用户名至少5个字符,最多20个字符",
		onCorrect : "用户名可以使用"
	}).inputValidator({
		min : 5,
		max : 20,
		onError : "请检查用户名长度，用户名至少5个字符,最多20个字符！"
	}).regexValidator({regExp:"^[\u4e00-\u9fa5a-zA-Z0-9]+$",onError:"请修改用户名格式"});
	
	$("#comment_email").formValidator({
		onShow:"请输入E-MAIL",
		onFocus:"邮箱长度6-100个字符",
		onCorrect:"邮箱正确"
		//defaultValue:"@"
	}).inputValidator({
		min:6,max:100,onError:"你输入的邮箱长度非法,请确认"
	}).regexValidator({
		regExp:"^([\\w-.]+)@(([[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.)|(([\\w-]+.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(]?)$",
		onError:"请修改邮箱格式"
	});
	

	$("#comment_content").formValidator({
		onShow:"请输入评论内容"
		//onFocus:"评论内容长度0-5000个字符"
	}).inputValidator({
		min:0,max:5000,onError:"你输入的评论过长，请确认"
	});/*.regexValidator({
		regExp:"^[\u4e00-\u9fa5a-zA-Z0-9]+$",
		onError:"请删除特殊字符"
	});*/
	
	/*.ajaxValidator({
		dataType : "html",
		async : true,
		url : "",
		success : function(data) {
			if (data.indexOf("此用户名可以注册!") > 0)
				return true;
			if (data.indexOf("此用户名已存在,请填写其它用户名!") > 0)
				return false;
			return false;
		},
		buttons : $("#button"),
		error : function(jqXHR, textStatus, errorThrown) {
			alert("服务器没有返回数据，可能服务器忙，请重试" + errorThrown);
		},
		onError : "该用户名不可用，请更换用户名",
		onWait : "正在对用户名进行合法性校验，请稍候..."
	}).defaultPassed();*/
});