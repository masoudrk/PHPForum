
angular.module(appName).controller('MessageCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, $stateParams, $uibModal, Extention) {

    $scope.showInbux = true;
    $scope.pagingParams = { UserID: $stateParams.id, MessageType: null, searchValue :''};
    $scope.dropDwonTitle = 'نمایش پیام ها';
    $scope.user = {};
    $scope.pagingController = {};
    $scope.user.selectedUser = [];
    $scope.users = [];
    $scope.Message = {};
    $scope.getPersons = function (filter) {
        console.log(filter);
        Extention.postAsync('getUsersByName', { filter: filter }).then(function (res) {
            if (res && res.Status == 'success') {
                $scope.users = res.Data;
            	}else{
            		Extention.popError("مشکل در اتصال.");
            	}
            });
    }
    $scope.sentMessage = function (messageType) {
        if ($scope.user.selectedUser.length == 0 || !$scope.Message.Message || !$scope.Message.MessageTitle) return;
        if (messageType == 1) {
            $scope.Message.MessageType = 1;
        } else {
            $scope.Message.MessageType = 0;
        }
        Extention.post('sendMessage', { Message: $scope.Message, Users: $scope.user.selectedUser }).then(function (res) {
            if (res && res.Status == 'success') {
                $scope.user.selectedUser = [];
                Extention.popSuccess("پیام های شما ارسال شد");
            } else {
                Extention.popError("مشکل در اتصال.");
            }
        }, function(err) {
            console.log(err);
        });
    }

    $scope.deleteMessage = function (message) {
        Extention.post('deleteMessage', { MessageID: message.ID}).then(function (res) {
            if (res && res.Status == 'success') {
                Extention.popSuccess("پیام با موفقیت حذف شد!");
                $scope.pagingController.update();
            } else {
                Extention.popError("مشکل در حذف پیام ، لطفا دوباره امتحان کنید.");
            }
        });
    }

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
    }


    $scope.openMessageModal = function (message) {
        $uibModal.open({
            animation: true,
            templateUrl: 'MessageModal.html',
            controller: function ($scope, $uibModalInstance) {
                $scope.message = message;
                console.log($scope.message);
                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
            },
            size: 'md'
        });
    }

    $scope.search = function () {
        $scope.pagingController.update();
    }
    activeElement('#SContact');
});