<!--图片上传对话框-->

<!-- 图片裁剪库-->
{{ HTML::script('js/lib/jquery.Jcrop.min.js') }}
{{ HTML::style('css/jquery.Jcrop.min.css') }}
{{ HTML::style('css/tool/image_upload.css') }}
<!-- 异步上传文件库-->
{{ HTML::script('js/lib/ajaxfileupload.v2.js') }}
<!--文件上传逻辑-->
{{ HTML::script('js/tools/image_upload.js') }}

<div  id="image_upload_diag" class="modal fade bs-example-modal-lg" tabindex="-1"
	  role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" >
<div class="modal-dialog modal-lg">
<div class="modal-content">

	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
			<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
		</button>
        <h4 class="modal-title" id="myModalLabel">设置博文摘要图片</h4>
    </div>
    <div class="modal-body">
    	<form class="form-inline" role="form">
	    	<h5>选择文件</h5>
	    	<input type="hidden" id="x" name="x" />
			<input type="hidden" id="y" name="y" />
			<input type="hidden" id="w" name="w" />
			<input type="hidden" id="h" name="h" />
			<div class="form-group">
		        <input type="file" name="up_image_file" id="up_image_file" value="请选择图片" />
		        <p class="help-block">请选择jpg、png、bmp、gif格式图片上传</p>
	        </div>
	        <div class="form-group">
				<button type="button" class="btn btn-primary" id="upload_image">上传</button>
	        </div>
        </form><!-- end of form -->

        <div id="upload_img_and_preview">
	        <div id="upload_img_pane">
<!-------------------------main image------------------------->
	        	<img id="upload_main_img" src="{{url()}}/img/space450x250.jpg"
					 alt="上传图片">
	        </div>
	        <div id="preview-pane">
	        	<div class="preview-container">
<!-------------------------preview image------------------------->
			    	<img id="upload_img_preview" src="{{url()}}/img/space250x150.jpg"
						 class="jcrop-preview" alt="Preview" />
				</div>
				<!-- <img src="" class="jcrop-preview" alt="Preview" /> -->
			</div>
		</div>
		<div class="form-group">
	      	<button type="button" class="btn btn-primary" id="cut_img">剪裁</button>
	      	<button type="button" class="btn btn-default" id="cut_img_back">撤销</button>
      	</div>
    </div><!--upload_img_and_preview-->

    <div class="modal-footer">
        <button type="button" class="btn btn-default" id="cancle_save_img">取消</button>
        <button type="button" class="btn btn-primary" id="save_img">保存</button>
    </div>

</div><!-- end of modal-content -->
</div><!-- end of modal-dialog modal-lg -->
</div><!-- end of cover_img_diag -->