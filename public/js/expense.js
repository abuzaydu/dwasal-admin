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
            method: 'GET',
            url: 'api/expenses'
        }).then(function (response) {
            $scope.items = response.data;
            console.log(response.data);
        }, function (error) {
            console.log(error);
            alert('This is embarassing. An error has occurred. Please check the log for details');
        });

        $scope.expensetemp = [ ];
        $scope.newexpensetemp = { };
        $http({
            method : 'GET',
            url: 'api/expensetemp'
        }).then(function (response){
            $scope.expensetemp = response.data;
        });

        $scope.getData = function(){
            $http({
                method: 'GET',
                url: 'api/expensetemp'
            }).then(function (response) {
                $scope.expensetemp = response.data;
            });
        };

        $scope.addExpenseTemp = function(item, newexpensetemp) {
            $http({
                method: 'POST',
                url : 'api/expensetemp',
                data: {expense_item_id: item.id}
            }).then(function (response){
               $scope.getData(); 
            });
        }

        $scope.updateExpenseTemp = function(newexpensetemp) {
            $http({
                method: 'PUT',
                url : 'api/expensetemp/' + newexpensetemp.id,
                data : {qty: newexpensetemp.qty, unit_cost: newexpensetemp.unit_cost, amount: newexpensetemp.amount, description: newexpensetemp.description, has_vat: newexpensetemp.has_vat, wht_rate: newexpensetemp.wht_rate}
            }).then(function(response) {
                $scope.getData();
            }, function(error){
                console.log(error);
                alert('shit happen');
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