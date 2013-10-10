'use strict';

quizApp.controller('QuizCtrl', function QuizCtrl($scope, $location, $cookieStore, Quiz, User, Session){

    var promiseQuizes = Quiz.getData();
    promiseQuizes.then(function(response){
        $scope.quizes = response.data;
    });

    $scope.user = {};

    $scope.chooseQuiz = function(quizId) {
        $scope.quizId = quizId;
    }

    // Check if quiz is running
    if(typeof($cookieStore.get('result_id')) != "undefined"){
        $location.path('/question');
    }

    $scope.startQuiz = function(){
        var process = User.createUser($scope.user);
        process.then(function(response){
            process = Quiz.start($scope.quizId, response.data.user_id);
            process.then(function(response){
                $cookieStore.put('result_id', response.data.result_id);
                $cookieStore.put('index', 0);
                $cookieStore.put('total_questions', response.data.total_questions);
                $location.path('/question');
            });
        });
    };



});
