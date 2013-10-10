'use strict';

quizApp.factory('Quiz', function($http, $q, $cookieStore){
    var question;

    return {

        start: function(id, user_id){
            var deffered = $q.defer();
            var data = $.param({quiz_id: id, user_id:user_id});
            $http({method:'POST',
                   url:'/api/quiz/main/start',
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
        },

        getData: function(){
            var deffered = $q.defer();
            $http({method:'GET', url:'/api/quiz/main/index'})
                .success(function(data, status, headers, config){
                    deffered.resolve(data);
                })
                .error(function(data, status, headers, config){
                    deffered.reject(status)
                });
            return deffered.promise;
        },

        getQuestion: function(result_id, index){
            var deffered = $q.defer();

            $http({method:'GET',
                   url:'/api/quiz/main/question',
                   params: {result_id: result_id, index:index}
            })
                .success(function(data, status, headers, config){
                    deffered.resolve(data);
                })
                .error(function(data, status, headers, config){
                    deffered.reject(status)
                });
            return deffered.promise;

        },

        saveAnswers: function(result_id, index, answers){
            var deffered = $q.defer();
            var data = $.param({result_id: result_id, index:index, answers:answers});
            $http({method:'POST',
                   url:'api/quiz/main/answer',
                   data:data,
                   headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
                .success(function(data, status, headers, config){
                    deffered.resolve(data);
                })
                .error(function(data, status, headers, config){
                    deffered.reject(status)
                });
            return deffered.promise;
        },

        getResult: function(result_id){
            var deffered = $q.defer();
            var data = $.param({result_id: result_id});
            $http({method:'POST',
                   url:'api/quiz/main/finish',
                   data:data,
                   headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
                .success(function(data, status, headers, config){
                    deffered.resolve(data);
                })
                .error(function(data, status, headers, config){
                    deffered.reject(status)
                });
            return deffered.promise;
        },

        removeCookies: function(){
            $cookieStore.remove('index');
            $cookieStore.remove('result_id');
            $cookieStore.remove("total_questions");
        }


    }
});