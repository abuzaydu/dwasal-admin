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
        
        $scope.invoicetemp = [ ];
        $scope.newinvoicetemp = { };
        $scope.dpercent = 0;
        $http({
            method : 'GET',
            url : '../api/invoicetemp'
        }).then(function(response){
             $scope.invoicetemp = response.data;
        });

        $scope.getData = function() {
            $http({
                method : 'GET',
                url : '../api/invoicetemp'
            }).then(function(response){
                $scope.invoicetemp = response.data.temps;
                console.log(response);
            });
        }

        $scope.getData();
        //Add Sale temp using Barcode Scanner

        $scope.addSaleTemp = function(item, newinvoicetemp) {
            $http({
                method : 'POST',
                url : '../api/invoicetemp',
                data : { product_id: item.id, cost_per_unit: item.retail_price }
            }).then(function(response){
                $scope.getData();
            }, function(error){

                console.log(error);
            });    
        }

        $scope.updateSaleTemp = function(newinvoicetemp) {
            $http({
                method : 'PUT',
                url : '../api/invoicetemp/' + newinvoicetemp.id,
                data : { quantity: newinvoicetemp.quantity, product_unit_id: newinvoicetemp.product_unit_id, price: newinvoicetemp.cost_per_unit * newinvoicetemp.quantity , cost_per_unit : newinvoicetemp.cost_per_unit, with_vat: newinvoicetemp.with_vat, disc_percent: newinvoicetemp.disc_percent, discount: newinvoicetemp.discount, total_discount: newinvoicetemp.total_discount, sold_in: newinvoicetemp.sold_in }
            }).then(function(response){
                $scope.getData();
                if (response.data.status == 'Fail') {
                    alert('Stock of this product is currently low');
                }
            });
        }   

        $scope.updateSaleTempDiscount = function(sale_discount) {
            var total=0;
            angular.forEach($scope.invoicetemp , function(newinvoicetemp){
                total+= parseFloat(newinvoicetemp.cost_per_unit * newinvoicetemp.quantity);
            });
            
            $scope.dpercent = (sale_discount/total)*100;

            // alert('You have added a Discount of '+ Math.round($scope.dpercent)+ '% to each item sold.');
            angular.forEach($scope.invoicetemp, function(newinvoicetemp){
                $http({
                    method : 'PUT',
                    url : '../api/invoicetemp/' + newinvoicetemp.id, 
                  data : { quantity: newinvoicetemp.quantity, product_unit_id: newinvoicetemp.product_unit_id, price: newinvoicetemp.cost_per_unit * newinvoicetemp.quantity , cost_per_unit : newinvoicetemp.cost_per_unit, with_vat: newinvoicetemp.with_vat, disc_percent: $scope.dpercent, discount: newinvoicetemp.discount, total_discount: newinvoicetemp.total_discount, sold_in: newinvoicetemp.sold_in }
                }).then(function(response){
                    $scope.getData();
                });
            });
        }     

        $scope.removeSaleTemp = function(id) {
            $http({
                method : 'DELETE',
                url : '../api/invoicetemp/' + id
            }).then(function(response){
                $scope.getData();
            }, function(error){

                alert('error');
            });
        }


        $scope.sum = function(list) {
            var total=0;
            angular.forEach(list , function(newinvoicetemp){
                total+= parseFloat(newinvoicetemp.cost_per_unit * newinvoicetemp.quantity);
            });
            return total;
        }
        
        $scope.sumDiscount = function(list){
            var t_discount=0;
            angular.forEach(list, function(newinvoicetemp) {
                t_discount += parseFloat(newinvoicetemp.total_discount);
            });
            return t_discount;
        }
        
        $scope.sumTax = function(list) {
            var total=0;
            angular.forEach(list , function(newinvoicetemp){
                total+= parseFloat(newinvoicetemp.vat_amount);
            });
            return total;
        }

        //Services Functions
        $scope.servinvoicetemp = [ ];
        $scope.newservinvoicetemp = { };
        $scope.servdpercent = 0;
        
        $scope.getSevData = function() {
            $http({
                method : 'GET',
                url : '../api/servinvoicetemp'
            }).then(function (response){
                $scope.servinvoicetemp = response.data.temps;
            });
        }

        $scope.getSevData();

        $scope.addServiceSaleTemp = function(id) {
            $http({
                method :'POST',
                url : '../api/servinvoicetemp',
                data : { service_id: id }
            }).then(function(response){
                $scope.getSevData();
            });   
        }

        $scope.updateServiceSaleTemp = function(newservinvoicetemp) {
            $http({
                method : 'PUT',
                url : '../api/servinvoicetemp/' + newservinvoicetemp.id,
                data : { repeatition: newservinvoicetemp.repeatition, cost_per_unit : newservinvoicetemp.cost_per_unit, total_discount: newservinvoicetemp.total_discount, with_vat: newservinvoicetemp.with_vat }
            }).then(function(response){
                $scope.getSevData();
            });
        }    

        $scope.updateSaleTempServiceDiscount = function(sale_discount) {
            var total=0;
            angular.forEach($scope.servinvoicetemp , function(newservinvoicetemp){
                total+= parseFloat(newservinvoicetemp.cost_per_unit * newservinvoicetemp.repeatition);
            });
         
            $scope.servdpercent = (sale_discount/total)*100;

            angular.forEach($scope.servinvoicetemp, function(newservinvoicetemp){
                $http({
                    method : 'PUT',
                    url : '../api/servinvoicetemp/' + newservinvoicetemp.id,
                    data:  { repeatition: newservinvoicetemp.repeatition, cost_per_unit: newservinvoicetemp.cost_per_unit, total_discount: newservinvoicetemp.cost_per_unit * newservinvoicetemp.repeatition*$scope.servdpercent/100, with_vat: newservinvoicetemp.with_vat }
                }).then(function(response){
                    $scope.getSevData();
                });

            });
        }

        $scope.removeServiceSaleTemp = function(id) {
            $http({
                method : 'DELETE',
                url :'../api/servinvoicetemp/' + id
            }).then(function(response){
                $scope.getSevData();
            });
        }

        $scope.sumService = function(list) {
            var total=0;
            angular.forEach(list , function(newservinvoicetemp){
                total += parseFloat(newservinvoicetemp.cost_per_unit * newservinvoicetemp.repeatition);
            });
            return total;
        }

        $scope.sumServiceDiscount = function(list){
            var t_discount=0;
            angular.forEach(list, function(newservinvoicetemp){
                t_discount += parseFloat(newservinvoicetemp.total_discount);
            });
            return t_discount;
        }


        $scope.sumServiceTax = function(list) {
            var total=0;
            angular.forEach(list , function(newservinvoicetemp){
                total+= parseFloat(newservinvoicetemp.vat_amount);
            });
            return total;
        }
    }]);
})();