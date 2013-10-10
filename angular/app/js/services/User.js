'use strict';

quizApp.factory('User', function($http, $q){
    var question;

    return {

        createUser: function(user){

            var deffered = $q.defer();
            var data = $.param({
                    firstname: user.firstname,
                    lastname: user.lastname,
                    email: user.email
                });
            $http({method:'POST',
                   url:'/api/user/manage/signup',
                   data : data,
                   headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
                .success(function(data, status, headers, config){
                    deffered.resolve(data);
                })
                .error(function(data, status, headers, config){
                    deffered.reject(status)
                });
            return deffered.promise;

        }

    }
});
