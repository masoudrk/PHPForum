angular.module(appName).controller('QuestionCtrl', function ($scope, $element, $rootScope, $routeParams, $state, $location, $timeout, $stateParams, Extention) {

    $scope.hello = "helloo";

    console.log($stateParams.id);
    //Extention.post("getPostByID", { PostID: $stateParams.id }).then(function (res) {
    //    $scope.post = res;
    //});

    //Extention.post("getPostComments", { PostID: $stateParams.id }).then(function (res) {
    //    $scope.comments = res.Items;
    //});


	//activeElement('#SForum','#SForumHome');
	fixFooter();
});