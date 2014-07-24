var xmlHttp;

$(document)
		.ready(
				function() {
					$.formValidator.initConfig({
						formID : "signupform",
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
						onCorrect : "该用户名可以注册"
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
								return true;
							}
							if (data.indexOf("用户名已经存在") > 0) {
								return false;
							}
							// return false;
						},
						buttons : $("#submit"),
						error : function(jqXHR, textStatus, errorThrown) {
							alert("服务器没有返回数据，可能服务器忙，请重试" + errorThrown);
						},
						onError : "该用户名不可用，请更换用户名",
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
					$("#user_pass_confirm").formValidator({
						onShow : "输再次输入密码",
						onFocus : "至少6个长度，最多10个长度",
						onCorrect : "密码一致"
					}).inputValidator({
						min : 6,
						max : 10,
						empty : {
							leftEmpty : false,
							rightEmpty : false,
							emptyError : "重复密码两边不能有空符号"
						},
						onError : "重复密码不能为空,请确认"
					}).compareValidator({
						desID : "user_pass",
						operateor : "=",
						onError : "两次密码不一致,请确认"
					});
					$("#user_email")
							.formValidator({
								onShow : "请输入邮箱",
								onFocus : "邮箱6-100个字符,输入正确了才能离开焦点",
								onCorrect : "恭喜你,你输对了",
								defaultValue : "@"
							})
							.inputValidator({
								min : 6,
								max : 100,
								onError : "你输入的邮箱长度非法,请确认"
							})
							.regexValidator(
									{
										// regExp :
										// "^([\\w-.]+)@(([[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.)|(([\\w-]+.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(]?)$",
										regExp : "^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$",
										onError : "你输入的邮箱格式不正确"
									}).ajaxValidator(
									{
										type : "GET",
										dataType : "html",
										async : true,
										url : ajaxUrl,
										success : function(data) {
											// alert(nameChkUrl);
											// alert(data);
											if (data.indexOf("邮箱可以注册") > 0) {
												return true;
											}
											if (data.indexOf("邮箱已经存在") > 0) {
												return false;
											}
											// return false;
										},
										buttons : $("#submit"),
										error : function(jqXHR, textStatus,
												errorThrown) {
											alert("服务器没有返回数据，可能服务器忙，请重试"
													+ errorThrown);
										},
										onError : "该邮箱不可用，请更换邮箱",
										onWait : "正在对邮箱进行合法性校验，请稍候..."
									});
				});
/*
 * function user_exist(str) { if (str.length == 0) {
 * document.getElementById("userNameHint").innerHTML = "用户名不能为空"; return; } if
 * (trim(str).length != str.length) {
 * document.getElementById("userNameHint").innerHTML = "用户名不能包含空格"; return; }
 * xmlHttp = GetXmlHttpObject() if (xmlHttp == null) { alert("Browser does not
 * support HTTP Request"); return; }
 * 
 * var type = "user_login"; var url = window.location.protocol + "//" +
 * window.location.host + "/blog/users/signup_chk/" + type + "/" + str; //
 * alert(url); // Math.random() xmlHttp.onreadystatechange = stateChanged;
 * xmlHttp.open("GET", url, true); xmlHttp.send(null); }
 * 
 * 
 * function stateChanged() { if (xmlHttp.readyState == 4 || xmlHttp.readyState ==
 * "complete") { document.getElementById("userNameHint").innerHTML =
 * xmlHttp.responseText } }
 */
function GetXmlHttpObject() {
	var xmlHttp = null;
	try {
		// Firefox, Opera 8.0+, Safari
		xmlHttp = new XMLHttpRequest();
	} catch (e) {
		// Internet Explorer
		try {
			xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}

// function email_check

