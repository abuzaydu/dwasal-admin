(function(){
    var app = angular.module('smartpos', [ ]);

    app.controller("SearchItemCtrl", [ '$scope', '$http', '$filter', function($scope, $http, $filter) {
        $scope.items = [ ];
        $http({
            method: 'GET',
            url: 'api/rmitem'
        }).then(function(response){
            $scope.items = response.data;
        });

        $scope.rmusedtemp = [ ];
        $scope.newrmusedtemp = { };
        $scope.cost_per_unit = 0;
        $http({
            method: 'GET',
            url : 'api/rmusedtemp'
        }).then(function(response){
            $scope.rmusedtemp = response.data;
        });

        function getData(){
            $http({
                method: 'GET',
                url : 'api/rmusedtemp'
            }).then(function(response){
                $scope.rmusedtemp = response.data;
            });
        }

        $scope.updateProdCost = function(qty , rmusedtemp){
            $scope.prod_qty = qty;
            var total = $scope.sum(rmusedtemp);
            
            $scope.cost_per_unit = total/$scope.prod_qty;

        }

        $scope.addStockTemp = function(item, newrmusedtemp) {
            $http({
                method: 'POST',
                url : 'api/rmusedtemp',
                data: { raw_material_id: item.id, quantity: 0, unit_cost: item.unit_cost }
            }).then(function(response){
                getData();
            });
        }

        $scope.updateStockTemp = function(newrmusedtemp) {
            $http({
                method: 'PUT',
                url : 'api/rmusedtemp/' + newrmusedtemp.id,
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

                getData();
            });
        }   

        $scope.removeStockTemp = function(id) {
            $http({
                method: 'DELETE',
                url : 'api/rmusedtemp/' + id
            }).then(function(response){
                getData();
            });
        }


        $scope.sum = function(list) {
            var total=0;
            angular.forEach(list , function(newrmusedtemp){
                total+= parseFloat(newrmusedtemp.total);
            });
            return total;
        }

    }]);
    
})();