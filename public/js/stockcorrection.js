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
        
            $scope.correctiontemp = [ ];
            $scope.newcorrectiontemp = { };

            $scope.getData = function() {
                $http({
                    method: 'GET',
                    url: 'api/correction-temp'
                }).then(function (response) {
                    $scope.correctiontemp = response.data;
                    console.log($scope.correctiontemp);  
                });
            };

            $scope.getData();
            $scope.addCorrectionTemp = function(item) {
                $http({
                    method : 'POST',
                    url : 'api/correction-temp',
                    data : { product_id: item.id } 
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

            $scope.updateCorrectionTemp = function(newcorrectiontemp) {
                $http({
                    method : 'PUT',
                    url : 'api/correction-temp/'+ newcorrectiontemp.id ,
                    data: {correction_qty: newcorrectiontemp.correction_qty} 
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
                            text: 'You have entered invalid expire date. Please enter a valid expire date for ' +newcorrectiontemp.product.name+ "'s Stock entry using the format in the text field."
                        });
                    }

                    $scope.getData();
                });
            }   

            $scope.removeCorrectionTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url: 'api/correction-temp/' + id ,
                }).then(function (response){
                   $scope.getData(); 
                });
            }
    }]);
    
})();