'use strict';

quizApp.controller('ResultCtrl', function ResultCtrl($scope, $location, $cookieStore, Quiz, Session){

    $scope.result = Session.getVal("result");
    $scope.time_spent = Session.getVal("time_spent");
    $scope.total_questions = $cookieStore.get("total_questions");
    Quiz.removeCookies();

    // Check if quiz is running
    if(typeof($scope.result) == "undefined"){
        $location.path('/');
    }

});

