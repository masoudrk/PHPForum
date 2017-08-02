angular.module(appName).controller('ProfileCtrl', function ($scope, $rootScope, $stateParams, $state, $uibModal, $timeout, Extention, Upload) {

    $scope.Position = {selected: null};
    $scope.education = {selected: null};
    $scope.educationLevel = {selected: null};
    $scope.jobRecord = {selected: null};
    $scope.JobInfo = {selected: null};
    $scope.Job = {selected: null};
    $scope.Depo = {selected: null};
    $scope.AssessmentPosition = {selected: null};
    $scope.Assessment = {};
    $scope.AssessmentJobInfo = null;
    $scope.SystemExperience = [];
    $scope.SystemExperienceDef = [];
    $scope.page = {
        AllEducations: [],
        AllEducationLevels: [],
        AllJobRecords: [],
        AllPositions: [],
        Depos: [],
        AllJobs :[],
        AssessmentJobInfo: []
    };
    $scope.Positions = {items: []};
    $scope.HaveAssessment = false;

    $scope.removeItem = function (type, index) {
        if (type == 1) {
            $scope.page.AssessmentJobInfo.splice(index, 1);
        } else if (type == 2) {
            $scope.page.SystemExperience.splice(index, 1);
        }
    }

    $scope.getDepo = function () {
        if ($scope.AssessmentPosition.selected) {
            Extention.postAsync('getPositionDepos', {PositionID:$scope.AssessmentPosition.selected.ID}).then(function (res) {
                if (res && res.Status == 'success') {

                    $scope.page.Depos = res.Data;
                    if($scope.Assessment.DepoID){
                        for (var i = 0; i < $scope.page.Depos.length; i++) {
                            if ($scope.page.Depos[i].ID == $scope.Assessment.DepoID) {
                                $scope.Depo.selected = $scope.page.Depos[i];
                                break;
                            }
                        }
                    }

                }
            });
        }else {
            $scope.Depo.selected = null;
        }
    }

    $scope.updateAssessment = function (number) {
        if (number == 1) {
            if (!$scope.education.selected) {
                Extention.popWarning("رشته ی تحصیلی را انتخاب کنید");
                return;
            }
            if (!$scope.Assessment.AssessmentEducationName && $scope.education.selected.ID == '10000') {
                Extention.popWarning("رشته ی تحصیلی را وارد کنید");
                return;
            }
            if (!$scope.educationLevel.selected) {
                Extention.popWarning("مدرک تحصیلی را انتخاب کنید");
                return;
            }
            if (!$scope.jobRecord.selected) {
                Extention.popWarning("مدت سابقه کار در ارتباطات را انتخاب کنید");
                return;
            }
            if (!$scope.Assessment.BrithDate) {
                Extention.popWarning("تاریخ تولد خود را وارد کنید");
                return;
            }
            if (!$scope.AssessmentPosition.selected) {
                Extention.popWarning("ناحیه فعلی مشغول به کار را انتخاب کنید");
                return;
            }
            //TODO for when depo added we should add thease :
            //if(!$scope.Depo.selected) {Extention.popWarning("دپو را انتخاب کنید");return;}
            // if(!$scope.Depo2.selected) {Extention.popWarning("دپو 2 را انتخاب کنید");return;}
            $scope.Assessment.DepoID = ($scope.Depo.selected) ? $scope.Depo.selected.ID : null;
            // $scope.Assessment.Depo2ID =$scope.Depo2.selected.ID;
            $scope.Assessment.AssessmentEducationID = $scope.education.selected.ID;
            $scope.Assessment.AssessmentEducationLevelID = $scope.educationLevel.selected.ID;
            $scope.Assessment.JobRecordID = $scope.jobRecord.selected.ID;
            $scope.Assessment.Positions = $scope.Positions.items;
            $scope.Assessment.CurrentPositionID = $scope.AssessmentPosition.selected.ID;
            $scope.Assessment.type = number;

            Extention.post('insertOrUpdateAssessment', $scope.Assessment).then(function (res) {
                if (res && res.Status == 'success') {
                    $scope.HaveAssessment = true;
                    Extention.popSuccess("اطلاعات ذخیره شد");
                    return;
                } else {
                    console.log(res);
                }
            });
        } else if (number == 2) {
            var data = {jobs: $scope.page.AssessmentJobInfo, type: number};
            Extention.post('insertOrUpdateAssessment', data).then(function (res) {
                if (res && res.Status == 'success') {
                    Extention.popSuccess("اطلاعات ذخیره شد");
                    return;
                } else {
                    console.log(res);
                }
            });
        } else if (number == 3) {
            var data = {
                jobsExpDef: []
                , jobsExpDef2: []
                , type: number
            };

            for (var i = 0; i < $scope.page.SystemExperienceDef.length; i++) {
                if ($scope.page.SystemExperienceDef[i].SystemName
                    && $scope.page.SystemExperienceDef[i].SystemType
                    && $scope.page.SystemExperienceDef[i].TrainingTime
                    && $scope.page.SystemExperienceDef[i].SelfScore) {
                    data.jobsExpDef.push($scope.page.SystemExperienceDef[i]);
                }
            }

            for (var i = 0; i < $scope.page.SystemExperience.length; i++) {
                data.jobsExpDef2.push({
                    SystemName: $scope.page.SystemExperience[i].SystemName
                    , SystemType: $scope.page.SystemExperience[i].SystemType
                    , TrainingTime: $scope.page.SystemExperience[i].TrainingTime
                    , SelfScore: $scope.page.SystemExperience[i].SelfScore
                    , Description: $scope.page.SystemExperience[i].Description
                });
            }

            Extention.post('insertOrUpdateAssessment', data).then(function (res) {
                console.log(res);
                if (res && res.Status == 'success') {
                    Extention.popSuccess("اطلاعات ذخیره شد");
                    return;
                } else {
                }
            }, function (err) {
                console.log(err);
            });
        }
    }

    $scope.addNewJobExp = function (newJobExp) {
        if (!newJobExp.SystemName) {
            Extention.popWarning("نام سیستم را انتخاب کنید");
            return;
        }
        if (!newJobExp.SystemType) {
            Extention.popWarning("دستگاه را انتخاب کنید");
            return;
        }
        if (!newJobExp.TrainingTime) {
            Extention.popWarning("مدت دوره را انتخاب کنید");
            return;
        }
        if (!newJobExp.SelfScore) {
            Extention.popWarning("امتیازخود را انتخاب کنید");
            return;
        }
        $scope.page.SystemExperience.push(newJobExp);
        $scope.SystemExperience = {};
    }

    $scope.addNewJobInfo = function (newJobInfo) {
        if (!$scope.JobInfo.selected) {
            Extention.popWarning("ناحیه را انتخاب کنید");
            return;
        }
        if (!$scope.Job.selected) {
            Extention.popWarning("عنوان شغلی را انتخاب کنید");
            return;
        }
        if (!$scope.endDate) {
            Extention.popWarning("	تاریخ پایان را وارد کنید");
            return;
        }
        if (!$scope.startDate) {
            Extention.popWarning("تاریخ شروع را وارد کنید");
            return;
        }

        newJobInfo.StartDate = $scope.startDate;
        newJobInfo.EndDate = $scope.endDate;
        newJobInfo.OrganPositionID = $scope.JobInfo.selected.ID;
        newJobInfo.JobInfo = {selected: $scope.JobInfo.selected};

        newJobInfo.JobID = $scope.Job.selected.ID;
        newJobInfo.Job = {selected: $scope.Job.selected};

        $scope.page.AssessmentJobInfo.push(newJobInfo);
        $scope.AssessmentJobInfo = {};
        $scope.JobInfo.selected = null;
        $scope.Job.selected = null;
        $scope.startDate = null;
        $scope.endDate = null;
    }
    Extention.postAsync('getAllPositions', {}).then(function (msg) {
        var pos
        if ($scope.Position.selected) {
            pos = $scope.Position.selected;
        }
        $scope.allPositions = msg;
        $scope.Position.selected = pos;
    });

    Extention.postAsync('getAssessmentData', {}).then(function (msg) {
        console.log(msg);
        $scope.page.AllEducations = msg.Data.AllEducations;
        $scope.page.AllEducationLevels = msg.Data.AllEducationLevels;
        $scope.page.AllJobRecords = msg.Data.AllJobRecords;
        $scope.page.AllPositions = msg.Data.AllPositions;
        $scope.page.AllJobs = msg.Data.AllJobs;
        for (var i = 0; i < msg.Data.SystemExperienceDef.length; i++) {
            msg.Data.SystemExperienceDef[i].SelfScore = Number(msg.Data.SystemExperienceDef[i].SelfScore);
            msg.Data.SystemExperienceDef[i].TrainingTime = Number(msg.Data.SystemExperienceDef[i].TrainingTime);
        }

        for (var i = 0; i < msg.Data.SystemExperience.length; i++) {
            msg.Data.SystemExperience[i].SelfScore = Number(msg.Data.SystemExperience[i].SelfScore);
            msg.Data.SystemExperience[i].TrainingTime = Number(msg.Data.SystemExperience[i].TrainingTime);
        }

        $scope.page.SystemExperienceDef = msg.Data.SystemExperienceDef;
        $scope.page.SystemExperience = msg.Data.SystemExperience;

        for (var j = 0; j < msg.Data.AssessmentJobInfo.length; j++) {
            for (var i = 0; i < $scope.page.AllPositions.length; i++) {
                if ($scope.page.AllPositions[i].ID == msg.Data.AssessmentJobInfo[j].OrganPositionID) {
                    $scope.JobInfo.selected = $scope.page.AllPositions[i];
                    break;
                }
            }
            for (var i = 0; i < $scope.page.AllJobs.length; i++) {
                if ($scope.page.AllJobs[i].ID == msg.Data.AssessmentJobInfo[j].JobID) {
                    $scope.Job.selected = $scope.page.AllJobs[i];
                    break;
                }
            }
            $scope.startDate = Number(msg.Data.AssessmentJobInfo[j].StartDate);
            $scope.endDate = Number(msg.Data.AssessmentJobInfo[j].EndDate);
            $scope.addNewJobInfo(msg.Data.AssessmentJobInfo[j]);

        }
        if (msg.Data.Assessment)
            $scope.HaveAssessment = true;
        else
            return;
        $scope.Assessment.JobExperience = msg.Data.Assessment.JobExperience;
        $scope.Assessment.BrithDate = Number(msg.Data.Assessment.BrithDate);
        $scope.Assessment.AssessmentEducationName = msg.Data.Assessment.AssessmentEducationName;
        $scope.Assessment.DepoID = msg.Data.Assessment.DepoID;
        for (var i = 0; i < $scope.page.AllEducations.length; i++) {
            if ($scope.page.AllEducations[i].ID == msg.Data.Assessment.AssessmentEducationID) {
                $scope.education.selected = $scope.page.AllEducations[i];
                break;
            }
        }
        for (var i = 0; i < $scope.page.AllEducationLevels.length; i++) {
            if ($scope.page.AllEducationLevels[i].ID == msg.Data.Assessment.AssessmentEducationLevelID) {
                $scope.educationLevel.selected = $scope.page.AllEducationLevels[i];
                break;
            }
        }
        for (var i = 0; i < $scope.page.AllJobRecords.length; i++) {
            if ($scope.page.AllJobRecords[i].ID == msg.Data.Assessment.JobRecordID) {
                $scope.jobRecord.selected = $scope.page.AllJobRecords[i];
                break;
            }
        }
        for (var i = 0; i < $scope.page.AllPositions.length; i++) {
            if ($scope.page.AllPositions[i].ID == msg.Data.Assessment.CurrentPositionID) {
                $scope.AssessmentPosition.selected = $scope.page.AllPositions[i];
                break;
            }
        }
        for (var j = 0; j < msg.Data.AssessmentPositions.length; j++) {
            for (var i = 0; i < $scope.page.AllPositions.length; i++) {
                if (msg.Data.AssessmentPositions[j].OrganPositionID == $scope.page.AllPositions[i].ID) {
                    $scope.Positions.items.push($scope.page.AllPositions[i]);
                    break;
                }
            }
        }
        $scope.getDepo();
    });
    $scope.checkDate = function (BrithDate) {
        if ($scope.Assessment.BrithDate > 1380) {
            $scope.Assessment.BrithDate = 1380;
            Extention.popWarning('تاریخ شما باید عددی کمتر از سال 1380 باشد');
        } else if ($scope.Assessment.BrithDate < 1340) {
            $scope.Assessment.BrithDate = 1340;
            Extention.popWarning('تاریخ شما باید عددی بیشتر از سال 1340 باشد');
        }
    }

    $scope.onChangeAvatar = function ($files, $file) {
        if ($files.length == 0)
            return;

        $uibModal.open({
            animation: true,
            templateUrl: 'cropModal.html',
            controller: function ($scope, $uibModalInstance, file) {
                $scope.croppedImage = {};

                $scope.cancel = function () {
                    $uibModalInstance.dismiss();
                };

                if (file != null) {

                    var reader = new FileReader();
                    reader.onload = function (evt) {
                        $scope.$apply(function ($scope) {
                            $scope.myImage = evt.target.result;
                        });
                    };
                    reader.readAsDataURL(file);
                }


                $scope.changeAvatar = function () {
                    Extention.setBusy(true);

                    var dataURItoBlob = function (dataURI) {
                        var binary = atob(dataURI.split(',')[1]);
                        var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
                        var array = [];
                        for (var i = 0; i < binary.length; i++) {
                            array.push(binary.charCodeAt(i));
                        }
                        return new Blob([new Uint8Array(array)], {type: mimeString});
                    };

                    var file = dataURItoBlob($scope.croppedImage);
                    file.name = 'cropfile.png';
                    file.upload = Upload.upload({
                        url: serviceBaseURL + 'updateAvatar',
                        data: {
                            file: file
                        }
                    });

                    $scope.uploading = true;
                    Extention.popInfo('لطفا تا پایان تغییر تصویر صبر کنید.');

                    file.upload.then(function (response) {
                        $timeout(function () {
                            Extention.popSuccess('تصویر با موفقیت تغییر کرد!');
                            //$scope.myWatcher =$scope.addWatcherForFileChanges();
                            Extention.setBusy(false);
                            session.Image = response.data.Image;
                            $rootScope.user.Image = response.data.Image;
                            $state.go('profile', {}, {reload: true});
                            $uibModalInstance.dismiss();
                        });
                    }, function (response) {
                        if (response.status > 0) {
                            Extention.popError('مشکل در تغییر تصویر پروفایل');
                        } else {
                            Extention.popSuccess('تصویر با موفقیت تغییر کرد!');
                        }
                        //$scope.myWatcher =$scope.addWatcherForFileChanges();
                        Extention.setBusy(false);
                        $state.go('profile', {}, {reload: true});

                    }, function (evt) {
                        // Math.min is to fix IE which reports 200% sometimes
                        file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));

                    });
                }

            },
            size: 'md',
            resolve: {
                file: function () {
                    return $file;
                }
            }
        });
    }


    $scope.timelinePagingController = {};

    if (!$stateParams.action) {
        $scope.activeTab = 2;
    } else {
        switch ($stateParams.action) {
            case 'Timeline':
                $timeout(function () {
                    $scope.timelinePagingController.update();
                });
                $scope.activeTab = 0;
                break;
            case 'Sessions':
                $scope.activeTab = 1;
                break;
            case 'Info':
                $scope.activeTab = 2;
                break;
            case 'AddentionalInfo':
                $scope.activeTab = 3;
                break;
            case 'Assessment':
                $scope.activeTab = 4;
                break;
        }
    }

    $scope.bgColorArray = ["bg-aqua-active",
        "bg-blue-active", "bg-green-active",
        "bg-yellow-active", "bg-maroon-active", "bg-light-blue-active",
        "bg-green", "bg-orange", "bg-purple", "bg-red",
        "bg-yellow", "bg-light-blue"];

    $scope.getRandomColorClass = function (id) {
        var i = id % $scope.bgColorArray.length;
        return $scope.bgColorArray[i];
    }

    $scope.getIconClass = function (item) {
        switch (item.EventTypeID) {
            // ثبت نام
            case '-1':
                return 'fa-user-plus ipalette-bg-sun-flower';
            // <tr><td>1</td><td>به جواب شما امتیاز مثبت داد.</td></tr>
            case '1':
            // <tr><td>2</td><td>به سوال شما امتیاز مثبت داد.</td></tr>
            case '2':
                return 'fa-star ipalette-bg-sun-flower';

            // <tr><td>3</td><td>سوال شما را تایید کرد.</td></tr>
            case '3':
                return 'fa-check ipalette-bg-peter-river';

            // <tr><td>4</td><td>سوال جدید مطرح کرده.</td></tr>
            case '4':
                return 'fa-flask  ipalette-bg-pomegranate';

            // <tr><td>5</td><td>پیام جدید برای شما ارسال کرد.</td></tr>
            case '5':
                return 'fa-envelope  ipalette-bg-pumpkin';

            // <tr><td>6</td><td>سوال شما را دنبال کرد.</td></tr>
            case '6':
                return 'fa-user  ipalette-bg-emerald';

            // <tr><td>7</td><td>فعالیت شما را پیگیر می شود.</td></tr>
            case '7':
                return 'fa-eye ipalette-bg-pomegranate';

            // <tr><td>8</td><td>به سوال شما جواب داده است.</td></tr>
            case '8':
                return 'fa-hand-pointer-o ipalette-bg-alizarin';

            // <tr><td>9</td><td>جواب جدید ، برای سوال دنبال شده شما ثبت کرده است.</td></tr>
            case '9':
                return 'fa-comments ipalette-bg-amethyst';

            // <tr><td>10</td><td>جواب شما را تایید کرد.</td></tr>
            case '10':
                return 'fa-check ipalette-bg-green-sea';

            // <tr><td>11</td><td>سوال جدید برای موضوع دنبال شده شما ارسال کرد.</td></tr>
            case '11':
                return 'fa-flask ipalette-bg-orange';
            default :
                return 'fa-clone ipalette-bg-orange';
        }
    }

    $scope.getTab = function (tabId) {
        $scope.activeTab = tabId;
        var opt = {
            location: true,
            inherit: true,
            relative: $state.$current,
            notify: false
        };
        switch ($scope.activeTab) {
            case 0:
                $state.transitionTo('profile', {action: 'Timeline'}, opt);
                $scope.timelinePagingController.update();
                break;
            case 1:
                $state.transitionTo('profile', {action: 'Sessions'}, opt);
                break;
            case 2:
                $state.transitionTo('profile', {action: 'Info'}, opt);
                break;
            case 3:
                $state.transitionTo('profile', {action: 'AddentionalInfo'}, opt);
                break;
            case 4:
                $state.transitionTo('profile', {action: 'Assessment'}, opt);
                break;
        }
    }

    $scope.isEqualWithVerify = true;

    $scope.getUser = function () {

        Extention.post('getUserProfile').then(function (res) {
            $scope.curUser = res;
            $scope.Position.selected = res.Organ;
        });
    }
    $scope.getUser();

    $scope.getRandomSpan = function () {
        var i = Math.floor((Math.random() * 6) + 1);

        switch (i) {
            case 1:
                return 'danger';
            case 2:
                return 'success';
            case 3:
                return 'info';
            case 4:
                return 'warning';
            case 5:
                return 'primary';
        }
    }

    $scope.saveUserInfo = function () {

        if ($scope.curUser.Password && ($scope.curUser.VerifyPassword != $scope.curUser.Password) ||
            $scope.curUser.VerifyPassword && ($scope.curUser.VerifyPassword != $scope.curUser.Password)) {
            Extention.popError('رمز وارد شده با تکرار آن یکسان نیست!');
            return;
        }

        if ($scope.curUser.Password && $scope.curUser.Password.length < 5) {
            Extention.popError(persianJs('رمز جدید بایستی حداقل 5 کاراکتر باشد!').englishNumber().toString());
            return;
        }

        $scope.curUser.OrganizationID = $scope.Position.selected.ID;
        Extention.post('saveUserInfo', $scope.curUser).then(function (res) {

            if (res && res.Status == 'success') {
                Extention.popSuccess('با موفقیت تغییر کرد!');
                session.FullName = res.FullName;
                $rootScope.user.FullName = res.FullName;
                $scope.curUser.Password = undefined;
                $scope.curUser.VerifyPassword = undefined;
                $scope.curUser.OldPassword = undefined;
            } else {
                if (res.Message == 'EmailExists') {
                    Extention.popWarning('خطا : این ایمیل قبلا ثبت شده ، لطفا ایمیل دیگری انتخاب کنید.', 12000);
                } else if (res.Message == 'OldPasswordIsNotValid') {
                    Extention.popError('خطا : رمز عبور فعلی اشتباه است.');
                } else if (res.Message == 'PasswordIsNotValid') {
                    Extention.popError(persianJs('خطا : رمز عبور جدید بایستی حداقل 5 کاراکتر باشد.').englishNumber().toString());
                } else {
                    console.log(res);
                    Extention.popError('مشکل در تغییر اطلاعات ، لطفا دوباره تلاش کنید.');
                }
            }
        });
    }

    $scope.saveUserAddintionalInfo = function () {
        Extention.post('saveUserAddintionalInfo', $scope.curUser).then(function (res) {
            if (res && res.Status == 'success') {
                Extention.popSuccess('با موفقیت تغییر کرد!');
            } else {
                Extention.popError('مشکل در تغییر اطلاعات ، لطفا دوباره تلاش کنید.');
            }
        });
    }


    $scope.passwordChanged = function () {
        if (!$scope.curUser.Password && !$scope.curUser.VerifyPassword) {
            $scope.isEqualWithVerify = true;
            return;
        }
        $scope.isEqualWithVerify = $scope.curUser.VerifyPassword == $scope.curUser.Password;
    }

    $scope.randomClass = ['danger', 'info', 'success', 'warning'];
    $scope.randomColor = function (id) {
        return $scope.randomClass[id % $scope.randomClass.length];
    }

    $scope.removeSession = function (id) {
        Extention.post('deleteSession', {ID: id}).then(function (res) {

            if (res) {
                if (res.Status == 'success') {
                    Extention.popSuccess('اتصال با موفقیت قطع شد.');
                    $scope.getUser();
                }
                else if (res.Message == 'CurrentSession') {
                    Extention.popInfo('مشکل در قطع اتصال ، شما با این شناسه متصل هستید!');
                }
                else {
                    Extention.popError('مشکل در قطع اتصال ، لطفا دوباره تلاش کنید.');
                }
            } else {
                Extention.popError('مشکل در قطع اتصال ، لطفا دوباره تلاش کنید.');
            }
        });
    }

    $scope.setAsContractor = function (userID) {
        if (userID) {
            Extention.post('changeUserContractor', {UserID: userID, Contractor: 1}).then(function (res) {
                if (res.Status == 'success') {
                    $scope.curUser.IsContractor = 1;
                    Extention.popInfo('وضعیت شما به پیمانکار تغییر کرد');
                } else {
                    Extention.popError('مشکل در تغییر وضعیت . لطفا دوباره امتحان کنید');
                }
            });
        }
    }
    $scope.setAsNotContractor = function (userID) {
        if (userID) {
            Extention.post('changeUserContractor', {UserID: userID, Contractor: 0}).then(function (res) {
                if (res.Status == 'success') {
                    $scope.curUser.IsContractor = 0;
                    Extention.popInfo('وضعیت شما به ثبت رسید');
                } else {
                    Extention.popError('مشکل در تغییر وضعیت . لطفا دوباره امتحان کنید');
                }
            });
        }
    }
    activeElement('#SProfile');
});

