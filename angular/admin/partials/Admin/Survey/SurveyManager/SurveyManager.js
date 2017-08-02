
angular.module(appName).controller('SurveyManagerCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, $stateParams, $uibModal, Extention) {

    $scope.pagingController={};
    $scope.deleteSurvey = function (Data) {
        Extention.post('Survey/deleteSurvey', { surveyID: Data.ID}).then(function (res) {
            if (res && res.Status == 'success') {
                Extention.popSuccess("نظرسنجی با موفقیت حذف شد!");
                $scope.pagingController.update();
            } else {
                Extention.popError("مشکل در حذف پیام ، لطفا دوباره امتحان کنید.");
            }
        });
    }
    $scope.openSurveyModal  = function (Data) {
        $uibModal.open({
            animation: true,
            templateUrl: 'survey.html',
            controller: function ($scope, $uibModalInstance) {
                $scope.Survey = {};
                Extention.post('Survey/getSurveyByID', { SurveyID: Data.ID}).then(function (res) {
                    if (res && res.Status == 'success') {
                        $scope.Survey = res.Data;
                    }
                });

                $scope.getProgressValueStyle = function (Count ,Total) {
                    var total  = Number(Total);
                    var count  = Number(Count );
                    if(total == 0){
                        return 0;
                    }else{
                        return ((count / total) *100);
                    }
                }

                $scope.getProgresColor = function (ID) {
                    ID = ID % 5 + 1;
                    switch (ID) {
                        case 1:
                            return 'progress-bar-green';
                        case 2:
                            return 'progress-bar-danger';
                        case 3:
                            return 'progress-bar-info';
                        case 4:
                            return 'progress-bar-warning';
                        case 5:
                            return 'progress-bar-primary';
                        default:
                            return 'progress-bar-yellow';
                    }
                }
                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
            },
            size: 'md'
        });
    }

    $scope.openSurveyUpdate  = function (Data) {
        var modalInstance =$uibModal.open({
            animation: true,
            templateUrl: 'surveyUpdate.html',
            controller: function ($scope, $uibModalInstance) {
                $scope.mindate =Date.now();
                $scope.ExpireDate=new Date(Date.parse(Data.ExpireDate.replace('-','/','g'))).getTime();
                $scope.survey = Data;
                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
                $scope.upgrade = function () {
                    if(!$scope.toFull.unix){
                        Extention.popError('تاریخ اتمام نظرسنجی را انتخاب کنید');return;
                    }
                    var ExpireDate = new Date($scope.toFull.unix);
                    Extention.post('Survey/updateSurvey',{ExpireDate : ExpireDate , SurveyID:$scope.survey.ID}).then(function (res) {
                        if(res && res.Status=='success'){
                            Extention.popSuccess("نظرسنجی با موفقیت تمدید شد!");
                            $uibModalInstance.close('success');
                        }else{
                            Extention.popError("مشکل در تمدید نظرسنجی ، لطفا دوباره امتحان کنید.");
                        }
                    });
                }
            },
            size: 'md'
        });

        modalInstance.result.then(function (res) {
            if (res == 'success') {
                $scope.pagingController.update();
            }
        }, function () {
        });
    }

    $scope.finishSurvey = function (SurveyID) {
        Extention.post('Survey/updateSurvey',{SurveyID:SurveyID, finishPopUp: true}).then(function (res) {
            if(res && res.Status=='success'){
                Extention.popSuccess("نظرسنجی با موفقیت پایان یافت!");
                $scope.pagingController.update();
            }else{
                Extention.popError("مشکل در تمدید نظرسنجی ، لطفا دوباره امتحان کنید.");
            }
        });
    }
    activeElement('#SSurvey','#SAllSurveys');
});