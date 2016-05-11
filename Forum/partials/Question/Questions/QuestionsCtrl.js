angular.module(appName).controller('QuestionsCtrl', function ($scope, $element, $rootScope, $routeParams, $state, 
															  $location, $timeout, Extention) {

	// Extention.post('').then(function (res) {
	//
	// });

	activeElement('#SQuestion','#SQuestionNew');
	fixFooter();
}); 