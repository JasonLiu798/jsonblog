@include('templates/header_login')

{{ HTML::script('js/tinymce/tinymce.min.js') }}
{{ HTML::script('js/tinymce/uploadimg/uploadimg.js') }}
{{ HTML::script('js/post/create_post.js') }}
{{ HTML::script('js/lib/jquery.Jcrop.min.js') }}
{{ HTML::script('js/lib/ajaxfileupload.v2.js') }}
{{ HTML::script('js/lib/dump_src.js') }}
{{ HTML::style('css/create_post.css') }}
{{ HTML::style('css/jquery.Jcrop.min.css') }}

<script type="text/javascript">
tinymce.init({
	//toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright | image link | code | preview",
	language : 'zh_CN',
	object_resizing: true,
	nowrap : false,
    selector: "textarea",
    tools: "inserttable",
    toolbar: [
    	          "undo redo | bold italic underline strikethrough | blockquote image link | alignleft aligncenter alignright | forecolor backcolor | code charmap | emoticons ",
    	          "link | insertdatetime | preview | save | uploadimg"
    	      ],
	plugins : 'advlist autolink link image lists charmap print preview insertdatetime charmap code textcolor table emoticons save uploadimg',
	insertdatetime_formats: ["%Y{{Lang::get('tools.YEAR')}}%m{{Lang::get('tools.MONTH')}}%d{{Lang::get('tools.DAY')}} %H:%M","%Y{{Lang::get('tools.YEAR')}}%m{{Lang::get('tools.MONTH')}}%d{{Lang::get('tools.DAY')}}","%H:%M"],
	save_enablewhendirty: true,
	upload_action: "http://"+window.location.host+'/img/post/content/upload',//required
	upload_file_name: 'uploadimg',//required	    
    save_onsavecallback: function() {console.log("Save");}
	
 });

//tinymce.getInstanceById('post_content').getBody().innerHTML= ""; 

//data = ({"msg":"\u4e0a\u4f20\u6210\u529f\uff01","url":"http:\/\/www.lblog.com\/upload\/img\/d094fd36b80aa2b3c8163581c74dcbfc"});
//console.log("data.msg:"+data.msg);

</script>

<div class="container">
<div class="row">
<div class="col-sm-8">
	<h2>{{$title}}</h2>
	
