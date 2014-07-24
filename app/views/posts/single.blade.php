@include('templates/header')

{{ HTML::script('js/validate/formValidator-4.0.1.min.js') }}
{{ HTML::script('js/validate/formValidatorRegex.js') }}
{{ HTML::script('js/validate/comment_chk.js') }}


<script type="text/javascript">
$().ready(function(){
	//do something
	$("div.replay_comment").hide();
	//css("visibility","hidden");//hide();
	$("div.replay").click(function(){
		$("div.replay_comment").show();
	});
	$("div.replay").append();
	
// 	$("div.replay").mouseleave(function(){
// 		$("div.replay_form").("visibility","hidden");
// 	});
});
</script>

<div class="container">
<div class="row">

	<div class="col-sm-8 blog-main">
		<div class="blog-post">
			<p class="blog-post-meta">
			
			</p>
            <h2 class="blog-post-title">{{$post->post_title }}</h2>
            <p class="blog-post-meta">{{date ( "Y-m-d", strtotime ( $post->post_date ) )}} by <a href="#">{{$post->post_author }}</a>
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
			上一文章: aa
			下一文章: bb
			</div>
		</div><!-- end of blog-post -->
		
		@if(count($comments)>0)
		<div class="comments">
			<h4>共{{count($comments)}}条评论</h4>
			@foreach ( $comments as $comment )
			<div class="comment">
				<h4>{{$comment->comment_author}}<small>
				@if( date("d",strtotime($comment->comment_date)) === date("d",time()))
					{{date ( "今日 H:i", strtotime ( $comment->comment_date ) )}}
				@else
					{{date ( "m-d H:i", strtotime ( $comment->comment_date ) )}}
				@endif
				</small>
				</h4>
				<p>{{$comment->comment_content}}</p>
				
				<p><a href="" id="replay">回复</a></p>
			</div>	
			@endforeach
		</div><!-- comments -->
		@endif

		<div class="addcomment">
		<h3>评论</h3>
		<span id="errorlist"></span>
			{{ Form::open(array('url' => 'comment/create', 'method' => 'post','id'=> 'comment_add_form')) }}
				<input type="hidden" name="post_id" id="post_id" value="{{$post->ID}}" />
				<input type="hidden" name="comment_parent" id="comment_parent" value="0" />
				<div class="form-group">
				    {{Form::label('comment_author', '姓名')}}
				    {{Form::text('comment_author','',array('class' => 'form-control','id'=>'comment_author')) }}
				    <span id="comment_authorTip" class="help-block"></span>
				</div>
				<div class="form-group">
					{{Form::label('comment_email', 'E-Mail')}}
					{{Form::text('comment_email', '',array('class' => 'form-control','id'=>'comment_email') ) }}
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
		</div><!-- end of addcomment -->
	
	
	</div><!-- col-sm-8 blog-main -->
	@include('templates/sidebar')
</div><!-- end of row -->
</div><!-- end of container -->
@include('templates/footer')

