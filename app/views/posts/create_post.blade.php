@include('templates/header_login')

{{ HTML::script('js/tinymce/tinymce.min.js') }}
{{ HTML::script('js/post/post_tag_add.js') }}
{{ HTML::style('css/create_post.css') }}
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
    	          "link | insertdatetime | preview | save "
    	      ],
	plugins : 'advlist autolink link image lists charmap print preview insertdatetime charmap code textcolor table emoticons save',
	insertdatetime_formats: ["%Y{{Lang::get('tools.YEAR')}}%m{{Lang::get('tools.MONTH')}}%d{{Lang::get('tools.DAY')}} %H:%M","%Y{{Lang::get('tools.YEAR')}}%m{{Lang::get('tools.MONTH')}}%d{{Lang::get('tools.DAY')}}","%H:%M"],
	save_enablewhendirty: true,
    save_onsavecallback: function() {console.log("Save");}	    
 });

$(document).ready(function(){
	$('#post_tag').blur(function(){
		var post_text = 1;
		$('#post_tag_id').val();
	});
});


</script>

<div class="container">
<div class="row">
<div class="col-sm-8">
	<h2>{{Lang::get('post.NEW_POST') }}</h2>
<form method="post" action="{{url()}}/posts/create/do" accept-charset="utf-8" role="form" id="create_post_form">

	<input type="hidden" name="post_tag_id" id="post_tag_id" value="" />
	<div class="form-group">
		{{ Form::label('post_title', Lang::get('post.POST_TITLE')) }}
		{{ Form::text('post_title', '', array('class' => 'form-control')) }}
	</div>
	
	<div class="form-group">
		{{Form::textarea('post_content','', array('class' => 'form-control','rows'=>15,'id'=>'post_content') ) }}
	</div>
	
	<div class="form-group">
		<label for="category"></label>
		{{ Form::label('category', Lang::get('post.CATEGORY') ) }}
		<select class="form-control" name="category" id="category">
			@foreach($category as $cat)
			  <option value="{{$cat->term_id }}">{{ $cat->name }}</option>
			@endforeach
		</select>
	</div>
	
	
	<div class="tagbox">
		<h5>{{Lang::get('post.POST_TAG')}}<small>{{ Lang::get('post.POST_TAG_ADD_INFO')}}</small></h5>
		<div class="clearfix"></div>
        <div id="tags"></div>
        <div class="clearfix"></div>
        <!-- <input type="text" value="" name="tag" style="border:none;outline:none"/> -->
        
		<input type="text"  name="post_tag" id="post_tag" value="" class="form-control"/>
		<span class="help-block" id="post_tag_alert"></span>
        <div class="old">
            <h5>{{Lang::get('post.USED_TAG')}}</h5>
            <div class="clearfix"></div>
            <span class="label label-primary" id="234" name="标签">标签</span>
            <span class="label label-primary" id="34" name="风景">风景</span>
            <span class="label label-primary" id="21" name="音乐">音乐</span>
        </div>
    </div>
    
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
	
	<div class="form-group col-sm-offset-5 col-sm-12">
		<input type="submit" value="{{Lang::get('post.PUBLISH')}}" class="btn btn-default"/>
		&nbsp;&nbsp;&nbsp;
		<input type="button" name="save_draft" class="btn btn-default"  value="保存草稿" />
	</div>
	
</form>
</div><!-- end of col-sm-8 -->
@include('templates/sidebar')
</div><!-- end of row -->
</div><!-- end of container -->
@include('templates/footer')
