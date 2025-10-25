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
        
        $scope.quotationTempId = function(quotation_id){
            $scope.tempid = quotation_id;
            $scope.settings = { };
            $scope.currencies = [ ];
            $scope.quotation = { };
            $scope.clients = [ ];
            $scope.projects = [ ];
            $scope.quotationtemp = [ ];
            $scope.newquotationtemp = { };

            $scope.getData = function(){
                $http({
                    method: 'GET',
                    url: 'api/quotationtemp/'+$scope.tempid
                }).then(function (response) {
                    console.log(response);
                    $scope.settings = response.data.settings;
                    $scope.currencies = response.data.currencies;
                    $scope.quotation = response.data.quotation;
                    $scope.clients = response.data.clients;
                    $scope.projects = response.data.projects;
                    $scope.quotationtemp = response.data.temps;
                });
            };

            $scope.getData();

            $scope.addQuotationTemp = function(quotation) {
                $http({
                    method: 'POST',
                    url : 'api/quotationtemp',
                    data: {quotation_id: quotation.id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateQuotationTemp = function(newquotationtemp) {
                $http({
                    method: 'PUT',
                    url : 'api/quotationtemp/' + newquotationtemp.id,
                    data : {name: newquotationtemp.name}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeQuotationTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/quotationtemp/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }


            $scope.addSubItemTemp = function(item) {
                $http({
                    method: 'POST',
                    url : 'api/subitemtemp',
                    data: {quotation_item_id: item.id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateSubItemTemp = function(newsubtemp) {
                $http({
                    method: 'PUT',
                    url : 'api/subitemtemp/' + newsubtemp.id,
                    data : {quotation_id: $scope.quotation.id, item_type: newsubtemp.item_type, description: newsubtemp.description, price: newsubtemp.price, qty: newsubtemp.qty, total: newsubtemp.total}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeSubItemTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/subitemtemp/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            $scope.sum = function(list) {
                var total=0;
                angular.forEach(list , function(newquotationtemp){
                    angular.forEach(newquotationtemp.subitems, function(newsubtemp){
                        total+= parseFloat(newsubtemp.total);
                    });
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

            $scope.updateQuotationTempInfo = function(quotation){
                $http({
                    method: 'POST',
                    url: 'api/update-quotation',
                    data: { 
                        quotation_id: quotation.id,
                        client_id: quotation.client_id,
                        project_id: quotation.project_id,
                        project_name: quotation.project_name,
                        due_date: quotation.due_date,
                        currency: quotation.currency,
                        ex_rate_mode: quotation.ex_rate_mode,
                        local_ex_rate: quotation.local_ex_rate,
                        foreign_ex_rate: quotation.foreign_ex_rate,
                        terms: quotation.terms,
                        att_to_name: quotation.att_to_name,
                        att_to_email: quotation.att_to_email,
                        att_to_mobile: quotation.att_to_mobile,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }

            $scope.updateTerms = function(terms){
                $http({
                    method: 'POST',
                    url: 'api/update-quotation',
                    data: { 
                        quotation_id: $scope.quotation.id,
                        client_id: $scope.quotation.client_id,
                        project_id: $scope.quotation.project_id,
                        project_name: $scope.quotation.project_name,
                        due_date: $scope.quotation.due_date,
                        currency: $scope.quotation.currency,
                        ex_rate_mode: $scope.quotation.ex_rate_mode,
                        local_ex_rate: $scope.quotation.local_ex_rate,
                        foreign_ex_rate: $scope.quotation.foreign_ex_rate,
                        terms: terms,
                        att_to_name: $scope.quotation.att_to_name,
                        att_to_email: $scope.quotation.att_to_email,
                        att_to_mobile: $scope.quotation.att_to_mobile,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }

            $scope.updateDueDate = function(duedate){
                $http({
                    method: 'POST',
                    url: 'api/update-quotation',
                    data: { 
                        quotation_id: $scope.quotation.id,
                        client_id: $scope.quotation.client_id,
                        project_id: $scope.quotation.project_id,
                        project_name: $scope.quotation.project_name,
                        due_date: duedate,
                        currency: $scope.quotation.currency,
                        ex_rate_mode: $scope.quotation.ex_rate_mode,
                        local_ex_rate: $scope.quotation.local_ex_rate,
                        foreign_ex_rate: $scope.quotation.foreign_ex_rate,
                        terms: $scope.quotation.terms,
                        att_to_name: $scope.quotation.att_to_name,
                        att_to_email: $scope.quotation.att_to_email,
                        att_to_mobile: $scope.quotation.att_to_mobile,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }
        };
    }]);
})();