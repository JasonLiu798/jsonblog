/*!function($){
	$(function(){
		$("#form_photo").ajaxForm();// ajaxForm()只是绑定表单事件，并不是提交表单。。。
	 	$("#button_photo").click(function(){
	 	// 判断上传格式，判断图片大小好像只能在服务端检测，所以预览图片必须先传上去
		var options = {
			 success: showResponse,// 上传成功回调函数
		};
	
		$("#form_photo").ajaxSubmit(options);// ajax上传图片，转由CI部分upload_photo
	 // 方法处理。另外，我上传file一直没有成功
	 // 过jquery.form官方文档中的submit()中再
	 // 回调函数ajaxForm()的方式,这种方法ajax
	 // 提交其它表单没问题
	 });
	 function showResponse(data){
	// 根据返回值判断上传是否成功
	// 成功返回图片路径
	// jquery添加<img id="jcrop_photo" src="网站目录"+data />
	// jquery添加<img id="preview_photo" src="网站目录"+data />
	// 现在开始准备
		 init_photo();// 初始化jcrop
		 $("#button_photo").click(function(){ 
			 $("#save_photo").submit();// 再次上传剪切后图片参数到CI部分save_photo方法 });
		 }
		 var photo_width = 292;// 设置显示预览图片的最大尺寸
		 var photo_height = 292;
	 
		 function init_photo(){
		 var k = 0;// 记录图片伸缩比例
		 var screen_img = $("#jcrop_photo");// 通过new_img获取ajax加载的图片
		 var new_img = new Image(); // 直接获取ajax加载图片的尺寸有问题
		 new_img.src = screen_img.attr("src");// 反正我是这样才获得了真实的尺寸
		 setTimeout(function(){ // 由于图片加载时间，可能要通过挂起一段时间后才能读取图片尺寸
	     if((new_img.height/292) >= (new_img.width/292)){// 限定长或宽最大为292
	    	 $("#jcrop_photo").css("height",292);
	    	 photo_width = Math.round(new_img.width*292/new_img.height);
	    	 $("#jcrop_photo").css("width",photo_width);
	    	 k = new_img.height/292;
	     }else{
	    	 $("#jcrop_photo").css("width",292);
	    	 photo_height = Math.round(new_img.height*292/new_img.width);
	    	 $("#jcrop_photo").css("height",photo_height);
	    	 k = new_img.width/292;
	     }
	     
	     $("#p_k").val(k); // 将伸缩比例传给hidden表单
	     
	     $('#jcrop_photo').Jcrop({// 绑定Jcrop到图片，必须在此刻绑定
	    	 onChange: show_preview,// 剪切预览图 //这时候图片的DOM才被获取，不要在
	    	 onSelect: show_preview, // ready的时候绑定，也不要jquery添加<img onload="">
	    	 aspectRatio: 1, // 
	     });
		 },100);
	 }
	 function show_preview(coords){ // 显示剪切后图片预览
		 if (parseInt(coords.w) > 0){
			 var i_k = 146 / coords.w; // 146为设置的预览图区域大小
			 $("#preview").css({
				 "height": (i_k * photo_height) + 'px', // Jcrop官方文档中给出的是
				 "width": (i_k * photo_width) + 'px', // 指定的图片长宽，这也就是
				 "marginLeft": '-' + (i_k * coords.x) + 'px', // 为什么要获得真实图片尺寸
				 "marginTop": '-' + (i_k * coords.y) + 'px',// 的原因，具体图片原理见后文
			 }).show();
			 $("#p_x").val(coords.x);// 将剪切位置传给hidden表单
			 $("#p_y").val(coords.y);
			 $("#p_h").val(coords.h);
			 $("#p_w").val(coords.w);
		 }
	 }
	 });
}(window.jQuery)*/