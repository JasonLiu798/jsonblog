@if( is_null( Session::get('user')) )
	@include('templates/header_logout')
@else
	@include('templates/header_login')
@endif

@include('templates/logo')
{{ HTML::style('css/index.css') }}

<div class="container">
<div class="row">
<!-- BLOGS -->
<div class="col-md-6 col-md-offset-1 posts_wrap">

@if(!is_null($term4title))
	<div class = "post term_title">
		<h3>‘<a href="{{url()}}/{{$term4title[0]->term_id}}">{{ $term4title[0]->name }}</a>’ {{ $term4title[0]->taxonomy==='category'?'分类':'标签' }}归档 </h3>
	</div>
@endif

@if(!is_null($date4title))
	<div class = "post term_title">
		<h3>‘<a href="{{url()}}/date/{{$date4title['link'] }}">{{ $date4title['title'] }}</a>’归档 </h3>
	</div>
@endif

@if(!is_null($user4title))
	<div class = "post term_title">
		<h3>作者‘<a href="{{url()}}/author/{{$user4title->ID}}">{{ $user4title->user_login }}</a>’归档</h3>
	</div>
@endif

@foreach ($posts as $posts_item) 
	<div class="post_pane">
	
		@if(!is_null( $posts_item->post_img_name ) )
		<!-- 有封面，无摘要 --> 
		<div class="post_with_cover" >
			<div class="cover_img">
				<img src="{{url().Constant::$UPLOAD_IMG_DIR.$posts_item->post_img_name }}" />
			</div>
			<div class="cover_img_black">
				<img class="cover_bg" src="{{url().'/img/cover_bg.v9.png' }}" />
			</div>
			<div class="post_title_meta" onclick="location.href='{{url('post/single/'.$posts_item->post_id)}}';">
				<h1 class="blog-post-title"><a href="{{url('post/single/'.$posts_item->post_id)}}">{{$posts_item->post_title}}</a></h1>
				<div class="blog-post-meta">
					<a href="#">{{$posts_item->post_author }}</a>/
		    		<a href="{{url('post/single/'.$posts_item->post_id)}}">{{date ( "Y年m月d日", strtotime ( $posts_item->post_date ) ) }}</a>/
		    		@if(!empty($posts_item->category))
		    			@foreach ($posts_item->category as $cat)
		    				<a href="{{url()}}/{{ $cat->term_id }}">{{ $cat->name }}</a>/
		    			@endforeach
		    		@endif
					
					@if( $posts_item->comment_count >0)
						<a href="{{url('post/single/'.$posts_item->post_id)}}/#commanchor">{{$posts_item->comment_count}}条评论</a>
					@else
						<a href="{{url('post/single/'.$posts_item->post_id)}}/#commanchor">添加评论</a>
					@endif
				
				
					@if( !empty($posts_item->post_tag) )
					<div class="post_tags">
			    		@foreach ($posts_item->post_tag as $tag)
			   				<span class="post_tag"><!-- 标签 -->
			   					<a href="{{url()}}/{{ $tag->term_id }}">{{ $tag->name }}</a>
			   				</span>
			   			@endforeach
					</div>
		   			@endif
		    	</div>
				
			</div>
		</div><!-- end of post_with_cover -->
		
		@elseif( !is_null($posts_item->post_summary) )
		<!-- 无封面，有摘要 -->
		<div class="post_no_cover">
			<h2 class="blog-post-title"><a href="{{url('post/single/'.$posts_item->post_id)}}">{{$posts_item->post_title}}</a></h2>
			<!-- meta -->
			<div class="blog-post-meta">
				<a href="#">{{$posts_item->post_author }}</a>/
	    		<a href="{{url('post/single/'.$posts_item->post_id)}}">{{date ( "Y年m月d日", strtotime ( $posts_item->post_date ) ) }}</a>/
	    		@if(!empty($posts_item->category))
	    			@foreach ($posts_item->category as $cat)
	    				<a href="{{url()}}/{{ $cat->term_id }}">{{ $cat->name }}</a>
	    			@endforeach
	    		@endif
				/
				@if( $posts_item->comment_count >0)
					<a href="{{url('post/single/'.$posts_item->post_id)}}/#commanchor">评论{{$posts_item->comment_count}}条</a>
				@else
					<a href="{{url('post/single/'.$posts_item->post_id)}}/#commanchor">添加评论</a>
				@endif
				
				@if(!empty($posts_item->post_tag))
		    		@foreach ($posts_item->post_tag as $tag)
		   				<span class="post_tag"><!-- 标签 -->
		   					<a href="{{url()}}/{{ $tag->term_id }}">{{ $tag->name }}</a>
		   				</span>
		   			@endforeach
	   			@endif
	    	</div><!-- end of blog-post-meta -->
	    	<!-- end of meta -->
	    	<!-- 摘要 -->
				<div class="post_content">
					{{ $posts_item->post_summary }}<a href="{{url('post/single/'.$posts_item->post_id) }}" >继续阅读</a>
			    </div><!-- post content -->
		</div><!-- post_no_cover -->
		@endif
		
   </div><!-- post_pane -->
   
@endforeach
<!-- 
	<ul class="pagination">
		<li><a href="#">&laquo;</a></li>
		
		<li class="active" ><a href="#">1 <span class="sr-only">(current)</span> </a></li>
		<li><a href="#">2</a></li>
  		<li><a href="#">&raquo;</a></li>
	</ul>
    -->
    {{ $posts->links() }}
</div><!-- container  posts_wrap col-9 -->

@include('templates/sidebar')

</div><!-- row -->
</div><!-- container  -->

@include('templates/footer')
