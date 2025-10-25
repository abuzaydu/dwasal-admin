var appname = angular.module('smartpos', []);
appname.controller('appCtrl', ['$scope',
  function($scope) {
    $scope.greeting = { text: 'Hello' };
}]);