
angular.module(appName).controller('NewPopUpCtrl', function ($scope, $rootScope, $stateParams
	, $state, $timeout, Extention,Upload) {
	$scope.mindate = Date.now();
	$scope.popUp ={};
	$scope.editMode = false;
	$scope.savePopUp= function () {
		if(!$scope.popUp.toFull.unix){
			Extention.popError('تاریخ اتمام پاپ آپ را انتخاب کنید');
		}
		if(!$scope.popUp.Title){
			Extention.popError('موضوع پاپ آپ را انتخاب کنید');
		}
		if(!$scope.popUp.ModalText){
			Extention.popError('متن پاپ آپ را انتخاب کنید');
		}
		$scope.popUp.ExpireDate = new Date($scope.popUp.toFull.unix);
		Extention.post('saveNewPopUp',$scope.popUp).then(function (res) {
			if(res && res.Status=='success'){
				Extention.popSuccess("پاپ آپ با موفقیت اضافه شد!");
				
			}else{
				Extention.popError("مشکل در حذف جواب ، لطفا دوباره امتحان کنید.");
			}
		});
	}

	$scope.fieldChanged = function (name , value) {
		$scope.popUp.ModalText = $scope.popUp.ModalText.replace(/¬/g, " ").replace(/&#173;/g, " ").replace(/&#8204;/g, " ");
	}

	activeElement('#SPopUp', '#SNewPopUp');
});