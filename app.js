'use strict';

angular.module('RescueRangersApp', [])

//Overall controller.  This is attached to the body tag in index.html.  
.controller('ControlPanelCtrl', ['$scope', '$http', function($scope, $http) {

    // PHP Connection #1: Injury Type Table Names 
    $scope.InjuryTypes = ["broken bone", "headache"];


    // PHP Connection #2: 
    $scope.IncidentTypes = ["stranded in wilderness", "lost", "hungry"];



    // Submit Form Function
    $scope.submitForm = function() {
        $http({
          method: 'GET',
          url: 'models.php?f=startIncident'
        }).then(function successCallback(response) {
            console.log(response)
            // this callback will be called asynchronously
            // when the response is available
          }, function errorCallback(response) {
            // called asynchronously if an error occurs
            // or server returns response with an error status.
          });


        // $scope.InjuryType

        // $scope.IncidentType

        // $scope.LocationName
        // $scope.Latitude
        // $scope.Longitude

        // $scope.PersonFirstName
        // $scope.PersonLastName

        // $scope.StreetName
        // $scope.City
        // $scope.State
        // $scope.Country
    }


}])

    