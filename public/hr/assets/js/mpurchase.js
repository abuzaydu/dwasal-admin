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
        
        $scope.purchaseTempId = function(purchase_id){
            $scope.tempid = purchase_id;
            $scope.settings = { };
            $scope.currencies = [ ];
            $scope.purchase = { };
            $scope.suppliers = [ ];
            $scope.orders = [ ];
            $scope.purchasetemp = [ ];
            $scope.newpurchasetemp = { };
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
                    url: 'api/purchasetemp/'+$scope.tempid
                }).then(function (response) {
                    console.log(response);
                    $scope.settings = response.data.settings;
                    $scope.currencies = response.data.currencies;
                    $scope.purchase = response.data.purchase;
                    $scope.suppliers = response.data.suppliers;
                    $scope.orders = response.data.orders;
                    $scope.purchasetemp = response.data.temps;
                });
            };

            $scope.getData();
            $scope.addPurchaseTemp = function(id) {
                $http({
                    method: 'POST',
                    url : 'api/purchasetemp',
                    data: {purchase_id: $scope.purchase.id, item_id: id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updatePurchaseTemp = function(newpurchasetemp) {
                $http({
                    method: 'PUT',
                    url : 'api/purchasetemp/' + newpurchasetemp.id,
                    data : {qty: newpurchasetemp.qty, unit_cost: newpurchasetemp.unit_cost}
                }).then(function(response) {
                    console.log(response);
                    // $scope.getData();
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removePurchaseTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/purchasetemp/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }


            $scope.sum = function(list) {
                var total=0;
                angular.forEach(list , function(newpurchasetemp){
                    total+= parseFloat(newpurchasetemp.total);
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

            $scope.updatePurchaseTempInfo = function(purchase){
                $http({
                    method: 'POST',
                    url: 'api/update-purchase/',
                    data: { 
                        purchase_id: purchase.id,
                        supplier_id: purchase.supplier_id,
                        invoice_no: purchase.invoice_no,
                        dn_no: purchase.dn_no,
                        currency: purchase.currency,
                        ex_rate_mode: purchase.ex_rate_mode,
                        local_ex_rate: purchase.local_ex_rate,
                        foreign_ex_rate: purchase.foreign_ex_rate,
                        warehouse: purchase.warehouse,
                        owner: purchase.owner,
                        remarks: purchase.remarks,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }

            $scope.setOrderInfo = function(purchase){
                $http({
                    method: 'POST',
                    url: 'api/set-order-info/',
                    data: { 
                        purchase_id: purchase.id,
                        order_id: purchase.m_p_order_id
                    }
                }).then(function (response) {
                    $scope.getData();
                });
            }
        };
    }]);
})();