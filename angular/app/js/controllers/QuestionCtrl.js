'use strict';

quizApp.controller('QuestionCtrl', function QuestionCtrl($scope, $location,$cookieStore, Quiz, Session){

    $scope.index = $cookieStore.get('index');//Session.getVal("index");
    $scope.result_id = $cookieStore.get('result_id');//Session.getVal("result_id");
    $scope.total_questions = $cookieStore.get("total_questions");
    $scope.result = Session.getVal("result");
    $scope.hasNext = true;
    $scope.hasPrevious = false;
    $scope.hasFinish = false;
    $scope.showTime = false;
    // Check if quiz is running
    if(typeof($scope.result_id) == "undefined"){
        $location.path('/');
    }

    $scope.init = function(){
        if($scope.result_id){
            var promiseQuestion = Quiz.getQuestion($scope.result_id, $scope.index);
            promiseQuestion.then(function(response){
                $scope.question = response.data.question;
                $scope.uiRefresh();
            });
        }
    };

    $scope.updateRadioSelected = function(index){
        $scope.answers = [];
        for(var i=0;i<$scope.question.choices.length;i++){
            if(index != i) {
                $scope.question.choices[i].selected = false;
            }
        }
        $scope.answers.push(index);
    };

    $scope.updateCheckboxSelected= function(){
        $scope.answers = [];
        for(var i=0;i<$scope.question.choices.length;i++){
             if($scope.question.choices[i].selected == true){
                 $scope.answers.push(i);
             }
        }
    };

    $scope.next = function(result_id, index){
        Quiz.saveAnswers(result_id, index, $scope.answers);
        $scope.index = index+1;
        $scope.answers = [];
        var promiseQuestion = Quiz.getQuestion(result_id, $scope.index);
        promiseQuestion.then(function(response){
            $scope.question = response.data.question;
            if (response.data.time_spent > 0){
               $scope.showTime = true;
               $scope.time_spent = response.data.time_spent;
            }
            $scope.uiRefresh();
        });
        $cookieStore.put('index', $scope.index);
    };

    $scope.back = function(result_id, index){
        Quiz.saveAnswers(result_id, index, $scope.answers);
        $scope.index = index-1;
        $scope.answers = [];
        var promiseQuestion = Quiz.getQuestion(result_id, $scope.index);
        promiseQuestion.then(function(response){
            $scope.question = response.data.question;
            if (response.data.time_spent > 0){
               $scope.showTime = true;
               $scope.time_spent = response.data.time_spent;
            }
            $scope.uiRefresh();
        });
        $cookieStore.put('index', $scope.index);
    };

    $scope.finish = function(result_id, index){
        var promiseSave = Quiz.saveAnswers(result_id, index, $scope.answers);
        promiseSave.then(function(response){
            $scope.result = Quiz.getResult(result_id);
            Session.setVal("result", $scope.result );
            Session.setVal("time_spent", $scope.time_spent);
            $location.path('/result');
        });
    };

    $scope.uiRefresh = function(){
        // Set back/next/finish buttons accordingly
        ($scope.index != 0) ? $scope.hasPrevious = true : $scope.hasPrevious = false;

        if ($scope.index+1 == $scope.total_questions){
            $scope.hasNext = false;
            $scope.hasFinish = true;
        } else {
            $scope.hasNext = true;
            $scope.hasFinish = false;
        }

        if($scope.question.type == 'single'){
            $scope.single = true;
            $scope.multiple = false;
        } else {
            $scope.single = false;
            $scope.multiple = true;
        }

    };

});
