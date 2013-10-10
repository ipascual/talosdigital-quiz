'use strict';

var quizApp = angular.module('quizApp', ['ngCookies'], function($compileProvider) {
  // configure new 'compile' directive by passing a directive
  // factory function. The factory function injects the '$compile'
  $compileProvider.directive('compile', function($compile) {
    // directive factory creates a link function
    return function(scope, element, attrs) {
      scope.$watch(
        function(scope) {
           // watch the 'compile' expression for changes
          return scope.$eval(attrs.compile);
        },
        function(value) {
          // when the 'compile' expression changes
          // assign it into the current DOM
          element.html(value);

          // compile the new DOM and link it to the current
          // scope.
          // NOTE: we only compile .childNodes so that
          // we don't get into infinite loop compiling ourselves
          $compile(element.contents())(scope);
        }
      );
    };
  })})
    .config(function($routeProvider){
        $routeProvider.when('/',
            {
                templateUrl:'templates/start.html',
                controller: 'QuizCtrl'
            });
         $routeProvider.when('/question',
            {
                templateUrl:'templates/question.html',
                controller: 'QuestionCtrl'
            });
        $routeProvider.when('/result',
            {
                templateUrl:'templates/result.html',
                controller: 'ResultCtrl'
            });
        $routeProvider.otherwise({redirectTo:'/'});
    }
);


/*window.onbeforeunload = function (e) {
  var message = "You will lose your results, do you want to continue?",
  e = e || window.event;
  // For IE and Firefox
  if (e) {
    e.returnValue = message;
  }

  // For Safari
  return message;
};*/


/*// Declare app level module which depends on filters, and services
angular.module('myApp', ['myApp.filters', 'myApp.services', 'myApp.directives', 'myApp.controllers']).
  config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/view1', {templateUrl: 'partials/partial1.html', controller: 'MyCtrl1'});
    $routeProvider.when('/view2', {templateUrl: 'partials/partial2.html', controller: 'MyCtrl2'});
    $routeProvider.otherwise({redirectTo: '/view1'});
  }]);*/
