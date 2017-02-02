
angular.module(appName).controller('AdminPostCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention) {


    $scope.pagingParams = {};
	$scope.pagingController = {};
	$scope.Posts = [];

	$scope.removePost = function (uid) {
	    Extention.post('deletePost', { PostID: uid }).then(function (res) {
	        if (res && res.Status == 'success') {
	            Extention.popSuccess("مطلب با موفقیت حذف شد!");
	            $scope.pagingController.update();
	        } else {
	            Extention.popError("مشکل در حذف مطلب ، لطفا دوباره امتحان کنید.");
	        }
	    });
	}

	$scope.getTagColor = function (id) {
	    id = id % 5 + 1;
	    switch (id) {
	        case 1:
	            return 'label-success';
	        case 2:
	            return 'label-warning';
	        case 4:
	            return 'label-primary';
	        case 5:
	            return 'label-danger';
	        default:
	            return 'label-success';
	    }
	}

	activeElement('#SAdminPost');
});