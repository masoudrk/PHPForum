angular.module(appName).controller('HomeCtrl', function ($scope, $element, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

    $scope.labels = ["January", "February", "March", "April", "May", "June", "July"];
    $scope.series = ['Series A', 'Series B'];
    $scope.data = [
      [65, 59, 80, 81, 56, 55, 40],
      [28, 48, 40, 19, 86, 27, 90]
    ];
    $scope.labelss = ["Eating", "Drinking", "Sleeping", "Designing", "Coding", "Cycling", "Running"];
    $scope.datas = [
      [65, 59, 90, 81, 56, 55, 40],
      [28, 48, 40, 19, 96, 27, 100]
    ];
    $scope.onClick = function (points, evt) {
        console.log(points, evt);
    }
	activeElement('#SForum','#SForumHome');
	fixFooter();
});