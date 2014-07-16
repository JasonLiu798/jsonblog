
<!-- <script type="text/javascript" src="<?=url('js/comment_check_ajax.js') ?>"></script>
 -->
<?=$header ?>

<script type="text/javascript">
$(document).ready(function(){
	jQuery("input#comment_author").emptyValue("请输入姓名");
	//jQuery("input#comment_content").emptyValue("请输入评论内容");
	//$("input#comment_content").emptyValue("请输入评论内容");
});
</script>

<div class="container">

	<div class="page-header">
		<table class="table">
			<tbody>
				<tr>
					<td><h2><?=$post[0]->post_title ?></h2></td>
					<td class="info"><?php
					
					date_default_timezone_set ( "Asia/Shanghai" );
					echo date ( "Y-m-d", strtotime ( $post[0]->post_date ) )?>
</td>
				</tr>
			</tbody>
		</table>

	</div>

	<p>
	<?=$post[0]->post_content?>
	</p>
	<p>
	
	
	<table class="table">
		<tbody>
<?php  foreach ( $comments as $comment ): ?>
			<tr>
				<td><h3><?=$comment->comment_author ?></h3></td>
			</tr>
			<tr>
				<td><p><?=$comment->comment_content?></p>
					<p><?=date ( "Y-m-d", strtotime ( $comment->comment_date ) )?></p>
				</td>
			</tr>
<?php endforeach ?>
		</tbody>
	</table>

	</p>


	<p>
		<span id="chkHint"></span>
	</p>
	<table>
		<tbody>
<?php /*
echo form_open ( 'comments/create', array (
		'id' => 'comment_form' 
) );*/
?>
<input type="hidden" name="post_id"
				value="<?=$post[0]->ID?>" />
			<tr>
				<td><label for="comment_author">姓名</label></td>
				<td><input type="input" name="comment_author" id="comment_author" /></td>
			</tr>
			<tr>
				<td><label for="comment_content">评论</label></td>
				<td><textarea name="comment_content" id="comment_content"></textarea></td>
			</tr>
			<tr>
				<td><input type="submit" name="submit" value="发表评论" /></td>
				<td><input type="reset" name="reset" value="重置" /></td>
			</tr>
		</tbody>
	</table>
	</form>

</div>
<?=$footer ?>
