@if( is_null( Session::get('user')) )
	@include('templates/header_logout')
@else
	@include('templates/header_login')
@endif
<script>
$().ready(function(){
    
    $('#selectall').click(function(){
        if( $(this).prop("checked") == true){
            $('[name=cid]:checkbox').each(function(){
                $(this).prop("checked",true);
            });
        }else{
            $('[name=cid]:checkbox').each(function(){
                $(this).prop("checked",false);
            });
        }
    });
});

</script>
<div class="container">
<div class="row">
    @include('templates/sidebar_admin')
    <div class="col-md-9 col-sm-9 col-lg-9">
    	<!-- <h3>{{ $title }}</h3> -->
        <table class="table table-striped" >
        	<tbody>
        	<tr>
                <th width="10%"><input type="checkbox" id="selectall"></th>
                <th width="20%">评论人</th>
        	    <th width="45%">评论</th>
        	    <th width="25%">回应给</th>
                <!-- <th width="10%">评论时间</th> -->
                <!-- <th width="10%">操作</th> -->
            </tr>
        		@foreach($comments as $comment)
        	<tr>
                <td><input type="checkbox" name="cid" value="{{ $comment->comment_ID }}"/> </td>
        		
                <td>{{ is_null($comment->comment_author_reg)?$comment->comment_author:$comment->comment_author_reg }}</td>
        		<td>提交于<a href="">{{ date("Y年m月d日 h:m",strtotime( $comment->comment_date ))  }}</a></br>
                    {{ $comment->comment_content }}</td>
        		<td>{{ $comment->post_title }}</td>
        		<!-- <td><a href="{{url()}}/comment/delete/{{$comment->comment_ID}}">删除</a></td> -->
        	</tr>	
        		@endforeach
        	
        	</tbody>
        	<tfoot><tr><td colspan="6">{{ $comments->links() }}</td></tr> </tfoot>
        </table>
    </div>
    
</div><!-- end of row -->
</div><!-- end of container ng-app controller -->
@include('templates/footer')
