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
        // $scope.InjuryType

        // $scope.IncidentType

        // $scope.LocationName
        // $scope.Latitude
        // $scope.Longitude

        // $scope.PersonFirstName
        // $scope.PerosnLastName

        // $scope.StreetName
        // $scope.City
        // $scope.State
        // $scope.Country
    }


}])

    