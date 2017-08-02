
angular.module(appName).controller('NewPopUpCtrl', function ($scope, $rootScope, $stateParams
	, $state, $timeout, Extention,Upload) {

    $scope.mindate = Date.now();
    $scope.popUp ={};
    $scope.editMode = false;
    if($stateParams.id){
        editMode=true;
        Extention.post('getPopUpByID',{popUpID:$stateParams.id}).then(function (res) {
            if(res && res.Status=='success'){
                $scope.popUp = res.Data;
                $scope.ExpireDate = new Date(Date.parse(res.Data.ExpireDate.replace('-', '/', 'g'))).getTime();
            }else{
                Extention.popError("مشکل در دریافت اطلاعات.");
            }
        });

	}

	$scope.savePopUp= function () {
		if(!$scope.popUp.toFull.unix){
			Extention.popError('تاریخ اتمام پاپ آپ را انتخاب کنید');return;
		}
		if(!$scope.popUp.Title){
			Extention.popError('موضوع پاپ آپ را انتخاب کنید');return;
		}
		if(!$scope.popUp.ModalText){
			Extention.popError('متن پاپ آپ را انتخاب کنید');return;
		}
		$scope.popUp.ExpireDate = new Date($scope.popUp.toFull.unix);
		Extention.post('saveOrUpdatePopUp',$scope.popUp).then(function (res) {
			if(res && res.Status=='success'){
				if(editMode){
                    Extention.popSuccess("پاپ آپ با موفقیت ویرایش شد!");
				}else {
                    Extention.popSuccess("پاپ آپ با موفقیت اضافه شد!");
				}
                $state.go('popup_manager');
			}else{
				Extention.popError("مشکل در حذف جواب ، لطفا دوباره امتحان کنید.");
			}
		});
	}

	$scope.fieldChanged = function (name , value) {
		$scope.popUp.ModalText = $scope.popUp.ModalText.replace(/¬/g, " ").replace(/&#173;/g, " ").replace(/&#8204;/g, " ").replace(/&#34;/g, "\"");
	}

	activeElement('#SPopUp', '#SNewPopUp');
});