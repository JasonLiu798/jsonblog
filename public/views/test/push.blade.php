@include('templates/header_navoff')
	<script>
	var timestamp = 0;
	var url = '{{url()}}/term/unreadcmtcnt/1';
	var error = false;
	function connect(){
		$.ajax({
			data : {'timestamp' : timestamp},
			url : url,
			type : 'get',
			timeout : 0,
			success : function(response){
console.log(response);
				var data = eval('('+response+')');
				error = false;
				timestamp = data.timestamp;
				$("#content").append('<div>' + data.msg + '</div>');
			},
			error : function(){
				error = true;
				setTimeout(function(){ connect();}, 5000);
			},
			complete : function(){
				if (error)
					// if a connection problem occurs, try to reconnect each 5 seconds
					setTimeout(function(){connect();}, 5000); 
				else
					connect();
			}
		})
	}
	function send(msg){
		$.ajax({
			data : {'msg' : msg},
			type : 'get',
			url : url
		})
	}
	$(document).ready(function(){
		connect();
	})
	</script>

<body>
  <div id="content">
  </div>

  <p>
    <form action="" method="get" onsubmit="send($('#word').val());$('#word').val('');return false;">
      <input type="text" name="word" id="word" value="" />
      <input type="submit" name="submit" value="Send" />
    </form>
  </p>

  </body>
</html>