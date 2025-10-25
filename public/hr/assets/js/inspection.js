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
        
        $scope.inspectionTempId = function(inspection_id){
            $scope.tempid = inspection_id;
            $scope.inspection = { };
            $scope.sites = [ ];
            $scope.inspectiontemp = [ ];
            $scope.newinspectiontemp = { };

            $scope.getData = function(){
                $http({
                    method: 'GET',
                    url: 'api/inspect-items/'+$scope.tempid
                }).then(function (response) {
                    console.log(response);
                    $scope.inspection = response.data.inspection;
                    $scope.sites = response.data.sites;
                    $scope.inspectiontemp = response.data.temps;
                });
            };

            $scope.getData();

            $scope.addInspectionTemp = function(inspection) {
                $http({
                    method: 'POST',
                    url : 'api/inspect-items',
                    data: {inspection_id: inspection.id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateInspectionTemp = function(newinspectiontemp) {
                $http({
                    method: 'PUT',
                    url : 'api/inspect-items/' + newinspectiontemp.id,
                    data : {name: newinspectiontemp.name}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeInspectionTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/inspect-items/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }


            $scope.addItemListTemp = function(item) {
                $http({
                    method: 'POST',
                    url : 'api/inspect-item-checklist',
                    data: {inspection_item_id: item.id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateItemListTemp = function(newitemlist) {
                $http({
                    method: 'PUT',
                    url : 'api/inspect-item-checklist/' + newitemlist.id,
                    data : {answer: newitemlist.answer}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeItemListTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/inspect-item-checklist/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            $scope.updateInspectionTempInfo = function(inspection){
                $http({
                    method: 'PUT',
                    url : 'api/inspect-items/' + inspection.id,
                    data: { 
                        site_id: inspection.site_id,
                        site_type: inspection.site_type,
                        supervisor_id: inspection.supervisor_id,
                        inspection_date: inspection.inspection_date,
                        comments: inspection.comments,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }

            $scope.updateSiteId = function(site_id){
                $http({
                    method: 'PUT',
                    url : 'api/inspect-items/' + $scope.inspection.id,
                    data: { 
                        site_id: site_id,
                        site_type: $scope.inspection.site_type,
                        supervisor_id: $scope.inspection.supervisor_id,
                        inspection_date: $scope.inspection.inspection_date,
                        comments: $scope.inspection.comments,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }

            $scope.updateSupervisorId = function(supervisor_id){
                $http({
                    method: 'PUT',
                    url : 'api/inspect-items/' + $scope.inspection.id,
                    data: { 
                        site_id: $scope.inspection.site_id,
                        site_type: $scope.inspection.site_type,
                        supervisor_id: supervisor_id,
                        inspection_date: $scope.inspection.inspection_date,
                        comments: $scope.inspection.comments,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }

            $scope.updateComments = function(comments){
                $http({
                    method: 'PUT',
                    url : 'api/inspect-items/' + $scope.inspection.id,
                    data: { 
                        site_id: $scope.inspection.site_id,
                        site_type: $scope.inspection.site_type,
                        supervisor_id: $scope.inspection.supervisor_id,
                        inspection_date: $scope.inspection.inspection_date,
                        comments: comments,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }
        };
    }]);
})();