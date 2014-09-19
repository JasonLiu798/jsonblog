var commentApp = angular.module('commentApp', ['mainCtrl', 'commentService'],function($interpolateProvider) {
    $interpolateProvider.startSymbol('<%ag');
    $interpolateProvider.endSymbol('%>');
});