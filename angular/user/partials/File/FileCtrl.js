
angular.module(appName).controller('FileCtrl', function ($scope, $rootScope, $stateParams, $state, $location,
                                                            $timeout, Extention, Upload, clipboard) {
    $scope.file = {};
    ($scope.getFileByID = function () {
        Extention.post("getFileByID", { FileID: $stateParams.id }).then(function (res) {
            if (res.Status == 'success') {
                console.log(res.Data);
                $scope.file = res.Data;
            } else {
                $scope.file = null;
            }
        });
    })();
    $scope.downloadFile = function (file) {
        var absUrl = $location.absUrl();
        var i = absUrl.indexOf('#');
        var siteName = absUrl.substr(0, i);

        window.open(siteName + file.FullPath, '_blank');
    };

    $scope.copyFileLink = function (file) {
        var absUrl = $location.absUrl();
        var i = absUrl.indexOf('#');
        var siteName = absUrl.substr(0, i);
        clipboard.copyText(siteName + '../' + file.AbsolutePath);

        Extention.popInfo('لینک فایل کپی شد.');
    };
});