@include('templates/header_login')

{{ HTML::script('js/tinymce/tinymce.min.js') }}

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
</script>

<div class="container">
<div class="row">
<div class="col-sm-8">
	<h2>{{Lang::get('post.NEW_POST') }}</h2>
	{{ Form::open(array('url' => 'posts/create/do','accept-charset'=>'utf-8','role'=>'form','method'=>'post', )) }}  
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
		  <option>1</option>
		  <option>2</option>
		  <option>3</option>
		</select>
	</div>
	<div class="form-group">
		{{ Form::label('post_tag',Lang::get('post.POST_TAG')) }}
		{{ Form::text('post_tag', '',array('class' => 'form-control')) }} 
		{{ Lang::get('post.INFO_COMMA')}}
	</div>
	
	<div class="form-group col-sm-offset-5 col-sm-12">
		{{Form::submit( Lang::get('post.PUBLISH') ,array('class' => 'btn btn-default') ) }}
		&nbsp;&nbsp;&nbsp;
		<input type="button" name="save_draft" class="btn btn-default"  value="保存草稿" />
	</div>
	
{{ Form::close() }}
</div><!-- end of col-sm-8 -->
@include('templates/sidebar')
</div><!-- end of row -->
</div><!-- end of container -->
@include('templates/footer')
