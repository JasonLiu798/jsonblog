@include('templates/header')

{{ HTML::style('css/index.css') }}

{{ HTML::style('css/search.css') }}

<script type="text/javascript">
$().ready(function(){
	// $("#cancleReplay").hide();
});

</script>

<div class="container">
<div class="row">

	<div class="col-sm-8 blog-main">
	<!--
		<div class="search_diag">
			<form method="post" action="{{url()}}/post/search" accept-charset="utf-8" role="form" id="search_form_diag">
				<input name="page" type="hidden" value="1"/>
				<div class="col-sm-8">
					<input name="searchtext" class="form-control" type="text" placeholder="搜索" />
				</div>
				<div class="col-sm-4">
					<button type="submit" class="btn btn-default">搜索</button>
				</div>

			</form>
		</div>-->
		<h3>“{{$searchtext}}”的搜索结果</h3>
		<div class="res_posts">
			@if( !is_null($posts) )
				@foreach($posts as $post)
					<div class="post_no_cover">
						<h2 class="blog-post-title"><a href="{{url('post/single/'.$post->post_id)}}">{{$post->post_title}}</a></h2>
						<!-- meta -->
						<div class="blog-post-meta">
							<a href="#">{{$post->post_author }}</a>/
				    		<a href="{{url('post/single/'.$post->post_id)}}">{{date ( "Y年m月d日", strtotime ( $post->post_date ) ) }}</a>/
				    		@if(!empty($post->category))
				    			<a href="{{url()}}/post/term/{{ $post->category->term_id }}">{{ $post->category->name }}</a>/
				    		@else
					    		<a href="{{url()}}/post/term/1">未分类</a>/
					    	@endif

							@if( $post->comment_count >0)
								<a href="{{url('post/single/'.$post->post_id)}}/#commanchor">{{$post->comment_count}}条评论</a>
							@else
								<a href="{{url('post/single/'.$post->post_id)}}/#commanchor">添加评论</a>
							@endif

							@if(!empty($post->post_tag))
							<div class="post_tags">
					    		@foreach ($post->post_tag as $tag)
					   				<span class="post_tag"><!-- 标签 -->
					   					<a href="{{url()}}/post/term/{{ $tag->term_id }}">{{ $tag->name }}</a>
					   				</span>
					   			@endforeach
					   		</div>
				   			@endif
				    	</div><!-- end of blog-post-meta -->
				    	<!-- end of meta -->
				    	<!-- 摘要 -->
							<div class="post_content">
								{{ $post->post_summary }}<a class="read_more" href="{{url('post/single/'.$post->post_id) }}" >
									继续阅读&nbsp;<span class="glyphicon glyphicon-arrow-right"></span></a>
						    </div><!-- post content -->
					</div><!-- post_no_cover -->
				@endforeach
			@endif
		</div>
		@if( $totalpage >1)
		<div class="pages">
			<nav>
				<ul class="pagination">
					@if( $page >1 && $page<= $totalpage )
						<li><a href="{{url()}}/post/search?page={{$page-1}}&searchtext={{$searchtext}}">&laquo;</a></li>
					@else
						<li class="disable"><span>&laquo;</span></li>
					@endif
					@for ($i = 1; $i <=$totalpage; $i++)
						<li @if($i == $page) class="active" @endif><a href="{{url()}}/post/search?page={{$i}}&searchtext={{$searchtext}}">{{$i}}</a></li>
					@endfor
					@if( $page<$totalpage && $page>=1 )
						<li><a href="{{url()}}/post/search?page={{$page+1}}&searchtext={{$searchtext}}">&raquo;</a></li>
					@else
						<li class="disable"><span>&raquo;</span></li>
					@endif
				</ul>
			</nav>
		</div>
		@endif



	</div><!-- col-sm-8 blog-main -->
	@include('templates/sidebar')
</div><!-- end of row -->
</div><!-- end of container -->
@include('templates/footer')

