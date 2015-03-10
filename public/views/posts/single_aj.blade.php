@include('templates/header')

{{ HTML::script('js/validate/formValidator-4.0.1.min.js') }}
{{ HTML::script('js/validate/formValidatorRegex.js') }}

{{ HTML::style('css/single_post.css') }}


{{ HTML::script('js/comment/create_comment.js') }}
{{ HTML::script('js/validate/comment_chk.js') }}

{{ HTML::script('bower_components/angular/angular.js') }}

{{ HTML::script('js/app/app.js') }}
{{ HTML::script('js/controller/mainCtrl.js') }}
{{ HTML::script('js/service/commentService.js') }}


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

<div class="comments" id="comments"  ng-app="commentApp" ng-controller="commentController">
	<!--新建评论-->
	<div id="commentNew">
		<div class="replay_comment">
			{{--<form ng-submit="processForm()" id="comment_add_form" method="post"--}}
				  {{--name="comment_add_form" >--}}
				{{--action="{{url()}}/comment/create">--}}
				@include('templates/validate_res')
				<!--评论博文相关信息-->
				<input type="hidden" name="post_id" id="post_id" value="{{ $post->post_id }}"/>
				<input type="hidden" name="post_author_id" id="post_author_id" value="{{ $post->post_author_id }}"/>
				<!--回复的评论，单独评论=0，-->
				<input type="hidden" name="comment_replay" id="comment_replay" value="0"/>

				<input type="hidden" name="comment_author_id" id="comment_author_id"
					   value="{{!is_null($user)?$user->user_id:0}}"
					   ng-model="commentData.comment_author_id" />
				@if( is_null($user))
					<div class="form-group">
						<label>姓名</label>
						<input type="text" name="comment_author" class="form-control"
							   ng-model="commentData.comment_author">
						<span id="comment_authorTip" class="help-block"></span>
					</div>
					<div class="form-group">
						<label>E-mail</label>
						<input type="text" name="comment_author_email" class="form-control"
							   ng-model="commentData.comment_author_email">
						<span id="comment_emailTip" class="help-block"></span>
					</div>
				@endif

				<div class="form-group">
					<label>评论</label>
						<textarea  id="comment_content" name="comment_content"
								   class="form-control" rows="3"
								   ng-model="commentData.comment_content"></textarea>
					<span id="comment_contentTip" class="help-block"></span>
				</div>
				{{--<button type="button" ng-click="processForm()" class="btn--}}
				{{--btn-primary">评论</button>--}}
				<button type="button" ng-click="create_new_comment()"
						class="btn btn-primary">评论</button>

				{{--<button type="button" class="btn btn-primary"--}}
				{{--id="create_comment_submit">评论</button>--}}
				<button type="reset" class="btn btn-default">重置</button>
			{{--</form>--}}
		</div><!-- end of replay_comment -->
	</div><!-- end of commentNew -->

	{{--@if(count($comments)>0)--}}
			{{--<h4>共{{ count($comments) }}条评论</h4>--}}
		<p ng-show="loading"><img src="{{url()}}/img/tool/loading_circle.gif"/></p>

		<div ng-hide="loading" ng-repeat="comment in comments">
			<div class="comment" id="comment-<%aj comment.comment_id %>">
				<!--评论 Meta-->
				<div class="comemnt_meta">
					<div class="comment_title">
						<h4><%aj comment.comment_author %><br/>
							<small>
								<%aj comment.comment_date %>
							</small>
						</h4>
					</div>

					<div class="comment_replay">
						<a href="#" id="replay<%aj comment.comment_id %>" onclick="moveCommentForm
						(this.id,false)">回复</a>
					</div>
				</div><!--end of comemnt_meta-->

				<div class="comment_content">
					<%aj comment.comment_content %>
				</div>

				<!--回复框出现处-->
				{{--<div id="replay<%aj comment.comment_id %>Comment">--}}
				<div id="replayComment">
					<form ng-submit="processForm()" id="comment_add_form" method="post"
						  name="comment_add_form" >
						{{--action="{{url()}}/comment/create">--}}
						@include('templates/validate_res')
						<!--评论博文相关信息-->
						<input type="hidden" name="post_id" id="post_id" value="{{ $post->post_id }}"/>
						<input type="hidden" name="post_author_id" id="post_author_id" value="{{ $post->post_author_id }}"/>
						<!--回复的评论，单独评论=0，-->
						<input type="hidden" name="comment_replay" id="comment_replay" value="0"/>


						<input type="hidden" name="comment_author_id" id="comment_author_id"
							   value="{{!is_null($user)?$user->user_id:0}}"
							   ng-model="commentData.comment_author_id" />
						@if( is_null($user))
							<div class="form-group">
								<label>姓名</label>
								<input type="text" name="comment_author" class="form-control"
									   ng-model="commentData.comment_author">
								<span id="comment_authorTip" class="help-block"></span>
							</div>
							<div class="form-group">
								<label>E-mail</label>
								<input type="text" name="comment_author_email" class="form-control"
									   ng-model="commentData.comment_author_email">
								<span id="comment_emailTip" class="help-block"></span>
							</div>
						@endif

						<div class="form-group">
							<label>评论</label>
						<textarea  id="comment_content" name="comment_content"
								   class="form-control" rows="3"
								   ng-model="commentData.comment_content"></textarea>
							<span id="comment_contentTip" class="help-block"></span>
						</div>
						{{--<button type="button" ng-click="processForm()" class="btn--}}
						{{--btn-primary">评论</button>--}}
						<button type="submit"
								class="btn btn-primary">评论</button>

						{{--<button type="button" class="btn btn-primary"--}}
						{{--id="create_comment_submit">评论</button>--}}
						<button type="reset" class="btn btn-default">重置</button>
					</form>

				</div>

					<div class="comment_childs">
						<div ng-repeat="child_comment in comment.child_comments">
							<div class="comment_child">
							<span class="child_comment_author">
								<h5><%aj child_comment.comment_author %></h5>
								</span>
							<span class="child_comment_content">
								<%aj child_comment.comment_content %>
								</span>
							<span class="child_comment_date">
								<%aj child_comment.comment_date %>
							</span>
								<!--回复框-->
								<div id="replayy<%aj child_comment.comment_id %>Comment"></div>
							</div>
						</div>
					</div>
			</div><!-- end of comment -->
			{{--<p><a href="#" ng-click="deleteComment(comment.id)">Delete</a></p>--}}
		</div>
			{{--<h4>还没有评论~~</h4>--}}



	{{--<div class="new_comment_child">--}}
		{{--<span class="child_comment_author">--}}
			{{--<h5><%aj child_comment.comment_author %></h5>--}}
			{{--</span>--}}
		{{--<span class="child_comment_content">--}}
			{{--<%aj child_comment.comment_content %>--}}
			{{--</span>--}}
		{{--<span class="child_comment_date">--}}
			{{--<%aj child_comment.comment_date %>--}}
		{{--</span>--}}
		{{--<!--回复框-->--}}
		{{--<div id="replayy<%aj child_comment.comment_id %>Comment"></div>--}}
	{{--</div>--}}
</div><!-- comments -->



</div><!-- col-sm-8 blog-main -->
	{{$sidebar}}
</div><!-- end of row -->
</div><!-- end of container -->
@include('templates/footer')

