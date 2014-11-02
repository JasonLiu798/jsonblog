<div class="col-sm-3 col-sm-offset-1 blog-sidebar">
	<div class="sidebar-module">
		<h4>近期文章</h4>
		@if( is_array($sidebar->latest_posts) && count($sidebar->latest_posts )>0 )
		<ol class="list-unstyled">
		@foreach($sidebar->latest_posts  as $latest_post)
			<li>
				<a href="{{ url()}}/post/single/{{ $latest_post->ID }}">{{$latest_post->post_title}}</a>
			</li>
		@endforeach
		</ol>
		@endif
	</div>
	
	<div class="sidebar-module">
	<h4>近期评论</h4>
	@if( is_array($sidebar->latest_comments) && count($sidebar->latest_comments )>0 )
		<ol class="list-unstyled">
		@foreach($sidebar->latest_comments as $latest_comment)
			<li>
			{{ $latest_comment->comment_author }}评论了
				<a href="{{ url()}}/post/single/{{ $latest_comment->post_id }}#comment-{{ $latest_comment->comment_id }}">{{$latest_comment->post_title}}</a>
			</li>
		@endforeach
		</ol>
	@endif
	</div>
	
	<div class="sidebar-module">
	<h4>分类归档</h4>
	@if( is_array($sidebar->cat_stats ) && count($sidebar->cat_stats )>0 )
	<ol class="list-unstyled">
		@foreach($sidebar->cat_stats as $cat_stat)
			<li><a href="{{url()}}/post/term/{{ is_null($cat_stat->term_id)?0:$cat_stat->term_id }}">{{ is_null($cat_stat->term_name)?'未分类':$cat_stat->term_name }}</a>({{ $cat_stat->term_count }})</li>
		@endforeach
	</ol>
	@endif
	</div>
	
	<div class="sidebar-module">
	<h4>标签归档</h4>
	@if( is_array($sidebar->tag_stats ) && count($sidebar->tag_stats )>0 )
	<ol class="list-unstyled">
		@foreach($sidebar->tag_stats as $tag_stat)
			<li><a href="{{url()}}/post/term/{{ is_null($tag_stat->term_id)?0:$tag_stat->term_id }}">
			{{ is_null($tag_stat->term_name)?'未分类':$tag_stat->term_name }}</a>
			({{ $tag_stat->term_count }})
			</li>
		@endforeach
	</ol>
	@endif
	</div>
	
	
	
	<div class="sidebar-module">
	<h4>日期归档</h4>
	@if( is_array($sidebar->post_stats )&& count( $sidebar->post_stats  )>0 )
	<ol class="list-unstyled">
	@foreach($sidebar->post_stats  as $post_stat)
		<li><a href="{{url()}}/post/date/{{ $post_stat->post_date_url }}">{{ $post_stat->post_date }}</a>({{ $post_stat->post_count }})</li>
	@endforeach
	</ol>
	@endif
	</div>
	
	<div id="backtotop"></div>
</div><!-- end of col-sm-3 col-sm-offset-1 blog-sidebar -->
