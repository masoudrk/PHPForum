angular.module(appName).controller('NewAdminPostCtrl', function ($scope, $rootScope, $stateParams
    , $state, $timeout, Extention,Upload) {

	$scope.adminPost = {};
	$scope.allTags = [];
	$scope.allSubjects = [];

	$scope.errForum ={};
	$scope.errForum.title =false;
	$scope.errForum.text =false;
	$scope.form = {};

	$scope.att = {};

	if($stateParams.id){
		$scope.adminPost.ID = $stateParams.id;
		$scope.form.header = 'ویرایش مطلب ادمین';
		$scope.form.submitButton = 'ویرایش مطلب ادمین';

		Extention.post('getAdminPostMetaEdit',{AdminPostID : $scope.adminPost.ID}).then(function (res) {
			$scope.allSubjects = res.AllSubjects;
			$scope.allPostTypes = res.AllPostTypes;
			$scope.adminPost = res.AdminPost;
			$scope.AdminPostTextIN = res.AdminPost.PostText;

			$timeout(function () {
				var ms = $scope.adminPost.MainSubject;
				for(var i = 0; i<res.AllSubjects.length ; i++) {
					if(ms.SubjectID == res.AllSubjects[i].SubjectID){
						$scope.adminPost.MainSubject = res.AllSubjects[i];
						break;
					}
				}
				$scope.allChildSubjects = $scope.adminPost.MainSubject.Childs ;

				var pt = $scope.adminPost.PostType;
				for(var i = 0; i<res.AllPostTypes.length ; i++){
					if(pt.ID == res.AllPostTypes[i].ID){
						$scope.adminPost.PostType = res.AllPostTypes[i];
						break;
					}
				}
			});

		});
		$timeout(function () {
			$rootScope.$title = 'ویرایش مطلب ادمین';
		});
	}else{
		$scope.form.header = 'ایجاد مطلب جدید';
		$scope.form.submitButton = 'ارسال مطلب';

		Extention.post('getAdminPostMetaEdit').then(function (res) {
			$scope.allSubjects = res.AllSubjects;
			$scope.allPostTypes = res.AllPostTypes;
		});
	}
	
	$scope.saveAdminPost= function () {
		var hasError = false;
		if(!$scope.adminPost.Title){
			$scope.errForum.title = true;
			hasError = true;
		}
		if(!$scope.adminPost.PostText){
			$scope.errForum.text = true;
			hasError = true;
		}
		
		if(!$scope.adminPost.MainSubject){
			$scope.errForum.mainSubject = true;
			hasError = true;
		}
		if(!$scope.adminPost.Subject){
			$scope.errForum.subject = true;
			hasError = true;
		}
		if(!$scope.adminPost.PostType){
			$scope.errForum.postType = true;
			hasError = true;
		}
		if(hasError){
			Extention.popError('لطفا فرم را به طور کامل پر کنید!');
			return;
		}

		if(!$scope.adminPost.ID){

			Extention.setBusy(true);

			var u = Upload.upload({
				url: serviceBaseURL + 'saveAdminPost',
				method: 'POST',
				file: $scope.att.myFiles,
				data: {formData : angular.toJson($scope.adminPost) }
			});

			u.then(function(resp) {
				// file is uploaded successfully
				Extention.setBusy(false);
				Extention.popSuccess('مطلب شما با موفقیت ارسال شد!');
				$scope.emptyForm();
			}, function(resp) {
				// handle error
				Extention.setBusy(false);
				Extention.popError('مشکل در ثبت مطلب ، لطفا دوباره تلاش کنید!');
			});
		}else{
			Extention.post('editAdminPost',$scope.adminPost).then(function (res) {
				if(res){
					if(res.Status == 'success'){
						Extention.popSuccess('مطلب با موفقیت تغییر کرد.');
					} else {
						Extention.popError('خطا در ارتباط با سرور ، لطفا دوباره تلاش کنید.');
					}
				}else{
					Extention.popError('خطا در ارتباط با سرور ، لطفا دوباره تلاش کنید.');
				}
			});
		}
	}

    $scope.emptyForm = function () {
        $scope.adminPost = {};
        $scope.att.myFiles= [] ;
		$scope.AdminPostTextIN = undefined;
    }

	$scope.fieldChanged = function (name , value) {
		$scope.errForum[name] = value == undefined || value == '';
		$scope.adminPost.PostText = $scope.adminPost.PostText.replace(/¬/g, " ").replace(/&#173;/g, " ").replace(/&#8204;/g, " ");
	}

	$scope.subjectChanged = function () {
		$scope.errForum.mainSubject = false;

		$scope.allChildSubjects = $scope.adminPost.Subject = undefined;
		$timeout(function () {
			$scope.allChildSubjects = $scope.adminPost.MainSubject.Childs;
		});
	}

	$scope.childSubjectChanged = function () {
		$scope.errForum.subject = false;
	}
    

	$scope.filesChanged = function(files, file, newFiles, duplicateFiles, invalidFiles ,event) {
     	var tttt = $scope.att.myFiles;
		debugger
	}

    $scope.removeFile = function (file) {
        var index = $scope.att.myFiles.indexOf(file);
        $scope.att.myFiles.splice(index,1);
    }
	activeElement('#SAdminPost','#SNewAdminPost');
});