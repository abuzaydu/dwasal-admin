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
        $scope.orderTempId = function(order_temp_id) {
            $scope.tempid = order_temp_id;

            $scope.pordertemp = { };
            $scope.orderitemtemp = [ ];
            $scope.neworderitemtemp = { };
            $scope.suppliers = [ ];

            $http({
                method : 'GET',
                url : '../api/usebarcode'
            }).then(function(response){
                $scope.usebarcode = response.data.usebarcode;
                if ($scope.usebarcode) {
                    // Kick off the interval
                    $scope.intervalFunction();
                }
            });

            $scope.getData = function(){
                $http({
                    method : 'GET',
                    url : '../api/pordertemp/'+$scope.tempid
                }).then(function(response){
                    console.log(response);
                    $scope.pordertemp = response.data.ordertemp;
                    $scope.orderitemtemp = response.data.temps;
                    $scope.suppliers = response.data.suppliers;  
                });
            };

            $scope.getData();
            // Function to replicate setInterval using $timeout service.
            $scope.intervalFunction = function(){
                $timeout(function() {
                  $scope.getData();
                  $scope.intervalFunction();
                }, 15000)
            };
            
            $scope.addOrderTemp = function(item, neworderitemtemp) {
                $http({
                    method : 'POST',
                    url : '../api/pordertemp',
                    data: {order_temp_id: $scope.tempid, product_id: item.id, quantity_in: 0 }
                }).then(function(response){
                    if(response.data.status == 'DUPL') {
                        Swal.fire({
                            type: 'warning',
                            title: 'DUPLICATES',
                            text: response.data.msg
                        });
                    }else {
                        $scope.getData();
                    }
                });
            }

            $scope.updateOrderTemp = function(neworderitemtemp) {
                $http({
                    method : 'PUT',
                    url : '../api/pordertemp/' + neworderitemtemp.id,
                    data: {qty: neworderitemtemp.qty, unit_cost: neworderitemtemp.unit_cost}
                }).then( function(response){
                    if(response.data.status == 'WRONG'){
                        Swal.fire({
                            type: 'info',
                            title: 'WRONG QTY...',
                            text: response.data.msg
                        });
                    }else if(response.data.status == 'FAIL'){
                        Swal.fire({
                            type: 'info',
                            title: 'INVALID EXPIRE DATE.',
                            text: 'You have entered invalid expire date. Please enter a valid expire date for ' +neworderitemtemp.product.name+ "'s porder entry using the format in the text field."
                        });
                    }

                    $scope.getData();
                });
            }   

            $scope.updateOrderTempInfo = function(pordertemp) {
                $http({
                    method: 'POST',
                    url: '../api/update-po-temp',
                    data: { 
                        id: pordertemp.id,
                        supplier_id: pordertemp.supplier_id,
                        pfi_no: pordertemp.pfi_no,
                        comments: pordertemp.comments
                    }
                }).then(function (response) {
                    $scope.getData();
                });
            }
            $scope.updateSaleSupplierID = function(supplier_id) {
                $http({
                    method: 'POST',
                    url: '../api/update-po-temp',
                    data: { 
                        id: $scope.pordertemp.id,
                        supplier_id: supplier_id,
                        pfi_no: $scope.pordertemp.pfi_no,
                        comments: $scope.pordertemp.comments
                    }
                }).then(function (response) {
                    $scope.getData();
                });
            }

            $scope.removeOrderTemp = function(id) {
                $http({
                    method : 'DELETE',
                    url : '../api/pordertemp/' + id
                }).then(function(response){
                    $scope.getData();
                });
            }


            $scope.sum = function(list) {
                var total=0;
                angular.forEach(list , function(neworderitemtemp){
                    total+= parseFloat(neworderitemtemp.qty*neworderitemtemp.unit_cost);
                });
                return total;
            }
        }
    }]);

})();