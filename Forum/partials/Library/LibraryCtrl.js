
angular.module(appName).controller('LibraryCtrl', function ($scope, $rootScope, $stateParams, $state, $location, $timeout, Extention, Upload) {

    $scope.files = [];

    $scope.getBoxColor = function (id) {
        id = id % 5 + 1;
        switch (id) {
            case 1:
                return 'box-success';
            case 2:
                return 'box-warning';
            case 3:
                return 'box-info';
            case 4:
                return 'box-primary';
            case 5:
                return 'box-danger';
            default:
                return 'box-success';
        }
    }
    activeElement('#SLibrary');
});