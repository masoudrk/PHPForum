/**
 * Created by -MR- on 29/05/2016.
 */

angular.module('am-charts', []).directive('serialChart', function () {
    return {
        restrict: 'ECA',
        scope:{
            chartData : '=',
            chartHeight : '@',
            chartGraphs : '=',
            enableScrollBar : '=',
            chartId : '@',
            delay : '@'
        },
        template: '<div id="{{chartId}}" style="width: 100%; height: {{chartHeight}}px;"></div>',
        controller : function ($scope,$timeout) {

            $scope.$watch('chartData',function (oval , nval) {

                if(!$scope.chartData)
                    return;

                $timeout(function () {

                    if(angular.isDefined($scope.chart)){
                        $scope.chart.dataProvider = $scope.chartData;
                        $scope.chart.validateData();

                        // $scope.chart.animateData($scope.chartData, {
                        //     duration: 1000,
                        //     complete: function () {
                        //
                        //     }
                        // });
                        return;
                    }

                    var opt= {

                        type: "serial",
                        dataProvider: $scope.chartData,

                        categoryField: "date",

                        addClassNames: true,
                        categoryAxis: {
                            parseDates: true,
                            minPeriod: "DD",
                            gridAlpha: 0.1,
                            minorGridAlpha: 0.1,
                            axisAlpha: 0,
                            minorGridEnabled: true,
                            inside: false,
                            labelFunction :function formatLabel(value, valueString, axis){
                                var date = moment(valueString);
                                var d=  date.format('jYY/jMM/jDD');
                                return persianJs(d).englishNumber().toString();
                            }
                        },
                        valueAxes: [{
                            labelFunction :function formatLabel(value, valueString, axis){
                                return persianJs(valueString).englishNumber().toString();
                            },
                            tickLength: 0,
                            axisAlpha: 0,
                            showFirstLabel: false,
                            showLastLabel: false,
                            inside: false,

                            guides: [{
                            }]

                        }],

                        graphs: $scope.chartGraphs,

                        // GRAPH
                        chartCursor: {
                            limitToGraph:"g1",
                            cursorColor:"#aaa",
                            categoryBalloonEnabled : false
                        },
                        mouseWheelZoomEnabled: true,

                    };

                    if ($scope.enableScrollBar){
                        opt.chartScrollbar= {};
                    }

                    $scope.chart = AmCharts.makeChart($scope.chartId, opt);
                    $scope.chartMaked = true;

                });
            });

        },
        // link : function ($scope) {
        //
        // }

    };
});