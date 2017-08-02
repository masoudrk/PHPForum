angular.module(appName).controller('AssessmentUsersCtrl', function ($scope, $uibModal, $rootScope, $routeParams, Extention) {

    $scope.action = 'افزودن';
    $scope.pagingParams = {};
    $scope.pagingController = {};
    $scope.user = {};
    $scope.adminType = {selected: {}};
    $scope.forumType = {selected: {}};
    $scope.adminTypes = [];
    $scope.forumTypes = [];
    $scope.Person = {};
    $scope.selectedAdmin = {};
    $scope.Position= {selected : null};
    Extention.postAsync('getAllPositions', {}).then(function (msg) {
        $scope.allPositions = msg;
    });


    Extention.post('getAllAdminTypes', {}).then(function (res) {
        if (res && res.Status == 'success') {
            $scope.adminTypes = res.Data;
        }
    });

    Extention.post('getAllForumTypes', {}).then(function (res) {
        if (res && res.Status == 'success') {
            $scope.forumTypes = res.Data;
        }
    });
    $scope.changePosition = function () {
        ($scope.Position.selected)?$scope.pagingParams.PositionID = $scope.Position.selected.ID:$scope.pagingParams.PositionID = null;
        $scope.search();
    }
    $scope.search = function () {
        $scope.pagingController.update();
    }

    $scope.updateAdmin = function () {
        if ($scope.adminType.selected && $scope.Person.selected) {
            if ($scope.adminType.selected.Permission == 'Administrator')
                $scope.selectedAdmin["ForumID"] = ($scope.forumType.selected) ? $scope.forumType.selected.ID : null;
            else
                $scope.selectedAdmin["ForumID"] = null;
            $scope.selectedAdmin["PermissionID"] = $scope.adminType.selected.ID;
            $scope.selectedAdmin["UserID"] = $scope.Person.selected.ID;
            console.log($scope.selectedAdmin);
            Extention.post('updateAdmin', $scope.selectedAdmin).then(function (res) {
                if (res && res.Status == 'success') {
                    $scope.search();
                    if ($scope.action == 'افزودن')
                        Extention.popSuccess("مدیر با موفقیت اضافه شد!");
                    else if ($scope.action == 'ویرایش') {
                        Extention.popSuccess("مدیر با موفقیت ویرایش شد!");
                        $scope.action = 'افزودن';
                    }
                    $scope.pagingController.update();
                }
            });
        }
    }

    $scope.deleteAdmin = function (uid) {
        Extention.post('deleteAdmin', {AdminID: uid}).then(function (res) {
            if (res && res.Status == 'success') {
                Extention.popSuccess("کاربر با موفقیت حذف شد!");
                $scope.pagingController.update();
            } else {
                Extention.popError("مشکل در حذف کاربر ، لطفا دوباره امتحان کنید.");
            }
        });
    }

    $scope.editAdmin = function (admin) {
        for (var i = 0; i < $scope.adminTypes.length; i++) {
            if ($scope.adminTypes[i].ID == admin.PID) {
                $scope.adminType.selected = $scope.adminTypes[i];
                break;
            }
        }
        $scope.forumType.selected = null;
        if (admin.ForumID != null) {
            for (var i = 0; i < $scope.forumTypes.length; i++) {
                if ($scope.forumTypes[i].ID == admin.ForumID) {
                    $scope.forumType.selected = $scope.forumTypes[i];
                    break;
                }
            }
        }
        $scope.Person.selected = {ID: admin.UserID, FullName: admin.FullName};
        $scope.action = 'ویرایش';
    }

    $scope.getPersons = function (filter) {
        Extention.postAsync('getUsersByName', {filter: filter}).then(function (res) {
            if (res && res.Status == 'success') {
                $scope.users = res.Data;
            } else {
                Extention.popError("مشکل در اتصال.");
            }
        });
    }
    $scope.openUserModal = function (user) {
        $uibModal.open({
            animation: true,
            templateUrl: 'UserDetail.html',
            controller: function ($scope, $uibModalInstance) {
                $scope.user = user;
                $scope.userPrintData = [];
                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
                $scope.prepairAssessment = function () {
                    $scope.userPrintData.push(['اطلاعات کاربر']);
                    $scope.userPrintData.push(['']);
                    $scope.userPrintData.push(['نام کامل', $scope.user.FullName]);
                    $scope.userPrintData.push(['ایمیل', $scope.user.Email]);
                    $scope.userPrintData.push(['شماره موبایل', $scope.user.PhoneNumber]);
                    $scope.userPrintData.push([' شماره ثابت', $scope.user.Tel]);
                    $scope.userPrintData.push(['تاریخ عضویت', moment($scope.user.SignupDate).format('jYYYY/jMM/jDD')]);
                    $scope.userPrintData.push(['ناحیه', $scope.user.OrganizationName]);
                    $scope.userPrintData.push(['*********************************************','*********************************************'
                    ,'*********************************************','*********************************************'
                    ,'*********************************************','*********************************************']);
                    $scope.userPrintData.push(['']);
                    $scope.userPrintData.push(['اطلاعات خودارزیابی']);
                    $scope.userPrintData.push(['']);
                    $scope.userPrintData.push([' رشته تحصیلی', $scope.user.Assessment.AssessmentEducationName]);
                    $scope.userPrintData.push([' مدرک تحصیلی', $scope.user.Assessment.NameFA]);
                    $scope.userPrintData.push([' سابقه کار در ارتباطات', $scope.user.Assessment.JobRecord]);
                    $scope.userPrintData.push([' تخصص و تجارب فنی', $scope.user.Assessment.JobExperience]);
                    $scope.userPrintData.push([' سال تولد', $scope.user.Assessment.BrithDate]);
                    $scope.userPrintData.push([' ناحیه فعلی مشغول به کار', $scope.user.Assessment.OrganizationName]);
                    $scope.userPrintData.push(['دپو 1', $scope.user.Assessment.Depo1Name]);
                    $scope.userPrintData.push(['دپو 2', $scope.user.Assessment.Depo2Name]);
                    if ($scope.user.AssessmentPositions) {
                        for (var i = 0; i < $scope.user.AssessmentPositions.length; i++) {
                            $scope.userPrintData.push(['نواحی مشغول بکار' + (i + 1), $scope.user.AssessmentPositions[i].OrganizationName]);
                        }
                    }
                    $scope.userPrintData.push(['*********************************************','*********************************************'
                        ,'*********************************************','*********************************************'
                        ,'*********************************************','*********************************************']);
                    $scope.userPrintData.push(['']);
                    $scope.userPrintData.push(['اطلاعات سوابق کاری']);
                    $scope.userPrintData.push(['']);
                    $scope.userPrintData.push(['تاریخ شروع','تاریخ پایان','شرکت پیمانکار','ناحیه','عنوان شغلی']);
                    for (var i = 0; i < $scope.user.AssessmentJobInfo.length; i++) {
                        $scope.userPrintData.push([
                            moment($scope.user.AssessmentJobInfo[i].StartDate).format('jYYYY/jMM/jDD'),
                            moment($scope.user.AssessmentJobInfo[i].EndDate).format('jYYYY/jMM/jDD'),
                            $scope.user.AssessmentJobInfo[i].ContractorCompany,
                            $scope.user.AssessmentJobInfo[i].OrganizationName,
                            $scope.user.AssessmentJobInfo[i].JobTitle
                        ]);
                    }
                    $scope.userPrintData.push(['*********************************************','*********************************************'
                        ,'*********************************************','*********************************************'
                        ,'*********************************************','*********************************************']);
                    $scope.userPrintData.push(['']);
                    $scope.userPrintData.push(['تخصص در سیستم ها و تجهیزات مربوط به ارتباطات']);
                    $scope.userPrintData.push(['']);
                    $scope.userPrintData.push(['نام سیستم یا تجهیز','نوع سیستم / دستگاه','مدت دوره آموزشی (ساعت)','امتیازخود ارزیابی','توضیحات','امتیاز کارشناس']);
                    for (var i = 0; i < $scope.user.SystemExperienceDef.length; i++) {
                        $scope.userPrintData.push([
                            $scope.user.SystemExperienceDef[i].SystemName,
                            $scope.user.SystemExperienceDef[i].SystemType,
                            $scope.user.SystemExperienceDef[i].TrainingTime,
                            $scope.user.SystemExperienceDef[i].SelfScore,
                            $scope.user.SystemExperienceDef[i].Description,
                            $scope.user.SystemExperienceDef[i].Score
                        ]);
                    }
                    for (var i = 0; i < $scope.user.SystemExperience.length; i++) {
                        $scope.userPrintData.push([
                            $scope.user.SystemExperience[i].SystemName,
                            $scope.user.SystemExperience[i].SystemType,
                            $scope.user.SystemExperience[i].TrainingTime,
                            $scope.user.SystemExperience[i].SelfScore,
                            $scope.user.SystemExperience[i].Description,
                            $scope.user.SystemExperience[i].Score
                        ]);
                    }
                }
                $scope.prepairAssessment();
            },
            size: 'lg'
        });
    }

    activeElement('#SReporting', '#SAssessmentUsers');
});