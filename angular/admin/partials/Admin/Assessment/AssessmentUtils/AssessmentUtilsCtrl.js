angular.module(appName).controller('AssessmentUtilsCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

    $scope.actionEducationLevel = 'افزودن';
    $scope.pagingParamsEducationLevel = {};
    $scope.pagingControllerEducationLevel = {};
    $scope.EducationLevel = {};

    $scope.searchEducationLevel = function () {
        $scope.pagingControllerEducationLevel.update();
    }

    $scope.updateEducationLevel = function() {
        if ($scope.EducationLevel.NameFA) {
            Extention.post('Assessment/addOrUpdateAssessmentEducationLevel', $scope.EducationLevel).then(function (res) {
                if (res && res.Status == 'success') {
                    $scope.searchEducationLevel();
                    if($scope.actionEducationLevel == 'افزودن')
                        Extention.popSuccess("مدرک تحصیلی با موفقیت اضافه شد!");
                    else if ($scope.actionEducationLevel == 'ویرایش') {
                        Extention.popSuccess("مدرک تحصیلی با موفقیت ویرایش شد!");
                        $scope.actionEducationLevel = 'افزودن';
                    }
                    $scope.EducationLevel = {};
                }
            });
        }else {
            Extention.popError('اطلاعات را درست وارد کنید');
        }
    }

    $scope.deleteEducationLevel = function (uid) {
        Extention.post('Assessment/deleteAssessmentEducationLevel', { ID: uid }).then(function (res) {
            if(res && res.Status=='success'){
                Extention.popSuccess("مدرک تحصیلی با موفقیت حذف شد!");
                $scope.searchEducationLevel();
            }else{
                Extention.popError("مشکل در حذف مدرک تحصیلی ، لطفا دوباره امتحان کنید.");
            }
        });
    }

    $scope.editEducationLevel = function(EducationLevel) {
        $scope.EducationLevel = EducationLevel;
        $scope.actionEducationLevel = 'ویرایش';
    }

    ////////////////////////////////////


    $scope.actionEducation = 'افزودن';
    $scope.pagingParamsEducation = {};
    $scope.pagingControllerEducation = {};
    $scope.Education = {};

    $scope.searchEducation = function () {
        $scope.pagingControllerEducation.update();
    }

    $scope.updateEducation = function() {
        if ($scope.Education.NameFA) {
            Extention.post('Assessment/addOrUpdateAssessmentEducation', $scope.Education).then(function (res) {
                if (res && res.Status == 'success') {
                    $scope.searchEducation();
                    if($scope.actionEducation == 'افزودن')
                        Extention.popSuccess("رشته تحصیلی با موفقیت اضافه شد!");
                    else if ($scope.actionEducation == 'ویرایش') {
                        Extention.popSuccess("رشته تحصیلی با موفقیت ویرایش شد!");
                        $scope.actionEducation = 'افزودن';
                    }
                    $scope.Education = {};
                }
            });
        }else {
            Extention.popError('اطلاعات را درست وارد کنید');
        }
    }

    $scope.deleteEducation = function (uid) {
        Extention.post('Assessment/deleteAssessmentEducation', { ID: uid }).then(function (res) {
            if(res && res.Status=='success'){
                Extention.popSuccess("رشته تحصیلی با موفقیت حذف شد!");
                $scope.searchEducation();
            }else{
                Extention.popError("مشکل در حذف رشته تحصیلی ، لطفا دوباره امتحان کنید.");
            }
        });
    }

    $scope.editEducation = function(Education) {
        $scope.Education = Education;
        $scope.actionEducation = 'ویرایش';
    }

     /////////////////////////////////////////



    $scope.actionSystem = 'افزودن';
    $scope.pagingParamsSystem = {};
    $scope.pagingControllerSystem = {};
    $scope.System = {};

    $scope.searchSystem = function () {
        $scope.pagingControllerSystem.update();
    }

    $scope.updateSystem = function() {
        if ($scope.System.SystemName) {
            Extention.post('Assessment/addOrUpdateAssessmentSystem', $scope.System).then(function (res) {
                if (res && res.Status == 'success') {
                    $scope.searchSystem();
                    if($scope.actionSystem == 'افزودن')
                        Extention.popSuccess("سیستم با موفقیت اضافه شد!");
                    else if ($scope.actionSystem == 'ویرایش') {
                        Extention.popSuccess("سیستم با موفقیت ویرایش شد!");
                        $scope.actionSystem = 'افزودن';
                    }
                    $scope.System = {};
                }
            });
        }else {
            Extention.popError('اطلاعات را درست وارد کنید');
        }
    }

    $scope.deleteSystem = function (uid) {
        Extention.post('Assessment/deleteAssessmentSystem', { ID: uid }).then(function (res) {
            if(res && res.Status=='success'){
                Extention.popSuccess("سیستم با موفقیت حذف شد!");
                $scope.searchSystem();
            }else{
                Extention.popError("مشکل در حذف سیستم ، لطفا دوباره امتحان کنید.");
            }
        });
    }

    $scope.editSystem = function(System) {
        $scope.System = System;
        $scope.actionSystem = 'ویرایش';
    }

    /////////////////////////////////////////



    $scope.actionJob = 'افزودن';
    $scope.pagingParamsJob = {};
    $scope.pagingControllerJob = {};
    $scope.Job = {};

    $scope.searchJob = function () {
        $scope.pagingControllerJob.update();
    }

    $scope.updateJob = function() {
        if ($scope.Job.NameFA) {
            Extention.post('Assessment/addOrUpdateAssessmentJob', $scope.Job).then(function (res) {
                if (res && res.Status == 'success') {
                    $scope.searchJob();
                    if($scope.actionJob == 'افزودن')
                        Extention.popSuccess("عنوان شغلی با موفقیت اضافه شد!");
                    else if ($scope.actionJob == 'ویرایش') {
                        Extention.popSuccess("عنوان شغلی با موفقیت ویرایش شد!");
                        $scope.actionJob = 'افزودن';
                    }
                    $scope.Job = {};
                }
            });
        }else {
            Extention.popError('اطلاعات را درست وارد کنید');
        }
    }

    $scope.deleteJob = function (uid) {
        Extention.post('Assessment/deleteAssessmentJob', { ID: uid }).then(function (res) {
            if(res && res.Status=='success'){
                Extention.popSuccess("عنوان شغلی با موفقیت حذف شد!");
                $scope.searchJob();
            }else{
                Extention.popError("مشکل در حذف عنوان شغلی ، لطفا دوباره امتحان کنید.");
            }
        });
    }

    $scope.editJob = function(Job) {
        $scope.Job = Job;
        $scope.actionJob = 'ویرایش';
    }

    activeElement('#SAssessment','#SAssessmentUtils');
});