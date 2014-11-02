@if( is_null( Session::get('user')) )
	@include('templates/header_logout')
@else
	@include('templates/header_login')
@endif

<script type="text/javascript">
/*
startclock();
var timerID = null;   
var timerRunning = false; 

function showtime() {   
	Today = new Date();
	var NowSecond = Today.getSeconds();   
	Secondleft = 59 - NowSecond   

	if (Secondleft<0)   
	{   
		Secondleft=60+Secondleft;   
		Minuteleft=Minuteleft-1;   
	}   
	
	Temp= Secondleft+'秒';
	document.form1.left.value=Temp;   
	timerID = setTimeout("showtime()",1000);   
	timerRunning = true;   
}

function stopclock () {   
	if(timerRunning)
	clearTimeout(timerID);   
	timerRunning = false;   
}   

function startclock () {   
	stopclock();
	showtime();
}*/
</script>   

<div class="error_box">
<h2>出错了o(&gt;﹏&lt;)o</h2>
	<span class="error_msg">{{$msg}}</span>

</div>



@include('templates/footer')








