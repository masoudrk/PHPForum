
angular.module(appName).controller('AwardQuestionsCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention, $stateParams, $uibModal) {

    $scope.pagingParams = {};
	$scope.pagingController = {};

	$scope.search = function () {
		$scope.pagingController.update();
	}

	$scope.changeQuestionState = function (uid, s ) {
	    Extention.post('changeQuestionAccepted', { State: s, QuestionID: uid.ID, AdminPermissionLevel: session.AdminPermissionLevel, UserID: uid.AuthorID, QuestionSubjectID: uid.SubjectID }).then(function (res) {
			if(res && res.Status == 'success'){
			    Extention.popSuccess("وضعیت سوال با موفقیت تغییر کرد!");
				$scope.pagingController.update();
			}else{
			    Extention.popError("مشکل در تغییر وضعیت سوال ، لطفا دوباره تلاش کنید.");
			}
		});
	}

	$scope.removeQuestion = function (uid ,AuthorID) {
	    Extention.post('deleteQuestion', { QuestionID: uid, AdminPermissionLevel: session.AdminPermissionLevel, UserID: AuthorID }).then(function (res) {
			if(res && res.Status=='success'){
			    Extention.popSuccess("سوال با موفقیت حذف شد!");
				$scope.pagingController.update();
			} else {
			    console.log(res);
			    Extention.popError("مشکل در حذف سوال ، لطفا دوباره امتحان کنید.");
			}
		});
	}


	$scope.openRoleModal = function (Question ) {
	    var modalInstance = $uibModal.open({
	        animation: true,
	        templateUrl: 'myModalContent.html',
	        controller: function ($scope, $uibModalInstance) {
	            $scope.Question = Question;
	            $scope.QuestionText = $scope.Question.QuestionText;
	            $scope.Title = $scope.Question.Title;
	            $scope.cancel = function () {
	                $uibModalInstance.dismiss('cancel');
	            };
	            $scope.editQuestion = function() {
	                if ($scope.Title && $scope.QuestionText) {
	                    Extention.post('editQuestion', { QuestionID: $scope.Question.ID, QuestionText: $scope.QuestionText, Title: $scope.Title }).then(function (res) {
	                        if (res && res.Status == 'success') {
	                            Extention.popSuccess("سوال با موفقیت ویرایش شد!");
                                $uibModalInstance.close('success');
	                            $scope.editMode = false;
	                        } else {
	                            Extention.popError("مشکل در ویرایش سوال ، لطفا دوباره امتحان کنید.");
	                        }
	                    });
	                }
	            }
	        },
	        size: 'md'
	    });
        modalInstance.result.then(function (res) {
            if(res == 'success'){
                $scope.pagingController.update();
            }
        }, function () {
        });
	}


	$scope.openDiscardModal = function (questionID , authorID) {
	    Extention.post('getCommonMessages', { filter: 'رد سوال' }).then(function (res) {
	        if (res && res.Status == 'success') {
	            var modalInstance = $uibModal.open({
	                animation: true,
	                templateUrl: 'DiscardQuestionModal.html',
	                controller: function ($scope, $uibModalInstance) {
	                    $scope.common = res.Data;
	                    $scope.questionID = questionID;
	                    $scope.authorID = authorID;
	                    $scope.cancel = function () {
	                        $uibModalInstance.dismiss('cancel');
	                    };

	                    $scope.send = function (message) {
	                        Extention.post('changeQuestionAccepted', { State: -1, QuestionID: $scope.questionID, AdminPermissionLevel: session.AdminPermissionLevel, UserID: $scope.authorID, Message: message }).then(function (res) {
	                            if (res && res.Status == 'success') {
	                                Extention.popSuccess("وضعیت سوال با موفقیت تغییر کرد!");
	                                $uibModalInstance.close('success');
	                            } else {
	                                Extention.popError("مشکل در تغییر وضعیت سوال ، لطفا دوباره تلاش کنید.");
	                            }
	                        });
	                    };
	                },
	                size: 'md'
	            });
	            modalInstance.result.then(function (res) {
	            	if(res == 'success'){
                        $scope.pagingController.update();
					}
	            }, function () {
	            });
	        } else {
	            return;
	        }

	    });
	}


    activeElement('#SAwardQuestion', '#SAllAwardQuestion');
	fixFooter();

});