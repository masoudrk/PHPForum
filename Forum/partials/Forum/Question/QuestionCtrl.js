angular.module(appName).controller('QuestionCtrl', function ($scope, $element, $rootScope,$uibModal,
                                                             $routeParams, $state, $location, $timeout, $stateParams, Extention ,Upload) {

    $scope.isOnline = false;
    $scope.question = {};

    ($scope.getQuestionByID = function () {
        Extention.post("getQuestionByID", { QuestionID: $stateParams.id, UserID: $rootScope.user.UserID }).then(function (res) {
            $scope.question = res;
            $scope.checkNowOnline();
        });
    })();

    

    $scope.setBestAnswer = function(AnswerID) {
        Extention.post("setBestAnswer", { QuestionID: $stateParams.id, AnswerID: AnswerID }).then(function (res) {
            if (res.Status == 'success') {
                $scope.getQuestionByID();
            }
        });
    }

    $scope.openAttachments = function (att) {
        $uibModal.open({
            animation: true,
            templateUrl: 'myModalContent.html',
            controller: function ($scope, $uibModalInstance,Attachment) {

                $scope.attachment = Attachment;
                $scope.downloadAttachment = function () {
                    var absUrl = $location.absUrl();
                    var i = absUrl.indexOf('#');
                    var siteName = absUrl.substr(0,i);

                    window.open(siteName + $scope.attachment.FullPath,'_blank');
                };

                $scope.ok = function () {
                    $uibModalInstance.close($scope.selected.item);
                };

                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
            },
            size: 'md',
            resolve: {
                Attachment: function () {
                    return att;
                }
            }
        });
    }

    $rootScope.$on("socketDataChanged", function(){
        $scope.checkNowOnline();
    });

    $scope.checkNowOnline = function () {
        if(!$scope.question.UserID)
            return;
        var ous =  $scope.socketData.OnlineUsers;
        for (var i = 0 ; i < ous.length ; i++){
            if($scope.question.UserID == ous[i].ID ){
                $scope.isOnline = true;
                return;
            }
        }
        $scope.isOnline = false;
    }

    $scope.saveAnswer = function() {
        if (!$scope.answerText || $scope.answerText.length == 0) {
            Extention.popError('متن خود را وارد کنید');
            return;
        }

        Extention.setBusy(true);
        var data = {data : angular.toJson({ QuestionID: $stateParams.id, AnswerText: $scope.answerText }) };

        var u = Upload.upload({
            url: serviceBaseURL + 'saveAnswer',
            method: 'POST',
            file: $scope.myFiles,
            data: data
        });

        u.then(function(resp) {
            // file is uploaded successfully
            Extention.setBusy(false);
            $scope.answerText = '';
            $scope.myFiles = [];
            $scope.getQuestionByID();
            Extention.popInfo('پاسخ شما ثبت شد . در صورت تایید نمایش داده خواهد شد');
        }, function(resp) {
            // handle error
            Extention.setBusy(false);
            Extention.popError('مشکل در ثبت سوال سوال ، لطفا دوباره تلاش کنید!');
        });
        //
        // Extention.post("saveAnswer", { QuestionID: $stateParams.id, AnswerText: $scope.answerText }).then(function (res) {
        //     if (res == true) {
        //         $scope.answerText = '';
        //         Extention.popInfo('پاسخ شما ثبت شد . در صورت تایید نمایش داده خواهد شد');
        //         console.log(res);
        //     }
        // });
    }

    $scope.setLikeQuestion = function(rate) {
        Extention.post("rateQuestion", { QuestionID: $stateParams.id, UserID: $rootScope.user.UserID, RateValue: rate, TargetUserID: $scope.question.UserID, AuthorID: $scope.question.AuthorID }).then(function (res) {
            $scope.getQuestionByID();
        });
    }

    $scope.setLikeAnswers = function (answer,rate) {
        Extention.post("rateAnswer", { AnswerID: answer.ID, UserID: $rootScope.user.UserID, RateValue: rate, TargetUserID: answer.UserID, AuthorID: answer.AuthorID, QuestionID: $stateParams.id }).then(function (res) {
            $scope.getQuestionByID();
        });
    }

    $scope.followQuestion = function () {
        Extention.postAsync("followQuestion", { QuestionID: $stateParams.id, UserID: $rootScope.user.UserID, AuthorID: $scope.question.AuthorID }).then(function (res) {
            if (res.Status == 'success') {
                $scope.getQuestionByID();
            }
        });
    }

    $scope.unFollowQuestion = function () {
        Extention.postAsync("unFollowQuestion", { QuestionID: $stateParams.id, UserID: $rootScope.user.UserID }).then(function (res) {
            if (res.Status == 'success') {
                $scope.getQuestionByID();
            }
        });
    }

    $scope.followPerson = function (personID) {
        Extention.postAsync("followPerson", { TargetUserID: personID, UserID: $rootScope.user.UserID }).then(function (res) {
            if (res.Status == 'success') {
                $scope.getQuestionByID();
            }
        });
    }

    $scope.unFollowPerson = function (personID) {
        Extention.postAsync("unFollowPerson", { TargetUserID: personID, UserID: $rootScope.user.UserID }).then(function (res) {
            if (res.Status == 'success') {
                $scope.getQuestionByID();
            }
        });
    }

    $scope.removeFile = function (file) {
        var index = $scope.myFiles.indexOf(file);
        $scope.myFiles.splice(index,1);
    }

    $scope.getBoxColor = function(id) {
        id = id % 5 + 1;
        switch (id) {
            case 1:
                return 'box-success';
            case 2:
                return 'box-warning';
            case 3:
                return 'box-info';
            case 4:
                return 'box-primary';
            case 5:
                return 'box-danger';
            default:
                return 'box-success';
        }
    }

    $scope.getTagColor = function (id) {
        id = id % 5 + 1;
        switch (id) {
            case 1:
                return 'label-success';
            case 2:
                return 'label-warning';
            case 4:
                return 'label-primary';
            case 5:
                return 'label-danger';
            default:
                return 'label-success';
        }
    }
    $scope.getBGColor = function(id) {
        id = id % 15 + 1;
        switch (id) {
            case 1:
                return 'bg-red-active';
            case 2:
                return 'bg-yellow-active';
            case 3:
                return 'bg-aqua-active';
            case 4:
                return 'bg-blue-active';
            case 5:
                return 'bg-light-blue-active';
            case 6:
                return 'bg-green-active';
            case 7:
                return 'bg-navy-active';
            case 8:
                return 'bg-teal-active';
            case 9:
                return 'bg-olive-active';
            case 10:
                return 'bg-lime-active';
            case 11:
                return 'bg-orange-active';
            case 12:
                return 'bg-fuchsia-active';
            case 13:
                return 'bg-purple-active';
            case 14:
                return 'bg-maroon-active';
            case 15:
                return 'bg-black-active';
            default:
                return 'bg-red-active';
        }
    }
});