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
        $scope.mwiptemp = [ ];
        $scope.newmwiptemp = { };

        $scope.getData = function(){
            $http({
                method: 'GET',
                url: 'api/mwiptemp'
            }).then(function (response) {
                $scope.mwiptemp = response.data.temps;
                console.log($scope.mwiptemp);
            });
        };

        $scope.getData();
        
        $scope.addmwipTemp = function(item, newmwiptemp) {
            $http({
                method: 'POST',
                url : 'api/mwiptemp',
                data: {mwip_type: item.name}
            }).then(function (response){
               $scope.getData(); 
            });
        }

        $scope.updatemwipTemp = function(newmwiptemp) {
            $http({
                method: 'PUT',
                url : 'api/mwiptemp/' + newmwiptemp.id,
                data : {produced: newmwiptemp.produced, used: newmwiptemp.used, dam_qty: newmwiptemp.dam_qty}
            }).then(function(response) {
                console.log(response);
                $scope.getData();
            }, function(error){
                console.log(error);
                alert('shit happen');
            });
        }   

        $scope.removemwipTemp = function(id) {
            $http({
                method: 'DELETE',
                url : 'api/mwiptemp/' +id 
            }).then(function(response) {
                $scope.getData();
            });
        }
    }]);    
})();