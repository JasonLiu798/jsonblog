
<script type="text/javascript"
	src="<?=base_url().'js/tinymce/tinymce.min.js'?>"></script>
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
	insertdatetime_formats: ["%Y年%m月%d日 %H:%M","%Y年%m月%d日","%H:%M"],
	save_enablewhendirty: true,
    save_onsavecallback: function() {console.log("Save");}

	    
 });

</script>
<div class="container">
	<h2>撰写新文章</h2>

<form role="form" method="post" action="<?=base_url().'posts/create_' ?>" accept-charset="utf-8">
<div class="form-group">
  <label for="post_title">标题</label> 
  <input type="input" name="post_title" class="form-control" />
</div>
<div class="form-group">
	<label for="post_content"></label>
	<textarea name="post_content"></textarea>
</div>

<div class="form-group col-sm-offset-5 col-sm-12"> 
	<input type="submit" name="submit" class="btn btn-default" value="发布" />
	&nbsp;&nbsp;&nbsp;
<!-- </div>
<div class="col-sm-offset-6 col-sm-10">
 -->
	<input type="reset" name="reset" class="btn btn-default"  value="保存草稿" />
</div>

<div class="form-group">
	<label for="category"></label>
</div>



</form>

</div>
