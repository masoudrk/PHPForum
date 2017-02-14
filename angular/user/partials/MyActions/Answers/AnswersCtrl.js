angular.module(appName).controller('AnswersCtrl', function ($scope, $element, $rootScope, $routeParams, $state,
															  $location, $timeout, Extention,Upload,$uibModal) {
    $scope.pagingController = {};
    $scope.selectedAnswer =null;
	$scope.ShowEdit = false;
	$scope.deleteAnswer = function (answer) {
		console.log(answer);
            Extention.post("deleteMyAnswer", { AnswerID: answer.ID }).then(function (res) {
                if (res.Status == 'success') {
                    $scope.pagingController.update();
                }
            });
	}
    $scope.editAnswer = function (q) {
        $scope.ShowEdit = true;
        $scope.answerTextIn = q.AnswerText;
        $scope.selectedAnswer =q;
    }
    $scope.openAnswerModal = function (answer) {
        $uibModal.open({
            animation: true,
            templateUrl: 'answerModal.html',
            controller: function ($scope, $uibModalInstance) {
                $scope.answer = answer;
                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
            },
            size: 'md'
        });
    }
    
	$scope.fieldChanged = function () {
		$scope.answerTextIn = $scope.answerTextIn.replace(/¬/g, " ").replace(/&#173;/g, " ").replace(/&#8204;/g, " ");
	}
	$scope.saveAnswer = function() {
		if (!$scope.answerTextIn || $scope.answerTextIn.length == 0) {
			Extention.popError('متن خود را وارد کنید');
			return;
		}

		Extention.setBusy(true);
		var data = {data : angular.toJson({ AnswerID: $scope.selectedAnswer.ID, AnswerText: $scope.answerTextIn }) };

		var u = Upload.upload({
			url: serviceBaseURL + 'updateAnswer',
			method: 'POST',
			file: $scope.myFiles,
			data: data
		});

		u.then(function(resp) {
			// file is uploaded successfully
			Extention.setBusy(false);
			$scope.answerTextIn = '';
			$scope.myFiles = [];
            $scope.ShowEdit = false;
            $scope.pagingController.update();
			Extention.popInfo('پاسخ شما تغییر کرد . در صورت تایید نمایش داده خواهد شد');
		}, function(resp) {
			// handle error
			Extention.setBusy(false);
			Extention.popError('مشکل در تغییر جواب سوال ، لطفا دوباره تلاش کنید!');
		});
	}
	activeElement('#SQuestion','#SAnswers');
}); 