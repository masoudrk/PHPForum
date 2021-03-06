
angular.module(appName).controller('NewSurveyCtrl', function ($scope, $rootScope, $stateParams
	, $state, $timeout, Extention,Upload) {
	$scope.mindate = Date.now();
	$scope.survey ={Options:[]};
	$scope.editMode = false;
    $scope.SurveyTypes =[];
    $scope.surveyType ={selected:null};
    $scope.newOption ="";

    if($stateParams.id){
        editMode=true;
        Extention.post('Survey/getSurveyByIDForEdit',{surveyID:$stateParams.id}).then(function (res) {
            if(res && res.Status=='success'){
                $scope.survey = res.Data;
                $scope.ExpireDate = new Date(Date.parse(res.Data.ExpireDate.replace('-', '/', 'g'))).getTime();
                $scope.StartDate = new Date(Date.parse(res.Data.StartDate.replace('-', '/', 'g'))).getTime();
                Extention.post('Survey/getAllSurveyTypes', { }).then(function (res) {
                    if (res && res.Status == 'success') {
                        $scope.SurveyTypes = res.Data;
                        for (var i = 0 ; i<$scope.SurveyTypes.length;i++){
                            if($scope.SurveyTypes[i].ID == $scope.survey.SurveyTypeID){
                                $scope.surveyType.selected =$scope.SurveyTypes[i];
                                break;
                            }
                        }
                    }
                });
            }else{
                Extention.popError("مشکل در دریافت اطلاعات.");
            }
        });

    }else {
        Extention.post('Survey/getAllSurveyTypes', { }).then(function (res) {
            if (res && res.Status == 'success') {
                $scope.SurveyTypes = res.Data;
            }
        });
    }

    $scope.addNewOption = function () {
    	if($scope.newOption.length <3){
            Extention.popError('متن گزینه را وارد کنید');return;
		}
        $scope.survey.Options.push({SurveyText:$scope.newOption});
        $scope.newOption ="";
    }
    $scope.removeOption = function (index) {
        $scope.survey.Options.splice(index,1);
    }
	$scope.saveSurvey= function () {
        if(!$scope.survey.toFullStart.unix){
            Extention.popError('تاریخ شروع نظرسنجی را انتخاب کنید');return;
        }
        if(!$scope.survey.toFullEnd.unix){
            Extention.popError('تاریخ اتمام نظرسنجی را انتخاب کنید');return;
        }
        if(!$scope.survey.Title){
            Extention.popError('عنوان نظرسنجی را انتخاب کنید');return;
        }
        if(!$scope.survey.Name){
            Extention.popError('اسم نظرسنجی را انتخاب کنید');return;
        }
        if(!$scope.surveyType.selected){
            Extention.popError('نوع نظرسنجی را انتخاب کنید');return;
        }
        if(!$scope.survey.Description){
            Extention.popError('متن نظرسنجی را انتخاب کنید');return;
        }
        if($scope.survey.Options.length <2){
            Extention.popError('تعداد گزینه های نظرسنجی کافی نیست');return;
        }

        $scope.survey.ExpireDate = new Date($scope.survey.toFullEnd.unix);
        $scope.survey.StartDate = new Date($scope.survey.toFullStart.unix);
        $scope.survey.SurveyTypeID = $scope.surveyType.selected.ID;

		Extention.post('Survey/updateSurvey',$scope.survey).then(function (res) {
			if(res && res.Status=='success'){
			    if($scope.editMode){
                    Extention.popSuccess("نظرسنجی با موفقیت ویرایش شد!");
                }else
                    Extention.popSuccess("نظرسنجی با موفقیت اضافه شد!");
				$state.go('survey_manager');
			}else{
				Extention.popError("مشکل در افزودن نظرسنجی ، لطفا دوباره امتحان کنید.");
			}
		});
	}

	$scope.fieldChanged = function (name , value) {
		$scope.survey.Description = $scope.survey.Description.replace(/¬/g, " ").replace(/&#173;/g, " ").replace(/&#8204;/g, " ").replace(/&#34;/g, "\"");
	}

	activeElement('#SSurvey', '#SNewSurvey');
});