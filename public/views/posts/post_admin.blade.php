@if( is_null( Session::get('user')) )
	@include('templates/header_logout')
@else
	@include('templates/header_login')
@endif

{{ HTML::style('css/post_admin.css') }}

{{ HTML::script('js/admin/admin.js') }}

<div class="container">
<div class="row">
    @include('templates/sidebar_admin')
    <div class="col-md-9 col-sm-9 col-lg-9">
    	<!-- <h3>{{ $title }}</h3> -->
        <div id="batch_delete">
            <form method="post" action="{{url()}}/admin/post/batchdelete" accept-charset="utf-8" role="form" id="batch_delete_form">
                <input type="hidden" name="delete_ids" id="delete_ids" value=""/>
                <button class="btn btn-default" name="batchdelete" id="batchdelete">批量删除</button>
            </form>
        </div>

        <table class="table table-striped" >
        	<tbody>
        	<tr>
                <th width="5%"><input type="checkbox" id="selectall"></th>
                <th width="10%">作者</th>
                <th width="25%">博文名称</th>
                <th width="10%">分类</th>
                <th width="15%">标签</th>
                <th width="10%">评论数量</th>
                <th width="15%">日期</th>
                <th width="10%">操作</th>
            </tr>
        		@foreach($posts as $post)
        	<tr>
        		<td><input type="checkbox" name="id" value="{{ $post->post_id }}"/> </td>
                <td>{{ $post->post_author }}</td>
        		<td>{{ $post->post_title }}</td>

        		<!-- <td>{{ $post->post_summary }}</td> -->
        		<td>
        		@if(!empty($post->category))
        			{{ $post->category->name }}
        		@endif
        		</td>
        		<td>
        		@if(!empty($post->post_tag))
        			@foreach ($post->post_tag as $tag)
       					<span class="tag">{{ $tag->name}}</span>
       				@endforeach
       			@endif
        		</td>
        		<td>{{ $post->post_comment_count }}</td>

                <td>{{ date("Y年m月d日",strtotime($post->post_date)) }}</td>

                <td>
                    <a href="{{url()}}/admin/post/update/{{$post->post_id}}">编辑</a>|
                    <a href="{{url()}}/admin/post/delete/{{$post->post_id}}">删除</a>
                </td>
        	</tr>
        		@endforeach

        	</tbody>
        	<tfoot><tr><td colspan="9">{{ $posts->links() }}</td></tr> </tfoot>
        </table>
    </div>

</div><!-- end of row -->
</div><!-- end of container ng-app controller -->
@include('templates/footer')
