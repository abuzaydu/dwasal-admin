(function(){
    var app = angular.module('nstsoft', [ ]);

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
        
        $scope.orderTempId = function(order_id){
            $scope.tempid = order_id;
            $scope.settings = { };
            $scope.currencies = [ ];
            $scope.order = { };
            $scope.suppliers = [ ];
            $scope.projects = [ ];
            $scope.ordertemp = [ ];
            $scope.newordertemp = { };
            // $scope.items = [];

            // $http({
            //     method: 'GET',
            //     url: 'api/m-items'
            // }).then(function (response) {
            //     $scope.items = response.data;
            //     console.log(response);
            // }, function (error) {

            // });
          
            $scope.getData = function(){
                $http({
                    method: 'GET',
                    url: 'api/ordertemp/'+$scope.tempid
                }).then(function (response) {
                    console.log(response);
                    $scope.settings = response.data.settings;
                    $scope.currencies = response.data.currencies;
                    $scope.order = response.data.order;
                    $scope.suppliers = response.data.suppliers;
                    $scope.projects = response.data.projects;
                    $scope.ordertemp = response.data.temps;
                });
            };

            $scope.getData();
            $scope.addOrderTemp = function(id) {
                $http({
                    method: 'POST',
                    url : 'api/ordertemp',
                    data: {order_id: $scope.order.id, item_id: id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateOrderTemp = function(newordertemp) {
                $http({
                    method: 'PUT',
                    url : 'api/ordertemp/' + newordertemp.id,
                    data : {qty: newordertemp.qty, unit_cost: newordertemp.unit_cost}
                }).then(function(response) {
                    console.log(response);
                    // $scope.getData();
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeOrderTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/ordertemp/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }


            $scope.sum = function(list) {
                var total=0;
                angular.forEach(list , function(newordertemp){
                    total+= parseFloat(newordertemp.total);
                });
                return total;
            }

            
            $scope.vat = function(total){
                var vat_amount = total*($scope.settings.tax_rate/100);
                return vat_amount;
            }

            $scope.total = function(total){
                var vat_amount = total*($scope.settings.tax_rate/100);
                return total+vat_amount;
            }

            $scope.updateOrderTempInfo = function(order){
                $http({
                    method: 'POST',
                    url: 'api/update-order/',
                    data: { 
                        order_id: order.id,
                        supplier_id: order.supplier_id,
                        project_id: order.project_id,
                        currency: order.currency,
                        ex_rate_mode: order.ex_rate_mode,
                        local_ex_rate: order.local_ex_rate,
                        foreign_ex_rate: order.foreign_ex_rate,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }
        };
    }]);
})();