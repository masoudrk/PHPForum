angular.module(appName).controller('HomeCtrl', function ($scope, $element, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

    $scope.labels = ["January", "February", "March", "April", "May", "June", "July"];
    $scope.series = ['Series A', 'Series b'];
    $scope.seriess = ['Series A', 'Series b'];
    $scope.data = [[28, 48, 40, 19, 40, 19 , 65],
      [65, 59, 80, 81, 56, 55, 40]
    ];
    $scope.labelss = ["دیتا سوئیچ", "رادیویی", "سیستم های انتقال", "خطوط انتقال"];
    $scope.datas = [
      [28, 48, 40, 19],
      [65, 59, 90, 81]
    ];
    $scope.onClick = function (points, evt) {
        console.log(points, evt);
    }
	activeElement('#SForum','#SForumHome');
	fixFooter();
});