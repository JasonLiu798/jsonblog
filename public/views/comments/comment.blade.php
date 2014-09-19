@if( is_null( Session::get('user')) )
	@include('templates/header_logout')
@else
	@include('templates/header_login')
@endif


<script src="{{url()}}/js/controllers/commentCtrl.js"></script> <!-- load our controller -->
<script src="{{url()}}/js/services/commentService.js"></script> <!-- load our service -->
<script src="{{url()}}/js/app.js"></script>

<!-- <body ng-app="commentApp" ng-controller="commentController"> -->


<div class="container" ng-app="commentApp" ng-controller="commentController">
<div class="row">
<div class="col-md-8">
	
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
    
    
    <p ng-show="loading"><span class="fa fa-meh-o fa-5x fa-spin"></span></p>
 
    <!-- THE COMMENTS =============================================== -->
    <!-- hide these comments if the loading variable is true -->
    <div ng-hide="loading" ng-repeat="comment in comments">
    	<table class="table">
    		
    	</table>
        <h3>Comment # <%ag comment.comment_date %> <small>by <%ag comment.comment_author %></h3>
        <p><%ag comment.comment_content %></p>
 
        <p><a href="#" ng-click="deleteComment(comment.comment_ID)">Delete</a></p>
    </div>
    
</div>    

</div><!-- end of row -->
</div><!-- end of container ng-app controller -->