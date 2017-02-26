
angular.module(appName).controller('SurveyCtrl', function ($scope, $rootScope, $stateParams, $state, $location, $timeout, Extention,Upload) {
    $scope.SurveyID = $stateParams.id;
    $scope.Survey = {};

$scope.getSurvey = function () {
    Extention.postAsync("getSurvey" , {SurveyID : $scope.SurveyID}).then(function (res) {
        if (res.Status == 'success') {
            $scope.Survey = res.Data;
        } else {
            $scope.Survey = {};
        }
    });
}
    $scope.getSurvey();

    $scope.checkForAnswer =function () {
        var ok = true;
        for (var i in $scope.Survey.Options){
            if($scope.Survey.Options[i].answered == 'true')
                return false;
        }
        return ok;
    }


    $scope.SelectOptione =function (SurveyID,OptionID) {
        Extention.post("selectSurveyOption" , {SurveyID : SurveyID , OptionID : OptionID}).then(function (res) {
            console.log(res);
            if (res.Status == 'success') {
                Extention.popSuccess('نظر شما ثبت شد');
                $scope.getSurvey();

            } else {
            }
        });
    }


    $scope.removeFromSelected =function (SurveyID ,OptionID) {
        Extention.post("removeSurveyOption" , {SurveyID : SurveyID , OptionID : OptionID}).then(function (res) {
            console.log(res);
            if (res.Status == 'success') {
                Extention.popInfo('نظر شما حذف شد');
                $scope.getSurvey();

            } else {
            }
        });
    }

    $scope.getProgressValueStyle = function (Count ,Total) {
        var total  = Number(Total);
        var count  = Number(Count );
        if(total == 0){
            return 0;
        }else{
            return ((count / total) *100);
        }
    }

    $scope.getProgresColor = function (ID) {
        ID = ID % 5 + 1;
        switch (ID) {
            case 1:
                return 'progress-bar-green';
            case 2:
                return 'progress-bar-danger';
            case 3:
                return 'progress-bar-info';
            case 4:
                return 'progress-bar-warning';
            case 5:
                return 'progress-bar-primary';
            default:
                return 'progress-bar-yellow';
        }
    }

    activeElement('#SSurvey', '#SSurvey'+$scope.SurveyID);
});