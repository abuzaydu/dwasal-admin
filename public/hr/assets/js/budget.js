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
        
        $scope.projectBudgetTempId = function(pbudget_id){
            $scope.tempid = pbudget_id;
            $scope.pbudget = {};
            $scope.allowancetemp = [ ];
            $scope.newallowancetemp = { };
            $scope.tooltemp = [ ];
            $scope.newtooltemp = { };
            $scope.transporttemp = [ ];
            $scope.newtransporttemp = { };
            $scope.risktemp = [ ];
            $scope.newrisktemp = { };
            $scope.commenttemp = [ ];
            $scope.newcommenttemp = { };

            $scope.getData = function(){
                $http({
                    method: 'GET',
                    url: 'api/allowancetemp/'+$scope.tempid
                }).then(function (response) {
                    console.log(response);
                    $scope.pbudget = response.data.pbudget;
                    $scope.allowancetemp = response.data.atemps;
                    $scope.tooltemp = response.data.ttemps;
                    $scope.transporttemp = response.data.tptemps;
                    $scope.risktemp = response.data.rtemps;
                    $scope.commenttemp = response.data.ctemps;
                });
            };

            $scope.getData();

            //Allowance start
            $scope.addAllowanceTemp = function(pbudget) {
                $http({
                    method: 'POST',
                    url : 'api/allowancetemp',
                    data: {budget_id: pbudget.id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateAllowanceTemp = function(newallowancetemp) {
                $http({
                    method: 'PUT',
                    url : 'api/allowancetemp/' + newallowancetemp.id,
                    data : {name: newallowancetemp.name, no_of_sites: newallowancetemp.no_of_sites, no_of_staffs: newallowancetemp.no_of_staffs, no_of_days: newallowancetemp.no_of_days, no_of_occasion: newallowancetemp.no_of_occasion, rate: newallowancetemp.rate}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeAllowanceTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/allowancetemp/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            
            $scope.sumAllowance = function(list) {
                var total=0;
                angular.forEach(list, function(newallowancetemp){
                    total+= parseFloat(newallowancetemp.sub_total);
                });
                return total;
            }
            //Allowance End

            //Tools start
            $scope.addToolTemp = function(pbudget) {
                $http({
                    method: 'POST',
                    url : 'api/tooltemp',
                    data: {budget_id: pbudget.id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateToolTemp = function(newtooltemp) {
                $http({
                    method: 'PUT',
                    url : 'api/tooltemp/' + newtooltemp.id,
                    data : {tool_type: newtooltemp.tool_type, quantity: newtooltemp.quantity, unit_cost: newtooltemp.unit_cost}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeToolTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/tooltemp/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            
            $scope.sumTool = function(list) {
                var total=0;
                angular.forEach(list, function(newtooltemp){
                    total+= parseFloat(newtooltemp.sub_total);
                });
                return total;
            }

            //Transport Start
            $scope.addTransportTemp = function(pbudget) {
                $http({
                    method: 'POST',
                    url : 'api/transporttemp',
                    data: {budget_id: pbudget.id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateTransportTemp = function(newtransporttemp) {
                $http({
                    method: 'PUT',
                    url : 'api/transporttemp/' + newtransporttemp.id,
                    data : {transport_type: newtransporttemp.transport_type, reg: newtransporttemp.reg, no_of_days: newtransporttemp.no_of_days, no_of_litres: newtransporttemp.no_of_litres, unit_cost: newtransporttemp.unit_cost}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeTransportTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/transporttemp/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            
            $scope.sumTransport = function(list) {
                var total=0;
                angular.forEach(list, function(newtransporttemp){
                    total+= parseFloat(newtransporttemp.sub_total);
                });
                return total;
            }

            //Risk Start
            $scope.addRiskTemp = function(pbudget) {
                $http({
                    method: 'POST',
                    url : 'api/risktemp',
                    data: {budget_id: pbudget.id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateRiskTemp = function(newrisktemp) {
                $http({
                    method: 'PUT',
                    url : 'api/risktemp/' + newrisktemp.id,
                    data : {risk_type: newrisktemp.risk_type, no_of_sites: newrisktemp.no_of_sites, no_of_staffs: newrisktemp.no_of_staffs, no_of_days: newrisktemp.no_of_days, no_of_occasion: newrisktemp.no_of_occasion, rate: newrisktemp.rate}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeRiskTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/risktemp/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            
            $scope.sumRisk = function(list) {
                var total=0;
                angular.forEach(list, function(newrisktemp){
                    total+= parseFloat(newrisktemp.sub_total);
                });
                return total;
            }

            //Comments Start
            $scope.addCommentTemp = function(pbudget) {
                $http({
                    method: 'POST',
                    url : 'api/commenttemp',
                    data: {budget_id: pbudget.id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateCommentTemp = function(newcommenttemp) {
                $http({
                    method: 'PUT',
                    url : 'api/commenttemp/' + newcommenttemp.id,
                    data : {comment: newcommenttemp.comment}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeCommentTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/commenttemp/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

        };
    }]);
})();