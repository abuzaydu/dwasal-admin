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

	app.controller("SearchItemCtrl", [ '$scope', '$http', '$filter', function($scope, $http, $filter) {

        $scope.rms = [ ];
        $scope.rmusedtemps = [ ];
        $scope.getData = function() {
            $http({
            method: 'GET',
            url : 'api/fp-rmusetemp'
            }).then(function(response){
                $scope.rmusedtemps = response.data.temps;
                $scope.rms = response.data.rms;
            });
        }

        $scope.getData();
               
        $scope.addRM = function(){
            $http({
                method: 'POST',
                url : 'api/fp-rmusetemp',
                data: { rm_id: $scope.rm_id }
            }).then(function(response){
                $scope.rm_id = '';
                $scope.getData();
            });
        } 

        $scope.updateRMTemp = function(newrmusedtemp) {
            $http({
                method: 'PUT',
                url : 'api/fp-rmusetemp/' + newrmusedtemp.id,
                data: { quantity: newrmusedtemp.quantity, unit_cost: newrmusedtemp.unit_cost, total: newrmusedtemp.total}
            }).then(function(response){
                if (response.data.status == 'LOW') {
                    Swal.fire({
                        type: 'info',
                        title: 'LOW STOCK...',
                        text: response.data.msg
                    });
                }else {
                     if(response.data.status == 'WRONG'){
                        Swal.fire({
                            type: 'info',
                            title: 'WRONG QTY...',
                            text: response.data.msg
                        });
                     }
                }
                $scope.getData();
            });
        }   

        $scope.removeRMTemp = function(id) {
            $http({
                method: 'DELETE',
                url : 'api/fp-rmusetemp/' + id
            }).then(function(response){
                $scope.getData();
            });
        }

        $scope.sumRM = function(list) {
            var total=0;
            angular.forEach(list , function(rmusedtemps){
                total+= parseFloat(rmusedtemps.total);
            });
            return total;
        }

	}]);
})();