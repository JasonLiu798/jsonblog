@if( is_null( Session::get('user')) )
	@include('templates/header_logout')
@else
	@include('templates/header_login')
@endif

{{ HTML::style('css/post_admin.css') }}

<div class="container">
<div class="row">
    @include('templates/sidebar_admin')
    <div class="col-md-9 col-sm-9 col-lg-9">
    	<!-- <h3>{{ $title }}</h3> -->
        <table class="table table-striped" >
        	<tbody>
        	<tr>
                <th width="5%"><input type="checkbox" name=""></th> 
                <th width="10%">作者</th>
                <th width="30%">博文名称</th> 
                <th width="10%">分类</th> 
                <th width="15%">标签</th>
                <th width="10%">评论数量</th> 
                <th width="20%">日期</th>
                <!-- <th width="10%">操作</th> -->
            </tr>
        		@foreach($posts as $post)
        	<tr>
        		<td><input type="checkbox" name="" value="{{ $post->post_id }}"/> </td>
                <td>{{ $post->post_author }}</td> 
        		<td>{{ $post->post_title }}</td>

        		<!-- <td>{{ $post->post_summary }}</td> -->
        		<td>
        		@if(!empty($post->category))
        			@foreach ($post->category as $cat)
        				{{ $cat->name }}
        			@endforeach
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
<!--         		<td>
        			<a href="{{url()}}/post/update/{{$post->post_id}}">编辑</a>|
        			<a href="{{url()}}/admin/post/delete/{{$post->post_id}}">删除</a>
        		</td> -->
                <td>{{ date("Y年m月d日",strtotime($post->post_date)) }}</td> 
        	</tr>
        		@endforeach
        	
        	</tbody>
        	<tfoot><tr><td colspan="9">{{ $posts->links() }}</td></tr> </tfoot>
        </table>
    </div>

</div><!-- end of row -->
</div><!-- end of container ng-app controller -->
@include('templates/footer')
