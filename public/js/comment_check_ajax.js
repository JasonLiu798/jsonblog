var xmlHttp

$(document).ready(function() {
	$('#comment_form').submit(function() {
		$.post("<?php echo site_url(comments/create);?>", {
			comment_author : $('#comment_author').val(),
			comment_content : $('#comment_content').val(),
		}, function(data) {
			//alert(data);
			document.getElementById("chkHint").innerHTML = data
		});
		return false;
	});
});

/*
function showHint(str) {
	if (str.length == 0) {
		document.getElementById("txtHint").innerHTML = ""
		return
	}
	xmlHttp = GetXmlHttpObject()
	if (xmlHttp == null) {
		alert("Browser does not support HTTP Request")
		return
	}
	var url = "gethint.php"
	url = url + "?q=" + str
	url = url + "&sid=" + Math.random()
	xmlHttp.onreadystatechange = stateChanged
	xmlHttp.open("GET", url, true)
	xmlHttp.send(null)
}

function stateChanged() {
	if (xmlHttp.readyState == 4 || xmlHttp.readyState == "complete") {
		document.getElementById("txtHint").innerHTML = xmlHttp.responseText
	}
}

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
*/