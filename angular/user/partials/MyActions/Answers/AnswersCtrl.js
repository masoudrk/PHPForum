angular.module(appName).controller('AnswersCtrl', function ($scope, $element, $rootScope, $routeParams, $state,
															  $location, $timeout, Extention) {
    $scope.pagingController = {};

	activeElement('#SQuestion','#SAnswers');
}); 