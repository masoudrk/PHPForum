angular.module(appName).controller('MessageCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, $stateParams, $uibModal, Extention) {

    $scope.showInbux = false;
    $scope.showSendMessage = false;
    $scope.showReciveMessage = true;

    if ($stateParams.id == $rootScope.session.UserID)
        $scope.pagingParams = {UserID: $stateParams.id, MessageType: null, searchValue: ''};

    $scope.dropDwonTitle = 'نمایش پیام ها';
    $scope.user = {};
    $scope.pagingController = {};
    $scope.messagesPagingController = {};
    $scope.user.selectedUser = [];
    $scope.users = [];
    $scope.Message = {};
    $scope.Messages = [];
    $scope.all = {};
    $scope.checkAll = function () {
        if ($scope.all.checked) {
            for (var i = 0; i < $scope.Messages.length; i++) {
                $scope.Messages[i].checked = true;
            }
        }
    };

    $scope.clearMiniSpace = function () {
        $scope.Message.MessageIn = $scope.Message.MessageIn.replace(/¬/g, " ").replace(/&#173;/g, " ").replace(/&#8204;/g, " ").replace(/&#34;/g, "\"");
    };

    $scope.getPersons = function (filter) {
        console.log(filter);
        Extention.postAsync('getUsersByName', {filter: filter}).then(function (res) {
            if (res && res.Status === 'success') {
                $scope.users = res.Data;
            } else {
                console.log(res);
                Extention.popError("مشکل در اتصال.");
            }
        });
    };
    $scope.sentMessage = function (messageType) {
        if ($scope.user.selectedUser.length == 0 || !$scope.Message.MessageIn || !$scope.Message.MessageTitle) return;
        if (messageType == 1) {
            $scope.Message.MessageType = 1;
        } else {
            $scope.Message.MessageType = 0;
        }
        $scope.Message.Message = $scope.Message.MessageIn.replace(/\r\n|\r|\n/g, "<br />");
        Extention.post('sendMessage', {Message: $scope.Message, Users: $scope.user.selectedUser}).then(function (res) {
            if (res && res.Status === 'success') {
                $scope.user.selectedUser = [];
                Extention.popSuccess("پیام های شما ارسال شد");
            } else {
                console.log(res);
                Extention.popError("مشکل در اتصال.");
            }
        }, function (err) {
            console.log(err);
        });
    };

    $scope.deleteMessage = function (message) {
        Extention.post('deleteMessage', {MessageID: message.ID}).then(function (res) {
            if (res && res.Status === 'success') {
                Extention.popSuccess("پیام با موفقیت حذف شد!");
                $scope.messagesPagingController.update();
            } else {
                console.log(res);
                Extention.popError("مشکل در حذف پیام ، لطفا دوباره امتحان کنید.");
            }
        });
    };
    $scope.deleteMessages = function () {
        var first = true;
        var query = '';
        for (var i = 0; i < $scope.Messages.length; i++) {
            if (first && $scope.Messages[i].checked) {
                first = false;
                query = '(' + $scope.Messages[i].ID;
            } else if (!first && $scope.Messages[i].checked) {
                query += ',' + $scope.Messages[i].ID;
            }
        }
        if (!first)
            query += ')';
        if (!query) return;
        Extention.post('deleteMessages', {MessagesID: query}).then(function (res) {
            if (res && res.Status === 'success') {
                Extention.popSuccess("پیام با موفقیت حذف شد!");
                $scope.messagesPagingController.update();
            } else {
                console.log(res);
                Extention.popError("مشکل در حذف پیام ، لطفا دوباره امتحان کنید.");
            }
        });
    };


    $scope.changeTypeFilter = function (type) {
        $scope.pagingParams.MessageType = type;
        switch (type) {
            case null:
                $scope.dropDwonTitle = 'همه ی پیام ها';
                break;
            case 1:
                $scope.dropDwonTitle = 'پیام های ایمیلی';
                break;
            case 0:
                $scope.dropDwonTitle = 'پیام های مستقیم';
                break;
            default:
                $scope.dropDwonTitle = 'نمایش پیام ها';
                break;
        }
        $scope.search();
    };


    $scope.openMessageModal = function (message) {
        $uibModal.open({
            animation: true,
            templateUrl: 'Message1Modal.html',
            controller: function ($scope, $uibModalInstance) {
                $scope.message = message;
                console.log($scope.message);
                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
            },
            size: 'md'
        });
    };
    $scope.showMessage = function (message) {

        if (message.MessageViewed == 0) {
            Extention.postAsync('markAsReadMessage', {MessageID: message.ID}).then(function (res) {
                for (var j = 0; j < $scope.message.all.length; j++) {
                    if ($scope.message.all[j].ID == res.Data) {
                        $scope.message.all[j].MessageViewed = 1;
                        break;
                    }
                }

                var notifyMessages = $scope.$parent.messages.All;
                for (var i = 0; i < notifyMessages.length; i++) {
                    if (notifyMessages[i].ID == res.Data) {
                        notifyMessages.splice(i, 1);
                        var notifyMessages = $scope.$parent.messages.Total -= 1;
                        break;
                    }
                }
            });
        }

        $uibModal.open({
            animation: true,
            templateUrl: 'MessageModal.html',
            controller: function ($scope, $uibModalInstance, Message) {
                $scope.message = Message;

                $scope.getAdmin = function () {
                    $state.go('UserProfile', {id: message.SenderUserID});
                    $uibModalInstance.dismiss('cancel');
                };
                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                }
            },
            size: 'md',
            resolve: {
                Message: function () {
                    return message;
                }
            }
        });
    };
    $scope.search = function () {
        $scope.messagesPagingController.update();
    };
    activeElement('#SContact');
});