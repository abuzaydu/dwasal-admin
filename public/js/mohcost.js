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
        $scope.mros = [ ];
        $scope.falsetrue = false;
        $scope.mohcosttemps = [ ];
        $scope.getItems = function() {
            $http({
                method: 'GET',
                url: 'api/moh-costtemp'
            }).then(function(response){
                $scope.mros = response.data.mros;
                $scope.mohcosttemps = response.data.temps;
            });
        }
        $scope.getData = function(){
            $scope.getItems();
        }

        $scope.getData();

//***************** MROS **********************//
        $scope.addItem = function(){
          $http({
                method: 'POST',
                url : 'api/moh-costtemp',
                data: {mro_id: $scope.mro_id }
            }).then(function(response){
                $scope.mro_id = '';
                $scope.getData();
            });
        }

        $scope.updateItemTemp = function(newmohcosttemp) {
            $http({
                method : 'PUT',
                url : 'api/moh-costtemp/' + newmohcosttemp.id,
                data : { quantity: newmohcosttemp.quantity, unit_cost: newmohcosttemp.unit_cost, total: newmohcosttemp.total} 
            }).then(function(response){
                if(response.data.status == 'WRONG'){
                    Swal.fire({
                        type: 'info',
                        title: 'WRONG QTY...',
                        text: response.data.msg
                    });
                }
                $scope.getData();
            });
        }   

        $scope.removeItemTemp = function(id) {
            $http({
                method: 'DELETE',
                url: 'api/moh-costtemp/' + id
            }).then(function(response){
                $scope.getData();
            });
        }

         $scope.sumItems = function(list) {
            var total=0;
            angular.forEach(list , function(mohcosttemps){
                total+= parseFloat(mohcosttemps.total);
            });
            return total;
        }
    }]);
})();