angular.module(appName).controller('NewQuestionCtrl', function ($scope, $rootScope, $stateParams
    , $state, $timeout, Extention,Upload) {

	$scope.question = {};
	$scope.allTags = [];
	$scope.allSubjects = [];

	$scope.errForum ={};
	$scope.errForum.title =false;
	$scope.errForum.text =false;
	$scope.form = {};

	$scope.att = {};

	if($stateParams.id){
		$scope.question.ID = $stateParams.id;
		$scope.form.header = 'ویرایش سوال';
		$scope.form.submitButton = 'ویرایش سوال';

		Extention.post('getQuestionMetaEdit',{QuestionID : $scope.question.ID}).then(function (res) {
			$scope.allTags = res.AllTags;
			$scope.allSubjects = res.AllSubjects;
			$scope.question = res.Question;
			$scope.QuestionTextIN = res.Question.QuestionText;
			$timeout(function () {
				var ms = $scope.question.MainSubject;
				for(var i = 0; i<res.AllSubjects.length ; i++){
					if(ms.SubjectID == res.AllSubjects[i].SubjectID){
						$scope.question.MainSubject = res.AllSubjects[i];
						break;
					}
				}
				$scope.allChildSubjects = $scope.question.MainSubject.Childs ;
			})
		});
		$timeout(function () {
			$rootScope.$title = 'ویرایش سوال';
		});
	}else{
		$scope.form.header = 'ایجاد سوال جدید';
		$scope.form.submitButton = 'ارسال سوال به انجمن';

		Extention.post('getQuestionMetaEdit').then(function (res) {
			$scope.allTags = res.AllTags;
			$scope.allSubjects = res.AllSubjects;
		});
	}
	
	$scope.saveQuestion= function () {
		var hasError = false;
		if(!$scope.question.Title){
			$scope.errForum.title = true;
			hasError = true;
		}
		if(!$scope.QuestionTextIN){
			$scope.errForum.text = true;
			hasError = true;
		}
		$scope.question.QuestionText = $scope.QuestionTextIN;//.replace(/\r\n|\r|\n/g, "<br />");

		if(!$scope.question.MainSubject){
			$scope.errForum.mainSubject = true;
			hasError = true;
		}
		if(!$scope.question.Subject){
			$scope.errForum.subject = true;
			hasError = true;
		}
		if(hasError){
			Extention.popError('لطفا فرم سوال را به طور کامل پر کنید!');
			return;
		}

		if(!$scope.question.ID){

			Extention.setBusy(true);

			var u = Upload.upload({
				url: serviceBaseURL + 'saveQuestion',
				method: 'POST',
				file: $scope.att.myFiles,
				data: {formData : angular.toJson($scope.question) }
			});

			u.then(function(resp) {
				// file is uploaded successfully
				Extention.setBusy(false);
				Extention.popSuccess('سوال شما با موفقیت ارسال شد!');
				$scope.emptyForm();
			}, function(resp) {
				// handle error
				Extention.setBusy(false);
				Extention.popError('مشکل در ثبت سوال سوال ، لطفا دوباره تلاش کنید!');
			});
		}else{
			Extention.post('editQuestion',$scope.question).then(function (res) {
				if(res){
					if(res.Status == 'success'){
						Extention.popSuccess('سوال با موفقیت تغییر کرد.');
					}else if(res.Status == 'CanNotEdit'){
						Extention.popError('<b>'+'خطا در تغییر محتوای سوال!' +'<b><br>' +
							' تغییرات فقط تا زمانی که سوال در انتظار تایید است مقدور میباشد. '
							,12000);
					}else if(res.Status == 'Deleted'){
						Extention.popError('خطا ، این سوال وجود ندارد.',12000);
					}else {
						Extention.popError('خطا در ارتباط با سرور ، لطفا دوباره تلاش کنید.');
					}
				}else{
					Extention.popError('خطا در ارتباط با سرور ، لطفا دوباره تلاش کنید.');
				}
			});
		}
	}

    $scope.emptyForm = function () {
        $scope.question = {};
        $scope.att.myFiles= [] ;
		$scope.QuestionTextIN = undefined;
    }

	$scope.fieldChanged = function (name , value) {
		$scope.errForum[name] = value == undefined || value == '';
		$scope.QuestionTextIN = $scope.QuestionTextIN.replace(/¬/g, " ").replace(/&#173;/g, " ").replace(/&#8204;/g, " ");
	}

	$scope.subjectChanged = function () {
		$scope.errForum.mainSubject = false;

		$scope.allChildSubjects = $scope.question.Subject = undefined;
		$timeout(function () {
			$scope.allChildSubjects = $scope.question.MainSubject.Childs;
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
	activeElement('#SQuestion','#SQuestionNew');
});