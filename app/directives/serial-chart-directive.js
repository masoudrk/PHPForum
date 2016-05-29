/**
 * Created by -MR- on 29/05/2016.
 */

angular.module('am-charts', []).directive('serialChart', function () {
    return {
        restrict: 'ECA',
        scope:{
            chartData : '=',
            chartHeight : '@'
        },
        template:
            '<div class="">'+
                '<div id="chartdiv" style="width: 100%; height: {{chartHeight}}px;"></div>'+
            '</div>',
        controller : function ($scope) {

            $scope.$watch('chartData',function (oval , nval) {

                if(!$scope.chartData)
                    return;

                AmCharts.makeChart("chartdiv", {

                    type: "serial",
                    dataProvider: $scope.chartData,

                    categoryField: "date",

                    categoryAxis: {
                        parseDates: true,
                        minPeriod: "DD",
                        gridAlpha: 0.1,
                        minorGridAlpha: 0.1,
                        axisAlpha: 0,
                        minorGridEnabled: true,
                        inside: true,
                        labelFunction :function formatLabel(value, valueString, axis){
                            var date = moment(valueString);
                            var d=  date.fromNow() + " " + date.format('jYYYY/jMM/jDD');
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

                        guides: [{
                            value: 10,
                            toValue: 20,
                            fillColor: "#00CC00",
                            inside: true,
                            fillAlpha: 0.2,
                            lineAlpha: 0
                        }]

                    }],

                    graphs: [{
                        id:"g1",
                        lineColor: "#00CC00",
                        valueField: "value",
                        fillColors: "#1d8b3a",
                        bullet: "round",
                        balloonText: "<b><span class='english-text' style='font-size:12px;'>[[value]]</span></b>",
                        balloon:{
                            drop:true
                        }
                    }],

                    chartCursor: {
                        limitToGraph:"g1",
                        cursorColor:"#00CC00"
                    },

                    mouseWheelZoomEnabled: true,

                });
            });

        },
        // link : function ($scope) {
        //
        // }

    };
});