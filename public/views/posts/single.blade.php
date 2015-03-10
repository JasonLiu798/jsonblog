@include('templates/header')

{{ HTML::script('js/validate/formValidator-4.0.1.min.js') }}
{{ HTML::script('js/validate/formValidatorRegex.js') }}

{{ HTML::style('css/single_post.css') }}


{{ HTML::script('js/comment/create_comment.js') }}
{{ HTML::script('js/validate/comment_chk.js') }}

{{ HTML::script('bower_components/angular/angular.js') }}

<script type="text/javascript">

</script>

<div class="container">
<div class="row">

	<div class="col-sm-8 blog-main">
		<div class="blog-post">
			<p class="blog-post-meta">

			</p>
			@if(!empty($post->post_img_name))
				<div class="post_cover">
					<img class="post_cover_img" src="{{ $post->cover_img_url }}"/>
				</div>
			@endif
            <h2 class="blog-post-title">{{$post->post_title }}</h2>
            <p class="blog-post-meta"><a href="{{url()}}/post/single/{{$post->post_id}}">{{date ( "Y-m-d", strtotime ( $post->post_date ) )}}</a> by <a href="{{url()}}/index">{{$post->post_author }}</a>

            @if(!is_null( $post->category) )
    			<a href="{{url()}}/post/term/{{ $post->category->term_id }}">{{ $post->category->name }}</a>
    		@else
    			<a href="{{url()}}/post/term/1">未分类</a>
    		@endif
			</p><!-- end of p blog-post-meta -->
			<div class="post_content">
			<p>{{html_entity_decode( $post->post_content , ENT_QUOTES); }}</p>
			</div>
			<div class = "post_tag">
			@if(!empty($post->post_tag))
   				<p class = "blog-post-meta">
	    		@foreach ($post->post_tag as $tag)
	   				<span class="tag">
	   					<a href="{{url()}}/post/term/{{ $tag->term_id}}">{{ $tag->name }}</a>
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




<div id="commanchor"></div>

<div class="comments" id="comments">
@if(count($comments)>0)
			<h4>共{{ count($comments) }}条评论</h4>
	@foreach($comments as $comm)
		<div class="comment" id="comment-{{$comm->comment_id}}">
			<!--评论 Meta-->
			<div class="comemnt_meta">
				<div class="comment_title">
					<h4>{{ $comm->comment_author }}<br/>
						<small>
							@if( date("d",strtotime($comm->comment_date)) === date("d",time()))
								{{ date ( "今日 H:i", strtotime ( $comm->comment_date ) )}}
							@else
								{{ date ( "m-d H:i", strtotime ( $comm->comment_date ) )}}
							@endif
						</small>
					</h4>
				</div>

				<div class="comment_replay">
					<a href="#" id="replay{{$comm->comment_id}}" onclick="moveCommentForm(this.id,false)">回复</a>
				</div>
			</div><!--end of comemnt_meta-->

			<div class="comment_content">
				{{ $comm->comment_content }}
			</div>
			<!--回复框-->
			<div id="replay{{$comm->comment_id}}Comment"></div>
			<!-- 子评论 -->
			@if( !is_null($comm->child_comments ) && count($comm->child_comments )>0)
				<div class="comment_childs">
					@foreach($comm->child_comments as $child )
						<div class="comment_child">
							<span class="child_comment_author">
								<h5>{{ $child->comment_author }}</h5>
								</span>
							<span class="child_comment_content">
								{{ $child->comment_content }}
								</span>
							<span class="child_comment_date">
								@if( date("d",strtotime($child->comment_date)) === date("d",time()))
									 {{ date ( "今日 H:i", strtotime ( $child->comment_date ) )}}
								@else
									  {{ date ( "m-d H:i", strtotime ( $child->comment_date ) )}}
								@endif
							</span>
							<!--回复框-->
							<div id="replay{{$child->comment_id}}Comment"></div>
						</div>
					@endforeach
				</div>
			@endif

		</div><!-- end of comment -->
	@endforeach
	{{ $comments->links(); }}
@else
			<h4>还没有评论~~</h4>
@endif
</div><!-- comments -->


		<div id="commentNew">
			<div id="replay_comment" class="replay_comment">
				<span id="errorlist"></span>
				{{ Form::open(array('url' => 'comment/create', 'method' => 'post','id'=> 'comment_add_form')) }}
					<a href="#" id="cancleReplay" onclick="moveCommentForm(this.id,true)">取消回复</a>
					@include('templates/validate_res')
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
					<button type="button" class="btn btn-primary"
							id="create_comment_submit">评论</button>
					<button type="reset" class="btn btn-default">重置</button>
				{{ Form::close() }}
			</div><!-- end of replay_comment -->
		</div><!-- end of commentNew -->

</div><!-- col-sm-8 blog-main -->
	{{$sidebar}}
</div><!-- end of row -->
</div><!-- end of container -->
@include('templates/footer')

