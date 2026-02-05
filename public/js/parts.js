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

    app.controller("SearchItemCtrl", [ '$scope', '$http', '$filter', '$timeout', function($scope, $http, $filter, $timeout) {
        $scope.purchaseTempId = function(part_purchase_temp_id) {
            $scope.tempid = part_purchase_temp_id;

            $scope.purchasetemp = { };
            $scope.vendors = [ ];
            $scope.currencies = [ ];
            $scope.parttempitems = [ ];
            $scope.newparttemp = { };
            $scope.total_amount = 0;

            $scope.getData = function() {
                $http({
                    method: 'GET',
                    url: 'api/parttemp/'+$scope.tempid
                }).then(function (response) {
                    $scope.purchasetemp = response.data.purchasetemp;
                    $scope.parttempitems = response.data.items
                    $scope.currencies = response.data.currencies;
                    $scope.vendors = response.data.vendors;      
                });
            };

            $scope.getData();

            $scope.addPartTemp = function(item) {
                $http({
                    method : 'POST',
                    url : 'api/parttemp',
                    data : { part_purchase_temp_id: $scope.tempid, part_id: item.id, pp_qty: 0 } 
                }).then(function(response){
                     if(response.data.status == 'DUPL') {
                        Swal.fire({
                            type: 'warning',
                            title: 'DULICATION',
                            text: response.data.msg
                        });
                     }else {
                        $scope.getData();
                    }
                }, function(error){
                    console.log(error);
                });
            }

            $scope.updatePartTemp = function(newparttemp) {
                    
                $http({ 
                    method : 'PUT',
                    url : 'api/parttemp/'+ newparttemp.id ,
                    data: {pp_qty: newparttemp.pp_qty, unit_price: newparttemp.unit_price, total_price: newparttemp.total_price} 
                }).then(function (response){
                    if(response.data.status == 'WRONG'){
                        Swal.fire({
                            type: 'info',
                            title: 'WRONG QTY...',
                            text: response.data.msg
                        });
                    }else if(response.data.status == 'FAIL'){
                        Swal.fire({
                            type: 'info',
                            title: 'INVALID EXPIRE DATE.',
                            text: 'You have entered invalid expire date. Please enter a valid expire date for ' +newparttemp.part.name+ "'s part entry using the format in the text field."
                        });
                    }

                    $scope.getData();
                });
            }   

            $scope.removePartTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url: 'api/parttemp/' + id ,
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.sum = function(list) {
                var total=0;
                angular.forEach(list , function(newparttemp){
                    total+= parseFloat(newparttemp.total_price);
                });
                $scope.total_amount = total;
                return total;
            }

            $scope.selectedItems = function(list) {
                var items=0;
                angular.forEach(list , function(newparttemp){
                    items++;
                });
                return items;
            }

            $scope.sumQty = function(list) {
                var total_qty=0;
                angular.forEach(list , function(newparttemp){
                    total_qty += parseFloat(newparttemp.pp_qty);
                });
                return total_qty;
            }

            $scope.updatePurchaseTempInfo = function(purchasetemp) {
                $http({
                    method: 'POST',
                    url: 'api/update-purchase-temp',
                    data: { 
                        id: purchasetemp.id,
                        vendor_id: purchasetemp.vendor_id,
                        date_set: purchasetemp.date_set,
                        purchase_date: purchasetemp.purchase_date,
                        purchase_type: purchasetemp.purchase_type,
                        pay_type: purchasetemp.pay_type,
                        currency: purchasetemp.currency,
                        ex_rate_mode: purchasetemp.ex_rate_mode,
                        local_ex_rate: purchasetemp.local_ex_rate,
                        foreign_ex_rate: purchasetemp.foreign_ex_rate,
                        comments: purchasetemp.comments
                    }
                }).then(function (response) {
                    $scope.getData();
                });       
            }

        };
    }]);
    
})();

// Validates that the input string is a valid date formatted as "mm/dd/yyyy"
function isValidDate(dateString)
{
    // First check for the pattern
    if(!/^\d{4}\-\d{1,2}\-\d{1,2}$/.test(dateString))
        return false;

    // Parse the date parts to integers
    var parts = dateString.split("-");
    var day = parseInt(parts[2], 10);
    var month = parseInt(parts[1], 10);
    var year = parseInt(parts[0], 10);

    // Check the ranges of month and year
    if(year < 2020 || year > 3000 || month == 0 || month > 12)
        return false;

    var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

    // Adjust for leap years
    if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
        monthLength[1] = 29;

    // Check the range of the day
    return day > 0 && day <= monthLength[month - 1];
};