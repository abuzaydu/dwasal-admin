(function(){
    var app = angular.module('smartpos', [ ]);

    app.directive('stringToNumber', function() {
        return {
            require: 'ngModel',
            link: function(scope, element, attrs, ngModel) {
              ngModel.$parsers.push(function(value) {
                return '' + value;
              });
              ngModel.$formatters.push(function(value) {
                return parseFloat(value, 10);
              });
            }
        };
    });

    app.controller("SearchItemCtrl", [ '$scope', '$http', '$filter', '$timeout', function($scope, $http, $filter, $timeout) {
        $scope.wiptemp = [ ];
        $scope.newwiptemp = { };

        $scope.getData = function(){
            $http({
                method: 'GET',
                url: 'api/wiptemp'
            }).then(function (response) {
                $scope.wiptemp = response.data.temps;
                console.log($scope.wiptemp);
            });
        };

        $scope.getData();
        
        $scope.addwipTemp = function(item, newwiptemp) {
            $http({
                method: 'POST',
                url : 'api/wiptemp',
                data: {wip_type: item.name}
            }).then(function (response){
               $scope.getData(); 
            });
        }

        $scope.updatewipTemp = function(newwiptemp) {
            $http({
                method: 'PUT',
                url : 'api/wiptemp/' + newwiptemp.id,
                data : {produced: newwiptemp.produced, finished_qty: newwiptemp.finished_qty, wip_damage: newwiptemp.wip_damage}
            }).then(function(response) {
                console.log(response);
                $scope.getData();
            }, function(error){
                console.log(error);
                alert('shit happen');
            });
        }   

        $scope.removewipTemp = function(id) {
            $http({
                method: 'DELETE',
                url : 'api/wiptemp/' +id 
            }).then(function(response) {
                $scope.getData();
            });
        }
    }]);    
})();