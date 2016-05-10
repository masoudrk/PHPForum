angular.module(appName).controller('DataSwitchCtrl', function ($scope, $element, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

	// Extention.post('').then(function (res) {
	//
	// });

	activeElement('#SForum','#SDataSwitch');
	fixFooter();
});