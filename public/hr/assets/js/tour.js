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
        
        $scope.tourTempId = function(tour_id){
            $scope.tempid = tour_id;
            $scope.tour = { };
            $scope.toursites = [ ];
            $scope.newtoursite = { };
            $scope.tourtemp = [ ];
            $scope.newtourtemp = { };
            $scope.tourimages = [ ];
            $scope.safeacts = [ ];
            $scope.newsafeacttemp = { };
            $scope.unsafeacts = [ ];
            $scope.newunsafeacttemp = { };

            $scope.getData = function(){
                $http({
                    method: 'GET',
                    url: 'api/tour-items/'+$scope.tempid
                }).then(function (response) {
                    console.log(response);
                    $scope.tour = response.data.tour;
                    $scope.toursites = response.data.toursites;
                    $scope.tourtemp = response.data.temps;
                    $scope.tourimages = response.data.images;
                    $scope.safeacts = response.data.safeacts;
                    $scope.unsafeacts = response.data.unsafeacts;
                });
            };

            $scope.getData();

            $scope.addTourSite = function(tourid, site_id){
                $http({
                    method: 'POST',
                    url : 'api/tour-sites',
                    data: {tour_id: tourid, site_id: site_id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }
            $scope.addUnregisteredSite = function(tour){
                $http({
                    method: 'POST',
                    url : 'api/tour-sites',
                    data: {tour_id: tour.id, site_id: ''}
                }).then(function (response){
                   $scope.getData(); 
                });
            }
            $scope.updateTourSite = function(newtoursite) {
                $http({
                    method: 'PUT',
                    url : 'api/tour-sites/'+newtoursite.id,
                    data : {site_name: newtoursite.site_name, subcontractors: newtoursite.subcontractors}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeTourSite = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/tour-sites/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            $scope.addtourTemp = function(tour) {
                $http({
                    method: 'POST',
                    url : 'api/tour-items',
                    data: {tour_id: tour.id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateTourTemp = function(newtourtemp) {
                $http({
                    method: 'POST',
                    url : 'api/tour-items/other-checks',
                    data : {tour_item_id: newtourtemp.id, other_checks: newtourtemp.other_checks}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removetourTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/tour-items/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }


            $scope.addItemListTemp = function(item) {
                $http({
                    method: 'POST',
                    url : 'api/tour-item-checklist',
                    data: {tour_item_id: item.id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateItemListTemp = function(newitemlist) {
                $http({
                    method: 'PUT',
                    url : 'api/tour-item-checklist/' + newitemlist.id,
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
                    url : 'api/tour-item-checklist/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            $scope.updateTourTempInfo = function(tour){
                $http({
                    method: 'PUT',
                    url : 'api/tour-items/' + tour.id,
                    data: { 
                        tour_start_date: tour.tour_start_date,
                        tour_end_date: tour.tour_end_date,
                        understand_the_work: tour.understand_the_work,
                        know_the_rule: tour.know_the_rule,
                        know_the_procedure: tour.know_the_procedure,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }

            
            $scope.updateTourDates = function(tour_start_date, tour_end_date){
                $http({
                    method: 'PUT',
                    url : 'api/tour-items/' + $scope.tour.id,
                    data: { 
                        tour_start_date: tour_start_date,
                        tour_end_date: tour_end_date,
                        understand_the_work: $scope.tour.understand_the_work,
                        know_the_rule: $scope.tour.know_the_rule,
                        know_the_procedure: $scope.tour.know_the_procedure,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }

            $scope.removeImage = function(id){
                Swal.fire({
                    title: 'Are you sure, You want to remove the Image?',
                    showDenyButton: true,
                    confirmButtonText: 'Yes Remove',
                    denyButtonText: `Don't Remove`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $http({
                            method: 'DELETE',
                            url : 'api/tour-images/' +id 
                        }).then(function(response) {
                            $scope.getData();
                        });
                        // Swal.fire('Removed!', '', 'success')
                    } else if (result.isDenied) {
                        Swal.fire('Image not removed', '', 'info')
                    }
                });
            }


            $scope.addSafeActTemp = function(tour) {
                $http({
                    method: 'POST',
                    url : 'api/safe-acts',
                    data: {tour_id: tour.id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateSafeActTemp = function(newsafeacttemp) {
                $http({
                    method: 'PUT',
                    url : 'api/safe-acts/'+newsafeacttemp.id,
                    data : {act_name: newsafeacttemp.act_name, action_taken: newsafeacttemp.action_taken, responsibility: newsafeacttemp.responsibility, date: newsafeacttemp.date}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeSafeActTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/safe-acts/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            //Unsafe acts
            $scope.addUnsafeActTemp = function(tour) {
                $http({
                    method: 'POST',
                    url : 'api/unsafe-acts',
                    data: {tour_id: tour.id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateUnsafeActTemp = function(newunsafeacttemp) {
                $http({
                    method: 'PUT',
                    url : 'api/unsafe-acts/'+newunsafeacttemp.id,
                    data : {act_name: newunsafeacttemp.act_name, immediate_action: newunsafeacttemp.immediate_action, prevent_action: newunsafeacttemp.prevent_action, responsibility: newunsafeacttemp.responsibility, date: newunsafeacttemp.date}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeUnsafeActTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/unsafe-acts/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }
        };
    }]);
    

    app.directive("datepicker", function () {

        function link(scope, element, attrs) {
            // CALL THE "datepicker()" METHOD USING THE "element" OBJECT.
            element.datepicker({
                dateFormat: "yy-mm-dd"
            });
        }

        return {
            require: 'ngModel',
            link: link
        };
    });
})();