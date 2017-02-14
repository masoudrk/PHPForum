
angular.module(appName).controller('LibraryCtrl', function ($scope, $rootScope, $stateParams, $state, $location,
                                                            $timeout, Extention, Upload, clipboard) {

    $scope.files = [];
    $scope.pagingController ={};
    $scope.childSubject={selected:null};
    $scope.tag={selected:null};
    $scope.mainSubject={selected:null};
    $scope.pagingParams = {childSubjectID : null ,tagID : null ,mainSubjectID:null};
    $scope.tags = [];
    $scope.allChildSubjects = [];
    $scope.allSubjects = [];

    Extention.postAsync('getLibraryData',{}).then(function (res) {
        if(res && res.Status=='success'){
            $scope.tags = res.Data.Tags;
            $scope.allSubjects = res.Data.AllSubjects;
        }else{
            Extention.popError("مشکل در گرفتن اطلاعات");
        }
    });

    $scope.changeFilter = function (filterType) {
        switch (filterType){
            case 1:
                $scope.pagingParams.tagID = ($scope.tag.selected)?$scope.tag.selected.ID:null;
                break;
            case 2:
                $scope.pagingParams.childSubjectID = ($scope.childSubject.selected)?$scope.childSubject.selected.ID:null;
                break;
            case 3:
                $scope.pagingParams.mainSubjectID = ($scope.mainSubject.selected)?$scope.mainSubject.selected.ID:null;
                if($scope.mainSubject.selected){
                    Extention.postAsync('getForumSubjects',{ID : $scope.mainSubject.selected.ID}).then(function (res) {
                        if(res && res.Status=='success'){
                            $scope.allChildSubjects = res.Data;
                        }else{
                            Extention.popError("مشکل در گرفتن اطلاعات");
                        }
                    });
                }else $scope.allChildSubjects = [];
                break;
        }
        $scope.search();
    }

    $scope.search = function () {
        $scope.pagingController.update();
    }
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