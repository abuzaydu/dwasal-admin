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
        
        $scope.invoiceTempId = function(invoice_id){
            $scope.tempid = invoice_id;
            $scope.settings = { };
            $scope.currencies = [ ];
            $scope.banks = [ ];
            $scope.invoice = { };
            $scope.clients = [ ];
            $scope.quotations = [ ];
            $scope.projects = [ ];
            $scope.invoicetemp = [ ];
            $scope.newinvoicetemp = { };

            $scope.getData = function(){
                $http({
                    method: 'GET',
                    url: 'api/invoicetemp/'+$scope.tempid
                }).then(function (response) {
                    console.log(response);
                    $scope.settings = response.data.settings;
                    $scope.currencies = response.data.currencies;
                    $scope.banks = response.data.banks;
                    $scope.invoice = response.data.invoice;
                    $scope.clients = response.data.clients;
                    $scope.quotations = response.data.quotations;
                    $scope.projects = response.data.projects;
                    $scope.invoicetemp = response.data.temps;
                });
            };

            $scope.getData();

            $scope.addInvoiceTemp = function(invoice) {
                $http({
                    method: 'POST',
                    url : 'api/invoicetemp',
                    data: {invoice_id: invoice.id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateInvoiceTemp = function(newinvoicetemp) {
                $http({
                    method: 'PUT',
                    url : 'api/invoicetemp/' + newinvoicetemp.id,
                    data : {name: newinvoicetemp.name}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeInvoiceTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/invoicetemp/' +id 
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
                    url : 'api/invsubitemtemp/' + newsubtemp.id,
                    data : {invoice_id: $scope.invoice.id, item_type: newsubtemp.item_type, description: newsubtemp.description, price: newsubtemp.price, qty: newsubtemp.qty, total: newsubtemp.total}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeSubItemTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/invsubitemtemp/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            $scope.sum = function(list) {
                var total=0;
                angular.forEach(list, function(newinvoicetemp){
                    angular.forEach(newinvoicetemp.subitems, function(newsubtemp){
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

            $scope.updateInvoiceTempInfo = function(invoice){
                $http({
                    method: 'POST',
                    url: 'api/update-invoice',
                    data: { 
                        invoice_id: invoice.id,
                        client_id: invoice.client_id,
                        project_id: invoice.project_id,
                        po_number: invoice.po_number,
                        due_date: invoice.due_date,
                        currency: invoice.currency,
                        ex_rate_mode: invoice.ex_rate_mode,
                        local_ex_rate: invoice.local_ex_rate,
                        foreign_ex_rate: invoice.foreign_ex_rate,
                        bank_id: invoice.bank_id,
                        terms: invoice.terms,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }

            $scope.updateTerms = function(terms){
                $http({
                    method: 'POST',
                    url: 'api/update-invoice',
                    data: { 
                        invoice_id: $scope.invoice.id,
                        client_id: $scope.invoice.client_id,
                        project_id: $scope.invoice.project_id,
                        po_number: $scope.invoice.po_number,
                        due_date: $scope.invoice.due_date,
                        currency: $scope.invoice.currency,
                        ex_rate_mode: $scope.invoice.ex_rate_mode,
                        local_ex_rate: $scope.invoice.local_ex_rate,
                        foreign_ex_rate: $scope.invoice.foreign_ex_rate,
                        bank_id: $scope.invoice.bank_id,
                        terms: terms,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }

            $scope.updateDueDate = function(duedate){
                $http({
                    method: 'POST',
                    url: 'api/update-invoice',
                    data: { 
                        invoice_id: $scope.invoice.id,
                        client_id: $scope.invoice.client_id,
                        project_id: $scope.invoice.project_id,
                        po_number: $scope.invoice.po_number,
                        due_date: duedate,
                        currency: $scope.invoice.currency,
                        ex_rate_mode: $scope.invoice.ex_rate_mode,
                        local_ex_rate: $scope.invoice.local_ex_rate,
                        foreign_ex_rate: $scope.invoice.foreign_ex_rate,
                        bank_id: $scope.invoice.bank_id,
                        terms: $scope.invoice.terms,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }

            $scope.setQuoteInfo = function(invoice){
                $http({
                    method: 'POST',
                    url: 'api/set-quote-info',
                    data: { 
                        invoice_id: invoice.id,
                        quotation_id: invoice.quotation_id
                    }
                }).then(function (response) {
                    $scope.getData();
                });
            }
        };
    }]);
})();