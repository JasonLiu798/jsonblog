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
    	<tr><th width="10%">编号</th> <th width="20%">评论人</th>
    	<th width="30%">评论内容</th> <th width="10%">评论时间</th>
    	<th width="20%">评论博文</th> <th width="10%">操作</th></tr>
    		@foreach($comments as $comment)
    		<tr>
    		<td>{{ $comment->comment_ID }}</td> <td>{{ is_null($comment->comment_author_reg)?$comment->comment_author:$comment->comment_author_reg }}</td> 
    		<td>{{ $comment->comment_content }}</td> <td>{{ $comment->comment_date }}</td>
    		<td>{{ $comment->post_title }}</td> 
    		<td><a href="{{url()}}/comment/delete/{{$comment->comment_ID}}">删除</a></td>
    		</tr>	
    		@endforeach
    	
    	</tbody>
    	<tfoot><tr><td colspan="6">{{ $comments->links() }}</td></tr> </tfoot>
    </table>
    
</div><!-- end of row -->
</div><!-- end of container ng-app controller -->
@include('templates/footer')
