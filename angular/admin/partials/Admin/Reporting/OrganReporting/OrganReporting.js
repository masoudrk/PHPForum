
angular.module(appName).controller('OrganReportingCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, $stateParams, $uibModal, Extention) {

    $scope.Position= {selected : null};
    $scope.StackChartData=[];
    $scope.data={};
    Extention.postAsync('getAllPositions', {}).then(function (msg) {
        $scope.allPositions = msg;
    });

    $scope.changePosition = function () {
        ($scope.Position.selected)?$scope.getChartData($scope.Position.selected.ID):$scope.getChartData(null);
    };

    $scope.stackChartOptions = [{
        "fillAlphas": 0.8,
        "lineAlpha": 0.2,
        "type": "column",
        "valueField": "QTotal",
        "labelText": "[[value]]",
        "clustered": false,
        "labelFunction": function(item) {
            return persianJs( item.values.value.toString() ).englishNumber().toString();
        },
        "balloonFunction": function(item) {
            return "<b style=\"font-size: 15px\">" +
                persianJs( item.category ).englishNumber().toString()
                + "</b><br><span class='pull-right' style='font-size=15px'> &nbsp;" +
                persianJs( item.values.value.toString() ).englishNumber().toString()
                + "</span><span style='font-size=15px'>" + 'سوال' + "</span>" ;
        }
    }, {
        "fillAlphas": 0.8,
        "lineAlpha": 0.2,
        "type": "column",
        "valueField": "ATotal",
        "labelText": "[[value]]",
        "clustered": false,
        "labelFunction": function(item) {
            return persianJs( (-item.values.value).toString() ).englishNumber().toString();
        },
        "balloonFunction": function(item) {
            return "<b style=\"font-size: 15px;\">" +
                persianJs( item.category ).englishNumber().toString()
                + "</b><br><span class='pull-right' style='font-size=15px'> &nbsp;" +
                persianJs( (-item.values.value).toString() ).englishNumber().toString()
                + "</span><span style='font-size=15px'>" + 'جواب' + "</span>" ; ;
        }
    }, {
        "fillAlphas": 1,
        "lineAlpha": 0.0,
        "type": "column",
        "valueField": "ATotalNA",
        "labelText": "[[value]]",
        "clustered": false,
        "labelFunction": function(item) {
            return persianJs( (-item.values.value).toString() ).englishNumber().toString();
        },
        "balloonFunction": function(item) {
            var s ="<b style=\"font-size: 15px\">" +
                persianJs( item.category ).englishNumber().toString()
                + "</b><br><span class='pull-right' style='font-size=15px'> &nbsp;" +
                persianJs( (-item.values.value).toString() ).englishNumber().toString()
                + "</span><span style='font-size=15px'>" + 'جواب تایید نشده' + "</span>" ;

            return s;
        }
    }, {
        "fillAlphas": 1,
        "lineAlpha": 0.0,
        "type": "column",
        "valueField": "QTotalNA",
        "labelText": "[[value]]",
        "clustered": false,
        "labelFunction": function(item) {
            return persianJs( item.values.value.toString() ).englishNumber().toString();
        },
        "balloonFunction": function(item) {
            return "<b style=\"font-size: 15px\">" +
                persianJs( item.category ).englishNumber().toString()
                + "</b><br><span class='pull-right' style='font-size=15px'> &nbsp;" +
                persianJs( item.values.value.toString() ).englishNumber().toString()
                + "</span><span style='font-size=15px'>" + 'سوال تایید نشده' + "</span>" ;
        }
    }];
    activeElement('#SReporting','#SOrganReport');

    $scope.getChartData= function (OrganizationID) {
        Extention.post('getReportChartData',{OrganizationID:OrganizationID}).then(function (res) {
            $scope.data = res;
        });
    }
    $scope.getChartData(null);
});
