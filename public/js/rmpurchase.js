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
        $scope.rmPurchaseId = function(rm_purchase_temp_id){
            
            $scope.items = [ ];
            $scope.tempid = rm_purchase_temp_id;

            $http({
               method: 'GET',
               url : 'api/rmitem' 
            }).then(function(response){
                 $scope.items = response.data;
            });
            const ukn = {id: 0 , name : 'Unknown'};
            $scope.rmtemp = { };
            $scope.suppliers = [ ];
            $scope.rmitemtemp = [ ];
            $scope.newrmitemtemp = { };
            $scope.currencies = [ ];
            $scope.defaultSupplier;

            $http({
                url: 'api/rmitemtemp',
                method: 'GET'
            }).then(function(response){

                $scope.rmitemtemp = response.data.itemtemps;
                $scope.rmtemp = response.data.rmtemp;
                $scope.suppliers = response.data.suppliers;
                $scope.currencies = response.data.currencies;
                console.log(response);
                
            }, function(error){
                alert('error')
                console.log(error);
            });

            function getData(){
                $http({
                url: 'api/rmitemtemp',
                method: 'GET'
            }).then(function(response){
                $scope.rmitemtemp = response.data.itemtemps;
                $scope.rmtemp = response.data.rmtemp;
                $scope.suppliers = response.data.suppliers;
                $scope.currencies = response.data.currencies;


            });
        }

            $scope.addStockTemp = function(item, newrmitemtemp , tempid) {
                $http({
                    method: 'POST',
                    url: 'api/rmitemtemp',
                    data:  { raw_material_id: item.id, qty: 0 , rm_purchase_temp_id: tempid }
                }).then(function(response) {
                    getData();
                }, function(error){
                    console.log(error)
                });
            } 
            
            $scope.updateStockTemp = function(newrmitemtemp) {

                $http({
                    method: 'PUT',
                    url: 'api/rmitemtemp/' + newrmitemtemp.id,
                    data: { qty: newrmitemtemp.qty, unit_cost: newrmitemtemp.unit_cost, total: newrmitemtemp.total }
                }).then(function(response){
                     if(response.data.status == 'WRONG'){
                        Swal.fire({
                            type: 'info',
                            title: 'WRONG QTY...',
                            text: response.data.msg
                        });
                    }

                    getData();
                });
            }   

            $scope.removeStockTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/rmitemtemp/' + id
                }).then(function(response){
                    getData();
                });
            }


            $scope.sum = function(list) {
                var total=0;
                angular.forEach(list , function(newrmitemtemp){
                    total += parseFloat(newrmitemtemp.total);
                });
                return total;
            }

            $scope.updateRmTempInfo = function(rmtemp) {

                $http({
                    method: 'PUT',
                    url: 'rm-temp/' + rmtemp.id,
                    data: { 
                        supplier_id: rmtemp.supplier_id,
                        date_set: rmtemp.date_set,
                        date: rmtemp.date,
                        purchase_type: rmtemp.purchase_type,
                        pay_type: rmtemp.pay_type,
                        currency: rmtemp.currency,
                        grn_no :rmtemp.grn_no,
                        order_no : rmtemp.order_no,
                        invoice_no : rmtemp.invoice_no, 
                        delivery_note_no : rmtemp.delivery_note_no,
                        ex_rate_mode: rmtemp.ex_rate_mode,
                        local_ex_rate: rmtemp.local_ex_rate,
                        foreign_ex_rate: rmtemp.foreign_ex_rate,
                        due_date: rmtemp.due_date,
                        comments: rmtemp.comments
                    }
                }).then(function (response) {
                    getData();
                });       
            }
        }

    }]);
    
})();