<form method="post" action="{{url()}}/post/update/save" accept-charset="utf-8" role="form" id="update_post_form">
	<input type="hidden" name="post_id" id="post_id" value="{{ $post->post_id }}" />
	<input type="hidden" name="post_tag_ids" id="post_tag_ids" value="{{ $post->post_tag_id }}" />
	<input type="hidden" id="set_cover" name="set_cover" value="{{ $post->post_cover_img==0?"false":"true" }}" />
	<!--<input type="hidden" id="cover_img_name" name="cover_img_name" value="false"/> 生成摘要url用 -->
	<input type="hidden" id="cover_img_id" name="cover_img_id" value="{{$post->post_cover_img }}"/><!-- 保存用 -->
	<div class="form-group">
		<label for="post_title">标题</label>
		<input class="form-control" value="{{$post->post_title}}" name="post_title" type="text" id="post_title">
	</div>
	
	<div class="form-group">
		<textarea rows="15" id="post_content" name="post_content" class="form-control">
		{{$post->post_content}}
		</textarea>
	</div>
	
	<div class="form-group">
		<h5>{{Lang::get('post.CATEGORY')}}</h5>
		
		<select class="form-control" name="category" id="category">
			@foreach($category as $cat)
			  <option id="category{{$cat->term_id}}" value="{{$cat->term_id }}" @if($post->category[0]->term_id==$cat->term_id) selected="selected" @endif>{{ $cat->name }}</option>
			@endforeach
		</select>
	</div>
	
	<div class="form-group">
		<!-- 增加分类button -->
		<input type="button" class="btn btn-default" id="new_category_button" data-toggle="modal" data-target="#create_category_diag" value="创建分类"/>
	</div><!-- end of from group -->
	
	<!-- 博客标签添加选择 -->
	<div class="form-group">
	<div class="tagbox">
		<h5>{{Lang::get('post.POST_TAG')}}&nbsp;<small>{{ Lang::get('post.POST_TAG_ADD_INFO')}}</small></h5>
		<div class="clearfix"></div>
		<!-- added tags -->
        <div id="newtags">
        	@foreach($post->post_tag as $tag)
        		<span name="{{$tag->name}}" class="tag tag_new" value="{{$tag->term_id}}">{{$tag->name}}&nbsp;X</span>
        	@endforeach
        </div>
        <div class="clearfix"></div>
        <!-- input new tag -->
		<input type="text"  name="post_tag" id="post_tag" value="" class="form-control"/>
		<span class="help-block" id="post_tag_alert"></span>
		<!-- exist tags  -->
        <div class="old">
            <h5>{{Lang::get('post.USED_TAG')}}</h5>
            <div class="clearfix"></div>
            @foreach($post_tag as $tag)
            	<span class="tag tag_old" value="{{$tag->term_id}}" id="tag{{$tag->term_id}}" name="{{$tag->name}}">{{$tag->name}}</span>
            @endforeach
        </div>
    </div><!-- tag box -->
    </div><!-- form-group -->


	<!-- 
	<div class="row">
		<div class="col-xs-6">
			<label for="post_tag">{{Lang::get('post.POST_TAG')}}</label>
			<input type="text" id="post_tag" name="post_tag" class="form_control">
		</div>
		<div class="col-xs-1">
			<button id="post_tag_add" class="btn btn-default">添加</button>
		</div>
	</div>
	
	<div class="form-group"></div> -->
	<div class="form-group">
		<!-- 设置博文封面图片 -->
		<input type="button" class="btn btn-default" id="submit_button" data-toggle="modal" data-target="#cover_img_diag" value="重设摘要图片"/>
		<div>
			<img id="cover_img_preview_inpage" src="{{$post->cover_img_url}}"/>
		</div>
	</div>
	
	<div class="form-group col-sm-offset-5 col-sm-12">
		<input type="submit" value="{{Lang::get('post.PUBLISH')}}" class="btn btn-default"/>
		&nbsp;&nbsp;&nbsp;
		<input type="button" name="save_draft" class="btn btn-default"  value="保存草稿" />
	</div>
	</form>
	
	<!-- 增加分类对话框 -->
	<div  id="create_category_diag" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" >
	  <div class="modal-dialog">
	    <div class="modal-content">
	    	<div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		        <h4 class="modal-title" id="myModalLabel">创建分类</h4>
		    </div>
		    <div class="modal-body">
				<div class="alert alert-danger alert-dismissible fade in" role="alert" id="new_category_alert_box">
			    	<button type="button" class="close" data-dismiss="alert">
			    		<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
			    	</button>
			    	<strong id="new_category_alert_text"></strong>
			    </div>
		        <h5>{{Lang::get('term.CATEGORY_NAME')}}</h5>
		        
		        <input type="text"  class="form-control" id="new_category_name" name="new_category_name" />
		         
		        <h5>{{Lang::get('post.NEW_CATEGORY_PARENT')}}</h5>
				
				<select class="form-control" name="new_category_parent" id="new_category_parent">
					<option value="0">{{ Lang::get('term.NO_PARENT') }}</option>
					@foreach($category as $cat)
						<option id="new_category_parent{{$cat->term_id}}" value="{{$cat->term_id }}">{{ $cat->name }}</option>
					@endforeach
				</select>
		    </div>
		    <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
		        <button type="button" class="btn btn-primary" id="save_new_category">保存</button>
		    </div>
	    </div><!-- end of modal-content -->
	  </div><!-- end of modal-dialog -->
	</div><!-- end of create_category_diag -->
		
		
	
	<!-- 博文封面图片设置对话框 -->
	<div  id="cover_img_diag" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		    	<div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			        <h4 class="modal-title" id="myModalLabel">设置博文摘要图片</h4>
			    </div>
			    <div class="modal-body">
			    	<!-- <form action="{{url()}}/img/post/cover/upload" id="upload_cover_img_form" method="post" accept-charset="utf-8" enctype="multipart/form-data"> -->
			    	<form class="form-inline" role="form">
				    	<h5>选择文件</h5>
				    	
				    	<input type="hidden" id="x" name="x" />
						<input type="hidden" id="y" name="y" />
						<input type="hidden" id="w" name="w" />
						<input type="hidden" id="h" name="h" />
						<div class="form-group">
					        <input type="file" name="up_cover_img_file" id="up_cover_img_file" value="请选择图片" />
					        <p class="help-block">请选择jpg、png、bmp、gif格式图片上传</p>
				        </div>
				        <div class="form-group">
							<button type="button" class="btn btn-primary" id="upload_cover_img">上传</button>		        
				        </div>
			        </form><!-- end of form -->
			        <div id="upload_img_and_preview">
				        <div id="upload_img_pane">
				        	<img id="up_cover_img" src="{{url()}}/img/space450x250.jpg" alt="上传图片"> 
				        	<!-- <img id="up_cover_img" src="" alt="请上传图片"> -->
				        </div>
				        <div id="preview-pane">
				        	<div class="preview-container">
						    	<img src="{{url()}}/img/space250x150.jpg" id="img_preview" class="jcrop-preview" alt="Preview" />
							</div>
							<!-- <img src="" class="jcrop-preview" alt="Preview" /> -->
						</div>
					</div>
					<div class="form-group">
				      	<button type="button" class="btn btn-primary" id="cut_img">剪裁</button>
				      	<button type="button" class="btn btn-default" id="cut_img_back">撤销</button>
			      	</div>
			    </div>
			    <div class="modal-footer">
			        <button type="button" class="btn btn-default" id="cancle_save_img">取消</button>
			        <button type="button" class="btn btn-primary" id="save_img">设置</button>
			    </div>
		    </div><!-- end of modal-content -->
		  </div><!-- end of modal-dialog modal-lg -->
		</div><!-- end of cover_img_diag -->
	

</div><!-- end of col-sm-8 -->
@include('templates/sidebar')
</div><!-- end of row -->
</div><!-- end of container -->
@include('templates/footer')
