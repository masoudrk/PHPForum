
angular.module(appName).controller('CommonMessageCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention) {
    $scope.pagingParams = {};
    $scope.pagingController = {};
    $scope.Messages = [];
    $scope.messageType = 'رد سوال';
    $scope.messageName = '';
    //$scope.pagingController.update();

    $scope.removeMessage = function(messageID) {
        Extention.post('deleteMessage', { MessageID: messageID }).then(function (res) {
            if (res && res.Status == 'success') {
                Extention.popSuccess("پیام با موفقیت حذف شد!");
                $scope.pagingController.update();
            } else {
                Extention.popError("مشکل در حذف پیام ، لطفا دوباره امتحان کنید.");
            }
        });
    }

    $scope.insertMessage = function () {
        if ($scope.messageType && $scope.messageName && $scope.messageName.length >0) {
            Extention.post('insertMessage', { MessageType: $scope.messageType, MessageName: $scope.messageName }).then(function (res) {
                if (res && res.Status == 'success') {
                    $scope.messageType = 'رد سوال';
                    $scope.messageName = '';
                    Extention.popSuccess("پیام اضافه شد");
                    $scope.pagingController.update();
                } else {
                    console.log(res);
                    Extention.popError("مشکل در وارد کردن پیام ، لطفا دوباره تلاش کنید.");
                }
            });
        }
    }

	activeElement('#SMeta', '#SCommonMessages');
});