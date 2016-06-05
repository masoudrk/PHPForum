
angular.module(appName).controller('QuestionsCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention, $stateParams, $uibModal) {
    $scope.adminType = $stateParams.id;
    $scope.pagingParams = { SubjectName: $stateParams.id, questionType: null};
	$scope.pagingController = {};
	$scope.dropDwonTitle = 'نمایش سوال ها';

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
			}else{
			    Extention.popError("مشکل در حذف سوال ، لطفا دوباره امتحان کنید.");
			}
		});
	}

	$scope.changeTypeFilter = function(type) {
	    $scope.pagingParams.answerType = type;
	    switch (type) {
	        case null:
	            $scope.dropDwonTitle = 'همه ی سوال ها';
	            break;
            case 1:
                $scope.dropDwonTitle = 'سوال ها تایید شده';
                break;
            case 0:
                $scope.dropDwonTitle = 'سوال ها در انتظار تایید';
                break;
            case -1:
                $scope.dropDwonTitle = 'سوال ها تایید نشده';
                break;
	        default:
	            $scope.dropDwonTitle = 'نمایش سوال ها';
                break;
	    }
	    $scope.search();
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
	    modalInstance.result.then(function () {
	    }, function () {
	        $scope.pagingController.update();
	    });
	}

	$scope.openExchangeModal = function (question) {
	    Extention.post('getSubjects', { }).then(function (res) {
	        if (res && res.Status == 'success') {
	            var modalInstance = $uibModal.open({
	                animation: true,
	                templateUrl: 'ExchangeModal.html',
	                controller: function ($scope, $uibModalInstance) {
	                    $scope.subjects = res.Data;
	                    $scope.question = question;
	                    $scope.cancel = function () {
	                        $uibModalInstance.dismiss('cancel');
	                    };
	                    $scope.send = function (subject) {
	                        Extention.post('exchangeQuestion', { SubjectID: subject.ID, QuestionID: $scope.question.ID}).then(function (res) {
	                            if (res && res.Status == 'success') {
	                                Extention.popSuccess("سوال با موفقیت انتقال داده شد!");
	                                $uibModalInstance.dismiss('cancel');
	                            } else {
	                                Extention.popError("مشکل در انتقال سوال ، لطفا دوباره امتحان کنید.");
	                            }
	                        });
	                    };
	                },
	                size: 'md'
	            });

	            modalInstance.result.then(function () {
	            }, function () {
	                $scope.pagingController.update();
	            });

	        } else {
	            return;
	        }
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
	                                $uibModalInstance.dismiss('cancel');
	                            } else {
	                                Extention.popError("مشکل در تغییر وضعیت سوال ، لطفا دوباره تلاش کنید.");
	                            }
	                        });
	                    };
	                },
	                size: 'md'
	            });
	            modalInstance.result.then(function () {
	            }, function () {
	                $scope.pagingController.update();
	            });
	        } else {
	            return;
	        }

	    });
	}

    console.log('#S' + $stateParams.id);
    activeElement('#SQuestions', '#SS' + $stateParams.id);
	fixFooter();

});