@if( is_null( Session::get('user')) )
	@include('templates/header_logout')
@else
	@include('templates/header_login')
@endif

{{ HTML::script('js/validate/formValidator-4.0.1.min.js') }}
{{ HTML::script('js/validate/formValidatorRegex.js') }}
{{ HTML::script('js/validate/comment_chk.js') }}


<script type="text/javascript">
$().ready(function(){
	$("#cancleReplay").hide();
});

function moveCommentForm(thisID,isBack){
	if(isBack){
		moveDiv( "commentNew","replay_comment");
		$("#cancleReplay").hide();
		$("#comment_parent").attr("id","0");
	}else{
		var desID = thisID+"Comment";
		moveDiv( desID,"replay_comment");
		$("#cancleReplay").show();
// 		var num = new RegExp("[0-9]+");
// 		var res = num.exec(thisID);
 		//console.log();
		$("#comment_parent").attr("value",new RegExp("[0-9]+").exec(thisID)[0] );
	}
	$("#comment_content").focus().select();
	var pos = $("#comment_content").offset().top - $("#replay_comment").height() - 44;
	$('html,body').animate({scrollTop:pos },ANI_SPEED_FAST);
}
</script>

<div class="container">
<div class="row">

	<div class="col-sm-8 blog-main">
		<div class="blog-post">
			<p class="blog-post-meta">
			
			</p>
            <h2 class="blog-post-title">{{$post->post_title }}</h2>
            <p class="blog-post-meta"><a href="{{url()}}/post/single/{{$post->post_id}}">{{date ( "Y-m-d", strtotime ( $post->post_date ) )}}</a> by <a href="{{url()}}/author/{{$post->post_author_id}}">{{$post->post_author }}</a>
            @if(!empty($post->category))
    			@foreach ($post->category as $cat)
    				{{ $cat->name }}
    			@endforeach
    		@else
    			{{{'未分类'}}}
    		@endif
			</p><!-- end of p blog-post-meta -->
			<div class="post_content">
			<p>{{$post->post_content }}</p>
			</div>
			<div class = "post_tag">
			@if(!empty($post->post_tag))
   				<p class = "blog-post-meta">
	    		@foreach ($post->post_tag as $tag)
	   				<span class="label label-default">
	   					{{ $tag->name }}
	   				</span>    
	   			@endforeach
	   			</p>
   			@endif
			</div>
			<div class="netpr_post">
			@if(count($pre_next_post['pre_post'])>0)
			上一文章: <a href="{{url()}}/post/single/{{$pre_next_post['pre_post'][0]->post_id}}">{{ $pre_next_post['pre_post'][0]->post_title }}</a>
			@endif
			
			@if(count($pre_next_post['next_post'])>0)
			下一文章: <a href="{{url()}}/post/single/{{$pre_next_post['next_post'][0]->post_id}}">{{ $pre_next_post['next_post'][0]->post_title }}</a>
			@endif
			</div>
		</div><!-- end of blog-post -->

<?php  
$root = new stdClass();
$root->comment_parent=-1;
$root->comment_ID=0;
?>
<?php function print_comments($node, $comments){?>
	@if($node->comment_ID!=0 )
		<div class="comment" id="comment-{{$node->comment_ID}}">
			<div class="comemnt_meta">
				<div class="comment_title">
					<h4>{{ $node->comment_author }}<br/>
						<small>
						@if( date("d",strtotime($node->comment_date)) === date("d",time()))
							{{ date ( "今日 H:i", strtotime ( $node->comment_date ) )}}
						@else
							{{ date ( "m-d H:i", strtotime ( $node->comment_date ) )}}
						@endif
						</small>
					</h4>
				</div>
				<div class="comment_replay">
					<a href="#" id="replay{{$node->comment_ID}}" onclick="moveCommentForm(this.id,false)">回复</a>
				</div>
			</div>
			<div class="comment_content">
				{{ $node->comment_content }}
			</div>
			<div id="replay{{$node->comment_ID}}Comment"></div>
	@endif
	@foreach($comments as $comm)
		@if($comm->comment_parent == $node->comment_ID)
			<?php print_comments($comm,$comments); ?>
		@endif
	@endforeach
	@if($node->comment_ID!=0)
		</div><!-- end of comment -->
	@endif
<?php }?>

	<div id="commanchor"></div>
	
	<div class="comments" id="comments">
	@if(count($comments)>0)
		<h4>共{{ count($comments) }}条评论</h4>
		<?php print_comments($root,$comments);?>
	@else
		<h4>还没有评论~~</h4>
	@endif
	</div><!-- comments -->	
	
	
	

	<div id="commentNew">
		<div id="replay_comment" class="replay_comment">
			<span id="errorlist"></span>
			{{ Form::open(array('url' => 'comment/create', 'method' => 'post','id'=> 'comment_add_form')) }}
				<a href="#" id="cancleReplay" onclick="moveCommentForm(this.id,true)">取消回复</a>
				<input type="hidden" name="post_id" id="post_id" value="{{ $post->post_id}}" />
				<input type="hidden" name="post_author_id" id="post_author_id" value="{{ $post->post_author_id }}" />
				<input type="hidden" name="comment_parent" id="comment_parent" value="0" />
				<div class="form-group">
				    {{Form::label('comment_author', '姓名')}}
				    {{Form::text('comment_author','',array('class' => 'form-control','id'=>'comment_author')) }}
				    <span id="comment_authorTip" class="help-block"></span>
				</div>
				<div class="form-group">
					{{Form::label('comment_author_email', 'E-Mail')}}
					{{Form::text('comment_author_email', '',array('class' => 'form-control','id'=>'comment_author_email') ) }}
					<span id="comment_emailTip" class="help-block"></span>
				</div>

				<div class="form-group">
					{{Form::label('comment_content', '评论')}}
				    {{Form::textarea('comment_content','', array('class' => 'form-control','rows'=>3,'id'=>'comment_content') ) }}
				    <span id="comment_contentTip" class="help-block"></span>
			  	</div>
			  	{{Form::submit('发表评论',array('class' => 'btn btn-default'))}}
			  	{{Form::reset('重置',array('class' => 'btn btn-default'))}}
			{{ Form::close() }}
		</div><!-- end of replay_comment -->
	</div>
		<!-- </div><!-- end of addcomment -->
	
	
	</div><!-- col-sm-8 blog-main -->
	@include('templates/sidebar')
</div><!-- end of row -->
</div><!-- end of container -->
@include('templates/footer')
