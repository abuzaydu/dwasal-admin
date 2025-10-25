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
 
        $scope.servinvoicetemp = [ ];
        $scope.newservinvoicetemp = { };
        $scope.dpercent = 0;
        
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
         
            $scope.dpercent = (sale_discount/total)*100;

            angular.forEach($scope.servinvoicetemp, function(newservinvoicetemp){
                $http({
                    method : 'PUT',
                    url : '../api/servinvoicetemp/' + newservinvoicetemp.id,
                    data:  { repeatition: newservinvoicetemp.repeatition, cost_per_unit: newservinvoicetemp.cost_per_unit, total_discount: newservinvoicetemp.cost_per_unit * newservinvoicetemp.repeatition*$scope.dpercent/100, with_vat: newservinvoicetemp.with_vat }
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

        $scope.sum = function(list) {
            var total=0;
            angular.forEach(list , function(newservinvoicetemp){
                total += parseFloat(newservinvoicetemp.cost_per_unit * newservinvoicetemp.repeatition);
            });
            return total;
        }

        $scope.sumDiscount = function(list){
            var t_discount=0;
            angular.forEach(list, function(newservinvoicetemp){
                t_discount += parseFloat(newservinvoicetemp.total_discount);
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
    }]);
})();