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
        $scope.labourcosttemps = [ ];
        $scope.getItems = function() {
            $http({
                method: 'GET',
                url: 'api/prod-labourcosttemp'
            }).then(function(response){
                $scope.stages = response.data.stages;
                $scope.labourcosttemps = response.data.temps;
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
                url : 'api/prod-labourcosttemp',
                data: {production_stage_id: $scope.production_stage_id }
            }).then(function(response){
                $scope.mro_id = '';
                $scope.getData();
            });
        }

        $scope.updateItemTemp = function(newlabourcosttemp) {
            $http({
                method : 'PUT',
                url : 'api/prod-labourcosttemp/' + newlabourcosttemp.id,
                data : { quantity: newlabourcosttemp.quantity, unit_cost: newlabourcosttemp.unit_cost, total: newlabourcosttemp.total} 
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
                url: 'api/prod-labourcosttemp/' + id
            }).then(function(response){
                $scope.getData();
            });
        }

         $scope.sumItems = function(list) {
            var total=0;
            angular.forEach(list , function(labourcosttemps){
                total+= parseFloat(labourcosttemps.total);
            });
            return total;
        }
    }]);
})();