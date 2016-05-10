angular.module(appName).controller('TransitionCtrl', function ($scope, $element, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

	// Extention.post('').then(function (res) {
	//
	// });

	activeElement('#SForum','#STransition');
	fixFooter();
});