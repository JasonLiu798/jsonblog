/**
 * Created by liujianlong on 15/2/11.
 */
//$().ready(function() {
    angular.module('commentService', [])
        .factory('Comment', function ($http) {
            return {
                // get all the comments
                get: function () {
                    return $http.get('/comment/get?page=1&post_id=72');
                }

                // save a comment (pass in comment data)
                //save : function(commentData) {
                //    return $http({
                //        method: 'POST',
                //        url: '/comment/create',
                //        headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                //        data: $.param(commentData)
                //    });
                //}
                /*
                 // destroy a comment
                 destroy : function(id) {
                 return $http.delete('/api/comments/' + id);
                 }*/
            }

        });
//});