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
        
        $scope.aRequestTempId = function(arequest_id){
            $scope.tempid = arequest_id;
            $scope.arequest = {};
            $scope.arequesttemp = [ ];
            $scope.newarequesttemp = { };

            $scope.getData = function(){
                $http({
                    method: 'GET',
                    url: 'api/arequesttemp/'+$scope.tempid
                }).then(function (response) {
                    console.log(response);
                    $scope.arequest = response.data.arequest;
                    $scope.arequesttemp = response.data.temps;
                });
            };

            $scope.getData();

            $scope.addRequestTemp = function(arequest) {
                $http({
                    method: 'POST',
                    url : 'api/arequesttemp',
                    data: {request_id: arequest.id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateRequestTemp = function(newarequesttemp) {
                $http({
                    method: 'PUT',
                    url : 'api/arequesttemp/' + newarequesttemp.id,
                    data : {item_description: newarequesttemp.item_description, item_category: newarequesttemp.item_category, no_of_days: newarequesttemp.no_of_days, quantity: newarequesttemp.quantity, price: newarequesttemp.price, is_passed: newarequesttemp.is_passed}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeRequestTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/arequesttemp/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            
            $scope.sum = function(list) {
                var total=0;
                angular.forEach(list, function(newarequesttemp){
                    total+= parseFloat(newarequesttemp.total);
                });
                return total;
            }
        };
    }]);
})();