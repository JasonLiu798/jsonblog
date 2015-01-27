@include('templates/header')

{{ HTML::script('js/tinymce/tinymce.min.js') }}
{{ HTML::script('js/tinymce/uploadimg/uploadimg.js') }}

{{ HTML::script('js/lib/jquery.Jcrop.min.js') }}
{{ HTML::script('js/lib/ajaxfileupload.v2.js') }}
<!-- form ajax submit-->
{{ HTML::script('js/lib/jquery-form.js') }}
{{ HTML::style('css/create_post.css') }}
{{ HTML::style('css/jquery.Jcrop.min.css') }}

{{ HTML::script('js/post/create_post.js') }}

<script type="text/javascript">
</script>


<div class="container">
<div class="row">
<div class="col-sm-8">
	<h2>{{ $title  }}</h2>

<form id="create_post_form" method="post" action="{{url()}}/admin/post/api/save/{{$post_id}}"
	  accept-charset="utf-8" role="form" >
	<input type="hidden" name="method" value="save"/>
	<input type="hidden" id="is_draft" name="is_draft" value="false"/>
	<input type="hidden" name="post_id" value="{{$post_id}}"/>
	<input type="hidden" id="post_tag_ids" name="post_tag_ids" value="{{ is_null($post)?'':
	(is_null($post->post_tag_id)?'':$post->post_tag_id) }}" />
	<input type="hidden" id="set_cover" name="set_cover" value="{{ is_null($post)?'false':($post->post_cover_img==0?"false":"true") }}" />
	<input type="hidden" id="cover_img_id" name="cover_img_id" value="{{ is_null($post)?'':$post->post_cover_img }}"/><!-- 保存用 -->
	<div class="form-group">
		<label for="post_title">{{Lang::get('post.POST_TITLE') }}</label>
		<input type="text" class="form-control" id="post_title" name="post_title"
			   value="{{ is_null($post)?'':$post->post_title }}"/>
	</div>

	<div class="form-group">
		<textarea rows="15" id="post_content_ta" name="post_content_ta" class="form-control">
			{{ is_null($post)?'':$post->post_content }}
		</textarea>
	</div>

	<div class="form-group">
		<h5>{{Lang::get('post.CATEGORY')}}</h5>
		<select class="form-control" name="category" id="category">
			@if(!is_null($category) && count($category)>0)
				@foreach($category as $cat)
					<option id="category{{$cat->term_id}}" value="{{$cat->term_id }}" @if(isset
					($cat->selected)) @if($cat->selected==1)  selected @endif @endif>{{
					$cat->name }}</option>
				@endforeach
			@else
				<option value="1">{{ Lang::get('term.NO_PARENT') }}</option>
			@endif
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
			@if(!is_null($post) )
				@if(!is_null($post->post_tag) && count($post->post_tag)>0 )
					@foreach($post->post_tag as $tag)
						<span name="{{$tag->name}}" class="tag tag_new" value="{{$tag->term_id}}">{{$tag->name}}&nbsp;X</span>
					@endforeach
				@endif
			@endif
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
    </div>
    </div>

	<div class="form-group">
		<!-- 设置博文封面图片 -->
		<input type="button" class="btn btn-default" id="submit_button" data-toggle="modal" data-target="#cover_img_diag" value="设置摘要图片"/>
		<div>
			<img id="cover_img_preview_inpage" src=""/>
		</div>
	</div>

	<div class="form-group col-sm-offset-5 col-sm-12">
		<input type="button" id="save_post" name="save_post" class="btn btn-default"
			   value="{{Lang::get('post.PUBLISH')}}"/>
		&nbsp;&nbsp;&nbsp;
		<input type="button" id="save_draft" name="save_draft" class="btn btn-default"
			   value="保存草稿" />
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
