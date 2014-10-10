@if( is_null( Session::get('user')) )
	@include('templates/header_logout')
@else
	@include('templates/header_login')
@endif

@include('templates/logo')

<div class="container">
<div class="row">


<div class="col-md-8 blog-main posts_wrap" id="posts_wrap">

	
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
	<div class="post">

		<h2 class="blog-post-title"><a href="{{url('post/single/'.$posts_item->post_id)}}">{{$posts_item->post_title}}</a>
		</h2>
		
		<p class="blog-post-meta">
			作者：<a href="#">{{$posts_item->post_author }}</a>/
    		日期：<a href="{{url('post/single/'.$posts_item->post_id)}}">{{date ( "Y年m月d日", strtotime ( $posts_item->post_date ) ) }}</a>/
    		分类：
    		@if(!empty($posts_item->category))
    			@foreach ($posts_item->category as $cat)
    				<a href="{{url()}}/{{ $cat->term_id }}">{{ $cat->name }}</a>
    			@endforeach
    		@endif
			</a>/
			@if( $posts_item->comment_count >0)
				<a href="{{url('post/single/'.$posts_item->post_id)}}/#commanchor">评论{{$posts_item->comment_count}}条</a>
			@else
				<a href="{{url('post/single/'.$posts_item->post_id)}}/#commanchor">添加评论</a>
			@endif
    	</p>
    	<!-- 标签 -->
    	
    	@if(!empty($post->post_tag))
   			<p class = "blog-post-meta">
    		@foreach ($post->post_tag as $tag)
   				<span class="label label-default">
   					<a href="{{url()}}/{{ $tag->term_id }}">{{ $tag->name }}</a>
   				</span>
   			@endforeach
   			</p>
   		@endif
		<!-- 内容 -->
		<div class="post_content">
			{{$posts_item->post_content}}
		    @if (strlen( $posts_item->post_content )>100)
			<div class="post_readmore">
				 <a href="{{url('post/single/'.$posts_item->post_id) }}" >Read More &raquo;</a>
			</div><!-- readmore -->
		    @endif
	    </div><!-- post content -->
   </div><!-- post -->
   
@endforeach

{{ $posts->links() }}

<!-- 
	<ul class="pagination">
		<li><a href="#">&laquo;</a></li>
		
		<li class="active" ><a href="#">1 <span class="sr-only">(current)</span> </a></li>
		<li><a href="#">2</a></li>
  		<li><a href="#">&raquo;</a></li>
	</ul>
    -->
</div><!-- container  posts_wrap col-9 -->

@include('templates/sidebar')

</div><!-- row -->
</div><!-- container  -->

@include('templates/footer')
