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
        $scope.payrolltemp = [ ];
        $scope.newpayrolltemp = { };
        $http({
            method : 'GET',
            url: 'api/payrolltemp'
        }).then(function (response){
            $scope.payrolltemp = response.data;
            console.log(response);
        });

        $scope.getData = function(){
            $http({
                method: 'GET',
                url: 'api/payrolltemp'
            }).then(function (response) {
                $scope.payrolltemp = response.data;
            });
        };

        $scope.addPayrollTemp = function(item, newpayrolltemp) {
            $http({
                method: 'POST',
                url : 'api/payrolltemp',
                data: {payroll_type: item.name}
            }).then(function (response){
               $scope.getData(); 
            });
        }

        $scope.updatePayrollTemp = function(newpayrolltemp) {
            $http({
                method: 'PUT',
                url : 'api/payrolltemp/' + newpayrolltemp.id,
                data : {days_work: newpayrolltemp.days_work, overtime_hrs: newpayrolltemp.overtime_hrs, bonuses: newpayrolltemp.bonuses, penalty: newpayrolltemp.penalty, absences: newpayrolltemp.absences, late: newpayrolltemp.late}
            }).then(function(response) {
                console.log(response);
                $scope.getData();
            }, function(error){
                console.log(error);
                alert('shit happen');
            });
        }   

        $scope.removePayrollTemp = function(id) {
            $http({
                method: 'DELETE',
                url : 'api/payrolltemp/' +id 
            }).then(function(response) {
                $scope.getData();
            });
        }


        $scope.sum = function(list) {
            var total=0;
            angular.forEach(list , function(newpayrolltemp){
                total+= parseFloat(newpayrolltemp.amount);
            });
            return total;
        }

    }]);

    
})();