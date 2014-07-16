

<?=$header ?>
<?=$logo ?>
<?=$sidebar ?>
<div class="container col-md-10 posts_wrap" id="posts_wrap">
<?php 
//print_r($posts);
//print($posts[0]->post_content);

date_default_timezone_set ( "Asia/Shanghai" );
foreach ($posts as $posts_item): 
	$content=$posts_item->post_content;

//print_r($posts_item['post_content']);
?>

	<div class="post">
		<h2 class="title"><a href="<?=url('post/'.$posts_item->ID) ?>"><?=$posts_item->post_title ?></a>&nbsp;</h2>
		
		<div class="post_content">
			<?php echo $content?>
	        <?php //strlen($content)>100?substr($content,0,299).'...':$content ?>
	        
		    <?php if(strlen($content)>100){ ?>
			<div class="post_readmore">
				 <a href="<?=url('post/'.$posts_item->ID) ?>" >Read More &raquo;</a>
			</div><!-- readmore -->
		    <?php }?>
	    </div><!-- post content -->
    
    <div class="post_meta clearfix">
    	<ul class="post_date">
    		<li><a href="#"><?=date ( "Y-m-d", strtotime ( $posts_item->post_date ) )?></a></li>
    	</ul>
    	<ul class="post_tag">
    		<li>TAG</li>
    	</ul>
    	<ul class="post_cat">
    		<li>CAT</li>
    	</ul>
    	<ul class="post_comment">
    		<li>评论</li>
    	</ul>
    </div><!-- post_meta -->
   </div><!-- post -->
<?php endforeach ?>
</div><!-- container  posts_wrap-->
</div>
<!--</div> row -->
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
	});
	//var sidebarHeight = document.getElementById("sidebar").style.height;
	//var postsHeight = document.getElementById("posts_wrap").offsetHeight;//offsetHeight;
	//document.getElementById("sidebar").style.height = Math.max(sidebarHeight,postsHeight) + "px";
	//alert(sidebarHeight+","+ postsHeight);
	//document.getElementById("posts").offsetHeight + "pt"; 
</script>

<?=$footer ?>
