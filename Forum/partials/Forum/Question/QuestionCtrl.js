angular.module(appName).controller('QuestionCtrl', function ($scope, $element, $rootScope, $routeParams, $state, $location, $timeout, $stateParams, Extention) {

    console.log($rootScope.user);

    Extention.post("getQuestionByID", { QuestionID: $stateParams.id, UserID: $rootScope.user.UserID }).then(function (res) {
        $scope.question = res;
        console.log($scope.question);
    });

	fixFooter();
});