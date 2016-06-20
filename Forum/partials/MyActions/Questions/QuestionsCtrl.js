angular.module(appName).controller('QuestionsCtrl', function ($scope, $element, $rootScope, $routeParams, $state, 
															  $location, $timeout, Extention) {
    $scope.pagingController = {};

    $scope.removeQuestion = function (id) {

        Extention.post('deleteQuestion',{QuestionID : id}).then(function (res) {
           if(res && res.Status == 'success'){
               Extention.popSuccess('سوال با موفقیت حذف شد !');
               $scope.pagingController.update();
           }else{
               Extention.popError('مشکل در حذف سوال ، لطفا دوباره تلاش کنید.');
           }
        });
    };

    $scope.getQuestion = function (q) {
        if(q.AdminAccepted == 1)
            $state.go('question',{id:q.ID});
    }
	activeElement('#SQuestion','#SQuestions');
}); 