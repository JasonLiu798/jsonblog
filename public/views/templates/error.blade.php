
<script type="text/javascript">   
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
}
</script>   

<div id="ttext" value="aa"></div>

{{$err_msg}}