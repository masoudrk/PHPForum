
angular.module(appName).controller('ProfileCtrl', function ($scope, $rootScope, $stateParams, $state, $location, $timeout, Extention,Upload) {

    $scope.timelinePagingController = {};

    if(!$stateParams.action){
        $scope.activeTab = 2;
    }else{
        switch ($stateParams.action){
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
        }
    }

    $scope.bgColorArray= ["bg-aqua-active",
        "bg-blue-active","bg-green-active",
        "bg-yellow-active","bg-maroon-active","bg-light-blue-active",
        "bg-green","bg-orange","bg-purple","bg-red",
        "bg-yellow","bg-light-blue"];

    $scope.getRandomColorClass = function(id){
        var i = id % $scope.bgColorArray.length;
        return $scope.bgColorArray[i];
    }

    $scope.getIconClass= function (item){
        switch (item.EventTypeID){
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

    $scope.getTab= function (tabId) {
        $scope.activeTab = tabId;
        var opt = {
            location: true,
            inherit: true,
            relative: $state.$current,
            notify: false
        };
        switch ($scope.activeTab){
            case 0:
                $state.transitionTo('profile', {action: 'Timeline'}, opt );
                $scope.timelinePagingController.update();
                break;
            case 1:
                $state.transitionTo('profile', {action: 'Sessions'}, opt );
                break;
            case 2:
                $state.transitionTo('profile', {action:'Info'}, opt);
                break;
            case 3:
                $state.transitionTo('profile', {action: 'AddentionalInfo'}, opt);
                break;
        }
    }

    $scope.isEqualWithVerify = true;

    $scope.getUser = function () {

        Extention.post('getUserProfile').then(function (res) {
            $scope.curUser = res;
        });
    }
    $scope.getUser();

    $scope.getRandomSpan = function(){
        var i = Math.floor((Math.random()*6)+1);

        switch (i){
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

        if($scope.curUser.Password && ($scope.curUser.VerifyPassword != $scope.curUser.Password) ||
            $scope.curUser.VerifyPassword && ($scope.curUser.VerifyPassword != $scope.curUser.Password) )
        {
            Extention.popError('رمز وارد شده با تکرار آن یکسان نیست!');
            return;
        }

        if($scope.curUser.Password.length < 5 )
        {
            Extention.popError(persianJs('رمز جدید بایستی حداقل 5 کاراکتر باشد!').englishNumber().toString() );
            return;
        }

        Extention.post('saveUserInfo', $scope.curUser).then(function (res) {

            if(res && res.Status=='success'){
                Extention.popSuccess('با موفقیت تغییر کرد!');
                session.FullName = res.FullName;
                $rootScope.user.FullName = res.FullName;
                $scope.curUser.Password=undefined;
                $scope.curUser.VerifyPassword=undefined;
                $scope.curUser.OldPassword=undefined;
            }else{
                if(res.Message == 'EmailExists'){
                    Extention.popWarning('خطا : این ایمیل قبلا ثبت شده ، لطفا ایمیل دیگری انتخاب کنید.',12000);
                }else if(res.Message == 'OldPasswordIsNotValid'){
                    Extention.popError('خطا : رمز عبور فعلی اشتباه است.');
                }else if(res.Message == 'PasswordIsNotValid'){
                    Extention.popError(persianJs('خطا : رمز عبور جدید بایستی حداقل 5 کاراکتر باشد.').englishNumber().toString());
                }else {
                    Extention.popError('مشکل در تغییر اطلاعات ، لطفا دوباره تلاش کنید.');
                }
            }
        });
    }

    $scope.saveUserAddintionalInfo = function () {
        Extention.post('saveUserAddintionalInfo', $scope.curUser).then(function (res) {
            if(res && res.Status=='success'){
                Extention.popSuccess('با موفقیت تغییر کرد!');
            }else{
                Extention.popError('مشکل در تغییر اطلاعات ، لطفا دوباره تلاش کنید.');
            }
        });
    }

    $scope.addWatcherForFileChanges = function () {

        return $scope.$watch('avatarFile.name', function () {
            if (!$scope.uploading && $scope.avatarFile) {
                $scope.changeAvatar();
            }
        }, true);
    }
    $scope.myWatcher = $scope.addWatcherForFileChanges();

    $scope.changeAvatar = function () {
        $scope.myWatcher();
        Extention.setBusy(true);

        var file = $scope.avatarFile;
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
            });
        }, function (response) {
            if (response.status > 0) {
                Extention.popError('مشکل در تغییر تصویر پروفایل');
            }else{
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

    $scope.passwordChanged = function () {
        if(!$scope.curUser.Password && !$scope.curUser.VerifyPassword)
        {
            $scope.isEqualWithVerify = true;
            return;
        }
        $scope.isEqualWithVerify = $scope.curUser.VerifyPassword == $scope.curUser.Password;
    }

    $scope.randomClass= ['danger','info','success','warning'];
    $scope.randomColor = function (id) {
        return $scope.randomClass[id % $scope.randomClass.length];
    }

    $scope.removeSession= function (id) {
        Extention.post('deleteSession',{ID : id}).then(function (res) {

            if(res){
                if (res.Status=='success') {
                    Extention.popSuccess('اتصال با موفقیت قطع شد.');
                    $scope.getUser();
                }
                else if(res.Message == 'CurrentSession'){
                    Extention.popInfo('مشکل در قطع اتصال ، شما با این شناسه متصل هستید!');
                }
                else {
                    Extention.popError('مشکل در قطع اتصال ، لطفا دوباره تلاش کنید.');
                }
            }else {
                Extention.popError('مشکل در قطع اتصال ، لطفا دوباره تلاش کنید.');
            }
        });
    }

    activeElement('#SProfile');
});