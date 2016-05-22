angular.module(appName).controller('QuestionCtrl', function ($scope, $element, $rootScope, $routeParams, $state, $location, $timeout, $stateParams, Extention) {

    $scope.isOnline = false;
    $scope.question = {};

    Extention.post("getQuestionByID", { QuestionID: $stateParams.id, UserID: $rootScope.user.UserID }).then(function (res) {
        $scope.question = res;
        console.log($scope.question);
        $scope.checkNowOnline();
        $timeout(function () {
            fixFooter();
        } );
    });

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
        Extention.post("saveAnswer", { QuestionID: $stateParams.id, AnswerText: $scope.answerText }).then(function (res) {
            if (res == true) {
                $scope.answerText = '';
                Extention.popInfo('پاسخ شما ثبت شد . در صورت تایید نمایش داده خواهد شد');
                console.log(res);
            }
        });
    }

    $scope.setLikeQuestion = function(rate) {
        Extention.post("rateQuestion", { QuestionID: $stateParams.id, UserID: $rootScope.user.UserID, RateValue: rate, TargetUserID: $scope.question.UserID }).then(function (res) {
            $scope.question.PersonQuestionRate = res;
            $scope.question.QuestionScore = Number(res) + Number($scope.question.QuestionScore);
        });
    }

    $scope.setLikeAnswers = function (answer,rate) {
        Extention.post("rateAnswer", { AnswerID: answer.ID, UserID: $rootScope.user.UserID, RateValue: rate, TargetUserID: answer.UserID }).then(function (res) {
            for (var i = 0; i < $scope.question.Answers.length ; i++) {
                if ($scope.question.Answers[i].ID == answer.ID)
                {
                    $scope.question.Answers[i].PersonAnswerRate = res;
                    $scope.question.Answers[i].AnswerScore = res + Number($scope.question.Answers[i].AnswerScore);
                    return;
                }
            }
        });
    }

    $scope.followQuestion = function () {
        Extention.postAsync("followQuestion", { QuestionID: $stateParams.id, UserID: $rootScope.user.UserID }).then(function (res) {
            if (res == true) {
                $scope.question.FollowCount = number($scope.question.FollowCount) + 1;
                $scope.question.PersonFollow = 1;
            }
        });
    }

    $scope.unFollowQuestion = function () {
        Extention.postAsync("unFollowQuestion", { QuestionID: $stateParams.id, UserID: $rootScope.user.UserID }).then(function (res) {
            if (res == true) {
                $scope.question.FollowCount = number($scope.question.FollowCount) - 1;
                $scope.question.PersonFollow = 0;
            }
        });
    }

    $scope.followPerson = function (personID) {
        Extention.postAsync("followPerson", { TargetUserID: personID, UserID: $rootScope.user.UserID }).then(function (res) {
            if (res == true) {
                for (var i = 0; i < $scope.question.Answers.length ; i++) {
                    if ($scope.question.Answers[i].UserID == personID)
                        {$scope.question.Answers[i].PersonFollow = 1;return;}
                }
            }
        });
    }

    $scope.unFollowPerson = function (personID) {
        Extention.postAsync("unFollowPerson", { TargetUserID: personID, UserID: $rootScope.user.UserID }).then(function (res) {
            if (res == true) {
                for (var i = 0; i < $scope.question.Answers.length ; i++) {
                    if ($scope.question.Answers[i].UserID == personID)
                    { $scope.question.Answers[i].PersonFollow = 0; return; }
                }
            }
        });
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
            case 3:
                return 'label-info';
            case 4:
                return 'label-primary';
            case 5:
                return 'label-danger';
            default:
                return 'label-success';
        }
    }
});