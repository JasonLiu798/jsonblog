@include('templates/header')

{{ HTML::style('css/admin.css') }}
{{ HTML::script('js/admin/admin.js') }}

<div class="container">
<div class="row">
    @include('templates/sidebar_admin')
    <div class="col-md-9 col-sm-9 col-lg-9">
    	<!-- <h3>{{ $title }}</h3> -->
        <div class="operations">
            <!--<button class="btn btn-primary" data-toggle="modal" data-target="#image_upload_diag"
                    id="upload_image_btn">上传图片</button>-->
            <form method="post" action="{{url()}}/admin/image/batchdelete" accept-charset="utf-8" role="form" id="batch_delete_form">
                <input type="hidden" name="delete_ids" id="delete_ids" value=""/>
                <button class="btn btn-default" name="batchdelete" id="batchdelete">批量删除</button>
            </form>
        </div>

        <table class="table table-striped" >
        	<tbody>
        	<tr>
                <th width="5%"><input type="checkbox" id="selectall"></th>
                <th width="10%">图片名</th>
                <th width="10%">文件路径(点击查看图片)</th>
                <th width="10%">类型</th>
                <th width="5%">宽度(px)</th>
                <th width="5%">高度(px)</th>
                <th width="10%">大小(k)</th>
                <th width="10%">创建日期</th>
                <th width="10%">所属文章</th>
                <th width="20%">操作</th>
            </tr>
        		@foreach($images as $img)
        	<tr>
        		<td><input type="checkbox" name="id" value="{{ $img->iid }}"/> </td>
                <td>{{ $img->name }}</td>
        		<td><a href="{{url().Constant::$UPLOAD_IMG_DIR.$img->filename }}">{{ $img->filename }}</a></td>
        		<td>{{ $img->filetype }}</td>
                <td>{{ $img->width }}</td>
                <td>{{ $img->height }}</td>
                <td>{{ $img->size }}</td>
                <td>{{ date("Y年m月d日",strtotime($img->add_date))}}</td>
                <td><a href="{{url()}}/post/single/{{ is_null($img->post)?'':$img->post->ID}}">{{ is_null($img->post)?'':$img->post->post_title }}</a></td>
                <td>
                    <!-- <a href="{{url()}}/admin/image/update/{{$img->iid}}">编辑</a>| -->
                    <a href="{{url()}}/admin/image/delete/{{$img->iid}}">删除</a>
                </td>
        	</tr>
        		@endforeach

        	</tbody>
        	<tfoot><tr><td colspan="9">{{ $images->links() }}</td></tr> </tfoot>
        </table>
    </div>
</div><!-- end of row -->
</div><!-- end of container ng-app controller -->
@include('templates/footer')
