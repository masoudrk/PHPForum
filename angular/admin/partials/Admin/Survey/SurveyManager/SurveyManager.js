
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
                Extention.post('Survey/getSurveyByID', { surveyID: Data.ID}).then(function (res) {
                    if (res && res.Status == 'success') {
                    }
                });
                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
            },
            size: 'md'
        });
    }
    activeElement('#SSurvey','#SAllSurveys');
});