@if( is_null( Session::get('user')) )
	@include('templates/header_logout')
@else
	@include('templates/header_login')
@endif

<div class="container">
<div class="row">
	<h3>{{ $title }}</h3>
    <table class="table table-striped" >
    	<tbody>
    	<tr><th width="5%">编号</th> <th width="10%">作者</th>
    	<th width="10%">博文名称</th> <th width="10%">博文日期</th>
    	<th width="20%">内容概览</th>
    	<th width="10%">分类</th> <th width="20%">标签</th>
    	<th width="10%">评论数量</th> <th width="10%">操作</th></tr>
    		@foreach($posts as $post)
    		<tr>
    		<td>{{ $post->post_id }}</td> <td>{{ $post->post_author }}</td> 
    		<td>{{ $post->post_title }}</td> <td>{{ $post->post_date }}</td> 
    		<td>{{ $post->post_content }}</td>
    		<td>
    		@if(!empty($post->category))
    			@foreach ($post->category as $cat)
    				<a href="{{url()}}/{{ $cat->term_id }}">{{ $cat->name }}</a>
    			@endforeach
    		@endif
    		</td>
    		<td>
    		@if(!empty($post->post_tag))
    			@foreach ($post->post_tag as $tag)
   					<a href="{{url()}}/{{ $tag->term_id }}">{{ $tag->name }}</a>
   				@endforeach
   			@endif
    		</td>
    		<td>{{ $post->post_comment_count }}</td>
    		<td>
    			<a href="{{url()}}/post/update/{{$post->post_id}}">编辑</a>|
    			<a href="{{url()}}/post/delete/{{$post->post_id}}">删除</a>
    		</td>
    		</tr>	
    		@endforeach
    	
    	</tbody>
    	<tfoot><tr><td colspan="9">{{ $posts->links() }}</td></tr> </tfoot>
    </table>
    
</div><!-- end of row -->
</div><!-- end of container ng-app controller -->
@include('templates/footer')
