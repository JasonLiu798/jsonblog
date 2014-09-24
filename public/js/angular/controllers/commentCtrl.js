angular.module('commentCtrl', []).controller('commentController', 
function($scope, $http, Comment) {
	// 处理提交表单的函数
	// SAVE A COMMENT ======================================================
	$scope.submitComment = function() {
		$scope.loading = true;
		// 保存评论。在表单间传递评论
		// 使用在服务中创建的函数
		Comment.save($scope.commentData).success(function(data) {
			// 如果成功，我们需要刷新评论列表
			Comment.get().success(function(getData) {
				$scope.comments = getData;
				$scope.loading = false;
			});
		}).error(function(data) {
			console.log(data);
		});
	};

	// 处理删除评论的函数
	// DELETE A COMMENT ====================================================
	$scope.deleteComment = function(id) {
		$scope.loading = true;
		// 使用在服务中创建的函数
		Comment.destroy(id).success(function(data) {
			// 如果成功，我们需要刷新评论列表
			Comment.get().success(function(getData) {
				$scope.comments = getData;
				$scope.loading = false;
			});

		});
	};
	
	
	
	//Comment show function
	//$scope.commentData = {};
	// 调用显示加载图标的变量
	$scope.loading = true;
	// 先获取所有的评论，然后绑定它们到$scope.comments对象         // 使用服务中定义的函数
	// GET ALL COMMENTS ====================================================
	Comment.get().success(function(data) {
		$scope.comments = data;
		$scope.loading = false;
	});
	
	//Process pagenation
	$scope.itemsPerPage = 5;
	$scope.currentPage = 0;
	$scope.commentData = [];
	for (var i=0; i<50; i++) {
		$scope.commentData.push({id: i, name: "name "+ i, description: "description " + i });
	}
	￼￼￼￼￼￼￼￼￼$scope.prevPage = function() { 
		if ($scope.currentPage > 0) {
			$scope.currentPage--; 
		}
	}
	$scope.prevPageDisabled = function() {
		return $scope.currentPage === 0 ? "disabled" : "";
	}


	$scope.pageCount = function() {
		return Math.ceil($scope.items.length/$scope.itemsPerPage)-1;
	}


	$scope.nextPage = function() {
		if ($scope.currentPage < $scope.pageCount()) {
			$scope.currentPage++; 
		}
	}

	$scope.nextPageDisabled = function() {
		return $scope.currentPage === $scope.pageCount() ? "disabled" : "";
	}
});









