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
        $scope.items = [ ];

        $http({
            method : 'GET',
            url: '../api/item'
        }).then(function(response){
            $scope.items = response.data;
        });

        $scope.ordertemp = [ ];
        $scope.newordertemp = { };
        $http({
            method: 'GET',
            url: '../api/ordertemp'
        }).then(function(response){
            $scope.ordertemp = response.data;
        });
     
        var destin_products = [ ];
        var destin = '';

        $scope.getData = function(){
            $http({
                method: 'GET',
                url: '../api/ordertemp'
            }).then(function (response) {
                $scope.ordertemp = response.data;
            });
        };

        $scope.getDestProducts = function(destin_id){
                  $scope.destin_id = destin_id;
                $http({
                    method: 'POST',
                    url: '../api/destin_produts',
                    data : {id: destin_id}
                }).then(function(response){
                    $scope.items = response.data;
                    destin = destin_id;
                });
        }
        
        $scope.addOrderTemp = function(item, newordertemp) {
            if (destin === '') {
                Swal.fire({
                    type: 'warning',
                    title: 'No destination...',
                    text: 'Please select a destination Shop OR Store to Add items.'
                });

            }else{
                $http({
                        method: 'POST',
                        url: '../transformation-transfer-temp',
                        data: {product_id: item.id, destin_id: destin }
                    }).then(function(response){
                        $scope.getData();
                });     
            }
        }

        $scope.updateOrderTemp = function(newordertemp) {

            $http({
                    method: 'PUT',
                    url: '../transformation-transfer-temp/' + newordertemp.id,
                    data: { quantity: newordertemp.quantity, destin_unit_cost: newordertemp.destin_unit_cost}
                }).then(function(response){
                         if (data.status == 'LOW') {
                            Swal.fire({
                                type: 'info',
                                title: 'Low Stock Level...',
                                text: 'Stock of Your Product in your Source Shop/Store is currently less than.'+newordertemp.quantity+'.'
                            });
                        } 
                        $scope.getData();
            });
        }   

        $scope.removeOrderTemp = function(id) {
            $http({
                method: 'DELETE',
                url: '../transformation-transfer-temp/' + id
            }).then(function(response){
                $scope.getData();
            });
        }

    }]);
    
})();