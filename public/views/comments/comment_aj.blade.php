@if( is_null( Session::get('user')) )
	@include('templates/header_logout')
@else
	@include('templates/header_login')
@endif


<script src="{{url()}}/js/controllers/commentCtrl.js"></script> <!-- load our controller -->
<script src="{{url()}}/js/services/commentService.js"></script> <!-- load our service -->
<script src="{{url()}}/js/app.js"></script>

<style>
.td{width:100px;overflow:hidden}

</style>

<!-- <body ng-app="commentApp" ng-controller="commentController"> -->


<div class="container" ng-app="commentApp" ng-controller="commentController">
<div class="row">
	
	<!-- <form ng-submit="submitComment()"> <!-- ng-submit will disable the default form action and use our function --
        <!-- AUTHOR --
        <div>
            <input type="text" class="form-control input-sm" name="comment_author" id="comment_author" ng-model="commentData.author" placeholder="Name">
        </div>
 
        <!-- COMMENT TEXT --
        <div>
            <input type="text" class="form-control input-lg" name="comment_content" id="comment_content" ng-model="commentData.text" placeholder="Say what you have to say">
        </div>
         
        <!-- SUBMIT BUTTON --
        <div class="form-group text-right">    
            <button type="submit" class="btn btn-primary btn-lg">提交</button>
        </div>
    </form> -->
    
    <!-- LOADING ICON =============================================== -->
    <!-- show loading icon if the loading variable is set to true -->
    
    <!-- 
    <p ng-show="loading"><span class="fa fa-meh-o fa-5x fa-spin"></span></p>
 <div ng-hide="loading" ng-repeat="comment in comments"></div>
  -->
    <!-- THE COMMENTS =============================================== -->
    <!-- hide these comments if the loading variable is true -->
    <table class="table table-striped" >
    	<tbody>
    	<tr><th width="10%">编号</th> <th width="10%">评论时间</th> <th width="20%">评论人</th> <th width="30%">评论内容</th> 
    		 <th width="20%">评论博文</th> <th width="10%">操作</th></tr>
    	
    	<tr ng-repeat="comment in comments| | offset: currentPage*itemsPerPage | limitTo: itemsPerPage">
    		<td><%ag comment.comment_ID %></td> <td><%ag comment.comment_author %></td> <td><%ag comment.comment_content %></td> 
    		<td><%ag comment.comment_date %></td> <td><%ag comment.comment_post_title %></td> 
    		<td><a href="#" ng-click="deleteComment(comment.comment_ID)">删除</a>|<a href="" ng-click=""></a></td>
    	</tr>	
    	</tbody>
		<tfoot>
			<td colspan="0">
			<div class="pagination">
			<ul>
			<li ng-class="prevPageDisabled()">
				<a href ng-click="prevPage()"> « Prev</a>
			</li>
			<li ng-repeat="n in range()" ng-class="{active: n == currentPage}" ng-click="setPage(n)">
			<a href="#"><%ag n+1 %></a> </li>
			<li ng-class="nextPageDisabled()">
			<a href ng-click="nextPage()">Next »</a>
			</li> 
			</ul>
			</div> 
			</td>
		</tfoot>
		
    </table>
 	

</div><!-- end of row -->
</div><!-- end of container ng-app controller -->