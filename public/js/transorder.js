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

        $scope.orderTempId = function(order_temp_id) {
            $scope.tempid = order_temp_id;
            $scope.ordertemp = { };
            $scope.orderitemtemp = [ ];
            $scope.neworderitemtemp = { };
            $scope.types = [];
            $scope.destinations = [];

            $scope.getData = function(){
                $http({
                    method: 'GET',
                    url: '../api/orderitemtemp/'+$scope.tempid
                }).then(function (response) {
                    console.log(response.data);
                    $scope.ordertemp = response.data.ordertemp;
                    $scope.orderitemtemp = response.data.temps;
                    $scope.destinations = response.data.destinations;
                    $scope.types = response.data.types;
                });
            };

            $scope.getData();

            $scope.updateTransferOrderTemp = function(ordertemp) {
                $http({
                    method: 'POST',
                    url: '../api/update-order-temp',
                    data: {temp_id: $scope.tempid, destin_id: ordertemp.destination_id, transfer_type: ordertemp.transfer_type, order_date: ordertemp.order_date, reason: ordertemp.reason}
                }).then(function(response){
                    $scope.getData();
                });
            }
            $scope.addOrderItemTemp = function(item) {
                if ($scope.ordertemp.destination_id == null) {
                    Swal.fire({
                        type: 'warning',
                        title: 'No destination...',
                        text: 'Please select a destination Shop OR Store to Add items.'
                    });
                    $scope.getData();
                }else{
                    if (item.in_stock == 0.00) {
                        Swal.fire({
                            type: 'warning',
                            title: 'ZERO Stock...',
                            text: 'The stock of '+item.name+' is currently ZERO. Please Purchase new Stock.'
                        });
                    }else{
                        $http({
                            method: 'POST',
                            url: '../api/orderitemtemp',
                            data: {temp_id: $scope.tempid, product_id: item.id, destin_id: $scope.ordertemp.destination_id}
                        }).then(function(response){
                            if(response.data.status == 'NOT') {
                                Swal.fire({
                                    type: 'info',
                                    title: 'Not Exists...',
                                    text: response.data.msg
                                });
                            }else if(response.data.status == 'DUPL') {
                                Swal.fire({
                                    type: 'info',
                                    title: 'DUPLICATES...',
                                    text: response.data.msg
                                });
                            }
                           $scope.getData();
                        }, function(error){

                        });
                    }
                }
            }

            $scope.addSTOItemTemp = function(item) {
                if ($scope.ordertemp.transfer_type == 0) {
                    Swal.fire({
                        type: 'warning',
                        title: 'No Transfer type Selected...',
                        text: 'Please select Transfer type to Add items.'
                    });
                }else if ($scope.ordertemp.destination_id == null) {
                    Swal.fire({
                        type: 'warning',
                        title: 'No Source Selected...',
                        text: 'Please select Source Shop OR Store to Add items.'
                    });
                }else{
                    $http({
                        method: 'POST',
                        url: '../api/orderitemtemp',
                        data: {temp_id: $scope.tempid, product_id: item.id, destin_id: $scope.ordertemp.destination_id, transfer_type: $scope.ordertemp.transfer_type}
                    }).then(function(response){
                        if(response.data.status == 'NOT') {
                            Swal.fire({
                                type: 'info',
                                title: 'Not Exists...',
                                text: response.data.msg
                            });
                        }else if(response.data.status == 'DUPL') {
                            Swal.fire({
                                type: 'info',
                                title: 'DUPLICATES...',
                                text: response.data.msg
                            });
                        }
                       $scope.getData();
                    }, function(error){

                    });
                }
            }


            $scope.updateOrderItemTemp = function(neworderitemtemp) {
                $http({
                    method: 'PUT',
                    url: '../api/orderitemtemp/' + neworderitemtemp.id ,
                    data : { quantity: neworderitemtemp.quantity, destin_unit_cost: neworderitemtemp.destin_unit_cost}
                }).then(function(response){
                    if (response.data.status == 'LOW') {
                        Swal.fire({
                            type: 'info',
                            title: 'Low Stock Level...',
                            text: 'Stock of Your Product in your Source Shop/Store is currently less than.'+neworderitemtemp.quantity+'.'
                        });
                    } 
                    $scope.getData();   
                });
            }   


            $scope.removeOrderItemTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url: '../api/orderitemtemp/' + id
                }).then(function(response){
                    $scope.getData();
                });
            }
        };
    }]);
})();