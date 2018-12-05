angular.module(appName).controller('AdminPostCtrl', function ($scope, $element, $rootScope, $uibModal,
                                                             $routeParams, $state, $location, $timeout, $stateParams, Extention ) {

    $scope.isOnline = false;
    $scope.adminPost = {};

    ($scope.getAdminPostID = function () {
        Extention.post("getAdminPostID", { AdminPostID: $stateParams.id}).then(function (res) {
            if (res.Status == 'success') {
                $scope.adminPost = res.Data;
                $scope.checkNowOnline();
            
                $rootScope.breadcrumbs = [];
                $rootScope.breadcrumbs.push({title : 'خانه' , url : 'home.php' ,icon : 'fa-home' });
                $rootScope.breadcrumbs.push({ title: res.Data.MainSubject, url: $state.href('main_forum', { id: res.Data.SubjectName }) });
                $rootScope.breadcrumbs.push({ title: res.Data.Subject, url: $state.href('forum', { id: res.Data.SubjectID }) });
                $rootScope.breadcrumbs.push({ title: Extention.subString(res.Data.Title, 22) });
            } else {
                $scope.adminPost = null;
            }
        });
    })();

    $scope.getInlineImageView = function (link) {
        $uibModal.open({
            animation: true,
            templateUrl: 'inlineImageView.html',
            controller: function ($scope, $uibModalInstance,link) {

                $scope.link = link;
                $scope.downloadAttachment = function () {
                    var absUrl = $location.absUrl();
                    var i = absUrl.indexOf('#');
                    var siteName = absUrl.substr(0,i);

                    window.open(link,'_blank');
                };

                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
            },
            size: 'lg',
            resolve: {
                link: function () {
                    return link;
                }
            }
        });
    }

    $scope.openAttachments = function (att) {
        $uibModal.open({
            animation: true,
            templateUrl: 'myModalContent.html',
            controller: function ($scope, $uibModalInstance,Attachment) {

                $scope.attachment = Attachment;
                $scope.downloadAttachment = function () {
                    var absUrl = $location.absUrl();
                    var i = absUrl.indexOf('#');
                    var siteName = absUrl.substr(0,i);

                    window.open(siteName + $scope.attachment.FullPath,'_blank');
                };

                $scope.ok = function () {
                    $uibModalInstance.close($scope.selected.item);
                };

                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
            },
            size: 'md',
            resolve: {
                Attachment: function () {
                    return att;
                }
            }
        });
    }

    $rootScope.$on("socketDataChanged", function(){
        $scope.checkNowOnline();
    });

    $scope.checkNowOnline = function () {
        if(!$scope.adminPost && !$scope.adminPost.UserID)
            return;
        var ous =  $scope.socketData.OnlineUsers;
        for (var i = 0 ; i < ous.length ; i++){
            if($scope.adminPost.UserID == ous[i].ID ){
                $scope.isOnline = true;
                return;
            }
        }
        $scope.isOnline = false;
    }


    $scope.getBoxColor = function(id) {
        id = id % 5 + 1;
        switch (id) {
            case 1:
                return 'box-success';
            case 2:
                return 'box-warning';
            case 3:
                return 'box-info';
            case 4:
                return 'box-primary';
            case 5:
                return 'box-danger';
            default:
                return 'box-success';
        }
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
    $scope.getBGColor = function(id) {
        id = id % 15 + 1;
        switch (id) {
            case 1:
                return 'bg-red-active';
            case 2:
                return 'bg-yellow-active';
            case 3:
                return 'bg-aqua-active';
            case 4:
                return 'bg-blue-active';
            case 5:
                return 'bg-light-blue-active';
            case 6:
                return 'bg-green-active';
            case 7:
                return 'bg-navy-active';
            case 8:
                return 'bg-teal-active';
            case 9:
                return 'bg-olive-active';
            case 10:
                return 'bg-lime-active';
            case 11:
                return 'bg-orange-active';
            case 12:
                return 'bg-fuchsia-active';
            case 13:
                return 'bg-purple-active';
            case 14:
                return 'bg-maroon-active';
            case 15:
                return 'bg-black-active';
            default:
                return 'bg-red-active';
        }
    }
});