@include('templates/header')

{{ HTML::script('js/admin/admin.js') }}
{{ HTML::style('css/admin.css') }}

<div class="container">
<div class="row">
    @include('templates/sidebar_admin')
    <div class="col-md-9 col-sm-9 col-lg-9">
    	<!-- <h3>{{ $title }}</h3> -->
        <div class="operations">
            <form method="post" action="{{url()}}/admin/comment/batchdelete" accept-charset="utf-8" role="form" id="batch_delete_form">
                <input type="hidden" name="delete_ids" id="delete_ids" value=""/>
                <button class="btn btn-default" name="batchdelete" id="batchdelete">批量删除</button>
            </form>
        </div>

        <table class="table table-striped" >
        	<tbody>
        	<tr>
                <th width="10%"><input type="checkbox" id="selectall"></th>
                <th width="20%">评论人</th>
        	    <th width="35%">评论</th>
        	    <th width="25%">回应给</th>
                <!-- <th width="10%">评论时间</th> -->
                <th width="10%">操作</th>
            </tr>
        		@foreach($comments as $comment)
        	<tr>
                <td><input type="checkbox" name="id" value="{{ $comment->comment_id }}"/> </td>

                <td>{{ $comment->comment_author }}</td>
        		<td>提交于<a href="">{{ date("Y年m月d日 h:m",strtotime( $comment->comment_date ))  }}</a></br>
                    {{ $comment->comment_content }}</td>

        		<td>
                    @if( $comment->post_id ==0)
                        来自留言板
                    @else
                        {{ $comment->post_title }}
                        {{--<a href="{{url()}}/post/single/{{$comment->comment_post_id}}#comment-{{$comment->comment_id}}">--}}
                        {{--</a>--}}
                    @endif
                </td>
                <td>
                    <a href="{{url()
                    }}/admin/comment/delete/{{$comment->comment_id}}?page={{$comments->getCurrentPage()}}">删除</a>
                </td>
        		<!-- <td><a href="{{url()}}/comment/delete/{{$comment->comment_id}}">删除</a></td> -->
        	</tr>
        		@endforeach

        	</tbody>
        	<tfoot><tr><td colspan="6">{{ $comments->links() }}</td></tr> </tfoot>
        </table>
    </div>

</div><!-- end of row -->
</div><!-- end of container ng-app controller -->
@include('templates/footer')
