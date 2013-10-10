'use strict';

quizApp.factory('Session', function($q){

    var value = [];

    return {

        setVal: function (key, val){
            value[key] = val;
        },

        getVal: function(key){
            return value[key];
        }

    }
});
