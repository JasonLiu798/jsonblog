@include('templates/header')

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
	var pos = $("#comment_content").offset().top - $("#replay_comment").height();
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

<?php  
$stack =& Stack::stack_initialize();
$root = new stdClass();
$root->parent=-1;
$root->id=0;
$node = &$root;
Tree::get_childs($node,$comments);
function print_comms($comments){
	if(!empty($comments)){
		echo "<h4>COMMS:</h4>";
		foreach ($comments as $comm) {
			echo "ID:".$comm->comment_ID.",parent:".$comm->comment_parent."<br>";//[".$comm->content."]<br>";//,isleaf:".$comm->isleaf."<br>";
		}
	}else{
		echo "<h4>COMMS NULL</h4>";
	}
}

echo 'comments:'."\n";
print_comms($comments);
echo 'childs:'."\n";
print_comms($node->childs);
?>
		@if(count($comments)>0)
		<div class="comments">
			<h4>共{{ count($comments) }}条评论</h4>
			@while ( $node!=null|| Stack::stack_size($stack)!=0 )
				@while( $node!=null )
					<!-- output root's child,not root -->
					@if(!is_null($node) && $node->id != 0)
					<div class="comment">
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
					@endif <!-- endif id!=0 -->
					<?php  
					Stack::stack_push($stack, $node); 
					$node = Tree::get_onechild($node->childs); 
					?>
				@endwhile
				@if( Stack::stack_size($stack)>0 )
					<?php  $node = Stack::stack_pop($stack); ?>
						@if(!is_null($node) && $node->id != 0)
							</div><!-- end of comment -->
						@endif
					@if(!is_null($node->childs))
						<?php  $node = Tree::get_onechild($node->childs); ?>
					@else
						<?php  $node = null; ?>
					@endif
				@endif
			@endwhile
		</div><!-- comments -->
		@endif
		
	<div id="commentNew">
		<div id="replay_comment" class="replay_comment">
			<span id="errorlist"></span>
			{{ Form::open(array('url' => 'comment/create', 'method' => 'post','id'=> 'comment_add_form')) }}
				<a href="#" id="cancleReplay" onclick="moveCommentForm(this.id,true)">取消回复</a>
				<input type="hidden" name="post_id" id="post_id" value="{{$post->ID}}" />
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

