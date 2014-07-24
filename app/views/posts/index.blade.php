@include('templates/header')
@include('templates/logo')

<div class="container">
<div class="row">


<div class="col-md-9 posts_wrap" id="posts_wrap">

@foreach ($posts as $posts_item) 
	<div class="post">
		
		<h2 class="blog-post-title"><a href="{{url('post/'.$posts_item->ID)}}">{{$posts_item->post_title}}</a>
		</h2>
		
		<p class="blog-post-meta">
			作者：<a href="#">{{$posts_item->post_author }}</a>/
    		日期：{{date ( "Y年m月d日", strtotime ( $posts_item->post_date ) ) }}/
    		分类：<a href="#">
    		@if(!empty($post->category))
    			@foreach ($post->category as $cat)
    				{{ $cat->name }}
    			@endforeach
    		@else
    			{{{'未分类'}}}
    		@endif
			</a>/
			<a href="#">暂无评论</a>
    	</p>
    	<!-- 标签 -->
    	
    	@if(!empty($post->post_tag))
   			<p class = "blog-post-meta">
    		@foreach ($post->post_tag as $tag)
   				<span class="label label-default">
   					{{ $tag->name }}
   				</span>    
   			@endforeach
   			</p>
   		@endif
		<!-- 内容 -->
		<div class="post_content">
			{{$posts_item->post_content}}
	        
		    @if (strlen( $posts_item->post_content )>100)
			<div class="post_readmore">
				 <a href="{{url('post/'.$posts_item->ID) }}" >Read More &raquo;</a>
			</div><!-- readmore -->
		    @endif
	    </div><!-- post content -->
   </div><!-- post -->
@endforeach
</div><!-- container  posts_wrap col-9 -->

@include('templates/sidebar')

</div><!-- row -->
</div><!-- container  -->
<!-- 
<script type="text/javascript">
	/** 
	 *调整边栏高度与所有 
	 *
	 */
	$().ready(function(){
		var sh = $("#sidebar").height();
		//var pwh = document.getElementById("posts_wrap").offsetHeight;
		var pwh = $("#posts_wrap").get(0).offsetHeight;
		
		$("#sidebar").css("height", Math.max(pwh,sh)+"px");
	
	//var sidebarHeight = document.getElementById("sidebar").style.height;
	//var postsHeight = document.getElementById("posts_wrap").offsetHeight;//offsetHeight;
	//document.getElementById("sidebar").style.height = Math.max(sidebarHeight,postsHeight) + "px";
	//alert(sidebarHeight+","+ postsHeight);
	//document.getElementById("posts").offsetHeight + "pt"; 
</script> -->

@include('templates/footer')
