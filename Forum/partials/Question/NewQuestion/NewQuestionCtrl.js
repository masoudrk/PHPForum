angular.module(appName).controller('NewQuestionCtrl', function ($scope, $rootScope, $stateParams, $state, $timeout, Extention) {

	$scope.question = {};
	$scope.allTags = [];
	$scope.allSubjects = [];

	$scope.errForum ={};
	$scope.errForum.title =false;
	$scope.errForum.text =false;
	$scope.form = {};

	if($stateParams.id){
		$scope.question.ID = $stateParams.id;
		$scope.form.header = 'ویرایش سوال';
		$scope.form.submitButton = 'ویرایش سوال';

		Extention.post('getQuestionMetaEdit',{QuestionID : $scope.question.ID}).then(function (res) {
			$scope.allTags = res.AllTags;
			$scope.allSubjects = res.AllSubjects;
			$scope.question = res.Question;
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
		if(!$scope.question.QuestionText){
			$scope.errForum.text = true;
			hasError = true;
		}
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

		Extention.post('saveQuestion',$scope.question).then(function (res) {
			if(res && res.Status=='success'){
				Extention.popSuccess('سوال شما با موفقیت در انجمن ثبت شد!');
				$state.go('new_question',{},{reload:true});
			}else{
				Extention.popError('مشکل در ثبت سوال سوال ، لطفا دوباره تلاش کنید!');
			}
		});
	}
	
	$scope.fieldChanged = function (name , value) {
		$scope.errForum[name] = value == undefined || value == '';
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

	activeElement('#SQuestion','#SQuestionNew');
	fixFooter();
});