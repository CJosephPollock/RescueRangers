'use strict';

angular.module('RescueRangersApp', [])

//Overall controller.  This is attached to the body tag in index.html.
.controller('ControlPanelCtrl', ['$scope', '$http', function($scope, $http) {

    // PHP Connection #1: Injury Type Table Names
    $scope.InjuryTypes = ["Broken Bone", "Headache", "Laceration", "Unconscious", "Unkown Injury", "Gunshot Wound", "Animal Attack"];


    // PHP Connection #2:
    $scope.IncidentTypes = {
        'Stranded': "Stranded in wilderness",
        'Lost': "Lost",
        'No Supplies': "No Food or Water",
        'Rescue': "Rescue Mission",
        'Search': "Search Mission",
        'Emergency Medical Response': "Emergency Medical Response"
    };

    $scope.KitNames = ['Blood Transfusion', 'Broken Nose Repair', 'Allergic Reaction', 'Broken Bone', 'Heart Attack', 'Panic Attack'];
    $scope.SeverityNames =['Minor', 'Moderate', 'Severe', 'Search'];


    // Submit Form Function
    $scope.submitForm = function() {
        $scope.incident['target'] = 'startIncident';
        console.log($scope.incident);
        $http({
          method: 'POST',
          url: 'models.php',
          data: $scope.incident,
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

