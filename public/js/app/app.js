/**
 * Created by liujianlong on 15/2/11.
 */
//$().ready(function() {
    var commentApp = angular.module('commentApp', ['commentCtrl', 'commentService'],
        function ($interpolateProvider) {
            $interpolateProvider.startSymbol('<%aj');
            $interpolateProvider.endSymbol('%>');
        });
//});

//var sampleApp = angular.module('sampleApp', [], function($interpolateProvider) {
//    $interpolateProvider.startSymbol('<%');
//    $interpolateProvider.endSymbol('%>');
//});