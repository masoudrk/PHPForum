
angular.module(appName).controller('LibraryCtrl', function ($scope, $rootScope, $stateParams, $state, $location,
                                                            $timeout, Extention, Upload, clipboard) {

    $scope.files = [];

    $scope.getBoxColor = function (id) {
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

    $scope.downloadFile = function (file) {
        var absUrl = $location.absUrl();
        var i = absUrl.indexOf('#');
        var siteName = absUrl.substr(0,i);

        window.open(siteName + file.FullPath,'_blank');
    };

    $scope.copyFileLink = function (file) {
        var absUrl = $location.absUrl();
        var i = absUrl.indexOf('#');
        var siteName = absUrl.substr(0,i);
        clipboard.copyText(siteName +'../'+ file.AbsolutePath);

        Extention.popInfo('لینک فایل کپی شد.');
    };
    activeElement('#SLibrary');
});