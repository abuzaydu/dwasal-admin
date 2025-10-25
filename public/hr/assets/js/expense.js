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
        
        $scope.suppliers = [ ];
        $scope.categories = [ ];
        $scope.expensetemp = [ ];
        $scope.newexpensetemp = { };

        $scope.getData = function(){
            $http({
                method: 'GET',
                url: 'api/expensetemp'
            }).then(function (response) {
                console.log(response);
                $scope.suppliers = response.data.suppliers;
                $scope.categories = response.data.categories;
                $scope.expensetemp = response.data.temps;
            });
        };

        $scope.getData();
        $scope.addExpenseTemp = function() {
            $http({
                method: 'POST',
                url : 'api/expensetemp',
                data: {}
            }).then(function (response){
               $scope.getData(); 
            });
        }

        $scope.updateExpenseTemp = function(newexpensetemp) {
            $http({
                method: 'PUT',
                url : 'api/expensetemp/' + newexpensetemp.id,
                data : {supplier_id: newexpensetemp.supplier_id, expense_category_id: newexpensetemp.expense_category_id, exp_type: newexpensetemp.exp_type, pay_mode: newexpensetemp.pay_mode, item: newexpensetemp.item, description: newexpensetemp.description, qty: newexpensetemp.qty, amount: newexpensetemp.amount}
            }).then(function(response) {
                console.log(response);
                $scope.getData();
            }, function(error){
                console.log(error);
                alert('Something went wrong.');
            });
        }   

        $scope.removeExpenseTemp = function(id) {
            $http({
                method: 'DELETE',
                url : 'api/expensetemp/' +id 
            }).then(function(response) {
                $scope.getData();
            });
        }

        $scope.sum = function(list) {
            var total=0;
            angular.forEach(list , function(newexpensetemp){
                total+= parseFloat(newexpensetemp.amount);
            });
            return total;
        }
    }]);
})();