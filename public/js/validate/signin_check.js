$(document).ready(
function() {
	$.formValidator.initConfig({
		formID : "signinform",
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

	var ajaxUrl = window.location.protocol + "//"
			+ window.location.host + "/blog/users/signup_chk";// +
	// $("#user_login").val();
	// alert(nameChkUrl);

	$("#user_login").formValidator({
		onShow : "请输入用户名",
		onFocus : "用户名至少5个字符,最多10个字符",
		onCorrect : "该用户名可以登录"
	}).inputValidator({
		min : 5,
		max : 10,
		onError : "你输入的用户名非法,请确认"
	})// .regexValidator({regExp:"username",dataType:"enum",onError:"用户名格式不正确"})
	.ajaxValidator({
		type : "GET",
		dataType : "html",
		async : true,
		url : ajaxUrl,
		success : function(data) {
			// alert(nameChkUrl);
			// alert(data);
			if (data.indexOf("用户名可以注册") > 0) {
				return false;
			}
			if (data.indexOf("用户名已经存在") > 0) {
				return true;
			}
			// return false;
		},
		buttons : $("#submit"),
		error : function(jqXHR, textStatus, errorThrown) {
			alert("服务器没有返回数据，可能服务器忙，请重试" + errorThrown);
		},
		onError : "该用户名不存在",
		onWait : "正在对用户名进行合法性校验，请稍候..."
	});// .defaultPassed();

	$("#user_pass").formValidator({
		onShow : "请输入密码",
		onFocus : "至少6个长度，最多10个长度",
		onCorrect : "密码合法"
	}).inputValidator({
		min : 6,
		max : 10,
		empty : {
			leftEmpty : false,
			rightEmpty : false,
			emptyError : "密码两边不能有空符号"
		},
		onError : "密码不能为空,请确认"
	});

});
