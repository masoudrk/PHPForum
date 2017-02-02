angular.module(appName).controller('HomeCtrl', function ($scope, $element, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

	$scope.changeView = function (id) {
	    hideCMS(false);
	    $state.go('main_forum', { id: id });
	}

	activeElement('#SForum','#SForumHome');
});