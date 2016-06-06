angular.module(appName).controller('FollowCtrl', function ($scope, $rootScope, $stateParams, $state, $timeout, Extention) {

	$scope.pagingParams = { };

	$scope.pcfPerson = {};
	$scope.pcfQuestion = {};
	$scope.pcfMainSubject = {};
	$scope.pcfSubject = {};
	
	$scope.follow = {};

	$scope.getTab = function (ft) {

		var opt = {
			location: true,
			inherit: true,
			relative: $state.$current,
			notify: false
		};

		$state.transitionTo('my_following', {action: ft }, opt );
		$scope.pagingParams.FollowType = ft;
		$scope.activeTab = ft;
		$scope.updateView(ft);
	}
	
	$scope.removeFollow = function (id,vn) {

		Extention.post('deleteFollow',{FollowType : vn , ID : id}).then(function (res) {
			if(res && res.Status =='success'){
				Extention.popSuccess('با موفقیت حذف شد!');
				$scope.updateView(res.Data);
			}else{
				Extention.popError('خطا لطفا دوباره امتحان کنید.');
			}
		});
	}
	
	$scope.updateView = function (name) {

		switch (name){
			case 'Person':
				$scope.pcfPerson.update();
				break;
			case 'Question':
				$scope.pcfQuestion.update();
				break;
			case 'MainSubject':
				$scope.pcfMainSubject.update();
				break;
			case 'Subject':
				$scope.pcfSubject.update();
				break;
		}
	}

	$timeout(function () {
		if(!$stateParams.action){
			$scope.getTab('Person');
		}
		else{
			$scope.getTab($stateParams.action);
		}
	});

	activeElement('#SQuestion','#SAnswers');
}); 