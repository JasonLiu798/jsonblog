var commentApp = angular.module('commentApp', ['commentCtrl', 'commentService'],function($interpolateProvider) {
    $interpolateProvider.startSymbol('<%ag');
    $interpolateProvider.endSymbol('%>');
});

commentApp.filter('offset', function() {
	return function(input, start) {
		￼￼￼￼￼￼￼￼￼￼￼￼￼￼￼￼￼￼start = parseInt(start, 10);
		￼￼￼￼￼￼￼￼return input.slice(start); 
	};
});