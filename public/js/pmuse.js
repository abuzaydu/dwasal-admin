(function(){
    var app = angular.module('smartpos', [ ]);

    app.controller("SearchItemCtrl", [ '$scope', '$http', '$filter', function($scope, $http, $filter) {
        $scope.items = [ ];
        $scope.prod_list =[ ];

        $http({
            method: 'GET',
            url : 'api/pmitem'
        }).then(function(response){
            $scope.items = response.data.packing_materials; 
            $scope.prod_list = response.data.products; 
        });

        $scope.pmusedtemp = [ ];
        $scope.newpmusedtemp = { };
        $http({
            method: 'GET',
            url : 'api/pmusedtemp'
        }).then(function(response) {
            $scope.pmusedtemp = response.data;
        });

        function getData(){
            $http({
                method: 'GET',
                url : 'api/pmusedtemp'
            }).then(function (response) {
                $scope.pmusedtemp = response.data;
            });
        }

        $scope.addStockTemp = function(item, newpmusedtemp) {
            $http({
                method: 'POST',
                url : 'api/pmusedtemp',
                data: {packing_material_id: item.id, quantity: 0, unit_cost: item.unit_cost }
            }).then(function(response){
                getData();
            });
        }

        $scope.updateStockTemp = function(newpmusedtemp) {
            $http({
                method: 'PUT',
                url: 'api/pmusedtemp/'+ newpmusedtemp.id,
                data: {quantity: newpmusedtemp.quantity, unit_packed: newpmusedtemp.unit_packed, product_packed: newpmusedtemp.product_packed, unit_cost: newpmusedtemp.unit_cost, total: newpmusedtemp.total}
            }).then(function(response){
                if (response.data.status == 'LOW') {
                    Swal.fire({
                        type: 'info',
                        title: 'LOW STOCK...',
                        text: data.msg
                    });
                }else {
                     if(response.data.status == 'WRONG'){
                        Swal.fire({
                            type: 'info',
                            title: 'WRONG QTY...',
                            text: data.msg
                        });
                     }
                }

                getData();
            });
        }   

        $scope.removeStockTemp = function(id) {
            $http({
                method: 'DELETE',
                url : 'api/pmusedtemp/' + id
            }).then(function(response) {
                getData();
            });
        }


        $scope.sum = function(list) {
            var total=0;
            angular.forEach(list , function(newpmusedtemp){
                total+= parseFloat(newpmusedtemp.total);
            });
            return total;
        }

    }]);

    
})();