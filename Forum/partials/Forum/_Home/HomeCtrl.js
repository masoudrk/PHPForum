angular.module(appName).controller('HomeCtrl', function ($scope, $element, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

	// Extention.post('').then(function (res) {
	//
	// });

	activeElement('#SForum','#SForumHome');
	fixFooter();
});