angular.module(appName).controller('HomeCtrl', function ($scope, $element, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

	hideCMS(true);

	$scope.show = function () {
		hideCMS(false);
	}

    //$scope.forumDetailOptions = {
    //    chart: {
    //        type: 'multiBarHorizontalChart',
    //        height: 250,
    //        margin: {
    //            top: 20,
    //            right: 20,
    //            bottom: 45,
    //            left: 130
    //        },
    //        x: function (d) { return d.label; },
    //        y: function (d) { return d.value; },
    //        showControls: true,
    //        showValues: true,
    //        duration: 1500,
    //        xAxis: {
    //            showMaxMin: false
    //        },
    //        yAxis: {
    //            axisLabel: 'Values',
    //            tickFormat: function (d) {
    //                return d3.format(',.2f')(d);
    //            }
    //        }
    //    }
    //};
    //$scope.forumDetailData = [
    //    {
    //        "key": "جواب ها",
    //        "color": "#d62728",
    //        "values": [
    //            {
    //                "label": "مباحث مشترک",
    //                "value": 25.307646510375
    //            },
    //            {
    //                "label": "سیستم های انتقال",
    //                "value": 16.756779544553
    //            },
    //            {
    //                "label": "گروه خطوط ارتباطی",
    //                "value": 18.451534877007
    //            },
    //            {
    //                "label": "مراکز خودکار و دیتا سوئیچ",
    //                "value": 8.6142352811805
    //            },
    //            {
    //                "label": "گروه رادیویی",
    //                "value": 8.6142352811805
    //            },
    //            {
    //                "label": "سوپروایزری",
    //                "value": 8.6142352811805
    //            }
    //        ]
    //    },
    //    {
    //        "key": "سوال ها",
    //        "color": "#1f77b4",
    //        "values": [
    //            {
    //                "label": "مباحث مشترک",
    //                "value": 25.307646510375
    //            },
    //            {
    //                "label": "سیستم های انتقال",
    //                "value": 16.756779544553
    //            },
    //            {
    //                "label": "گروه خطوط ارتباطی",
    //                "value": 18.451534877007
    //            },
    //            {
    //                "label": "مراکز خودکار و دیتا سوئیچ",
    //                "value": 8.6142352811805
    //            },
    //            {
    //                "label": "گروه رادیویی",
    //                "value": 8.6142352811805
    //            },
    //            {
    //                "label": "سوپروایزری",
    //                "value": 8.6142352811805
    //            }
    //        ]
    //    }
    //];

    //$scope.forumOptions = {
    //    chart: {
    //        type: 'multiBarChart',
    //        height: 250,
    //        margin: {
    //            top: 20,
    //            right: 20,
    //            bottom: 45,
    //            left: 45
    //        },
    //        clipEdge: true,
    //        //staggerLabels: true,
    //        duration: 1500,
    //        stacked: true,
    //        xAxis: {
    //            axisLabel: 'تاریخ',
    //            showMaxMin: false,
    //            tickFormat: function (d) {
    //                return d3.format(',f')(d);
    //            }
    //        },
    //        yAxis: {
    //            axisLabel: 'تعداد',
    //            axisLabelDistance: -20,
    //            tickFormat: function (d) {
    //                return d3.format(',f')(d);
    //            }
    //        }
    //    }
    //};

    //$scope.forumData = generateData();
    //console.log($scope.forumData);
    ///* Random Data Generator (took from nvd3.org) */
    //function generateData() {
    //    return stream_layers(3, 50 + Math.random() * 50, .1).map(function (data, i) {
    //        return {
    //            key: 'Stream' + i,
    //            values: data
    //        };
    //    });
    //}

    ///* Inspired by Lee Byron's test data generator. */
    //function stream_layers(n, m, o) {
    //    if (arguments.length < 3) o = 0;
    //    function bump(a) {
    //        var x = 1 / (.1 + Math.random()),
    //            y = 2 * Math.random() - .5,
    //            z = 10 / (.1 + Math.random());
    //        for (var i = 0; i < m; i++) {
    //            var w = (i / m - y) * z;
    //            a[i] += x * Math.exp(-w * w);
    //        }
    //    }
    //    return d3.range(n).map(function () {
    //        var a = [], i;
    //        for (i = 0; i < m; i++) a[i] = o + o * Math.random();
    //        for (i = 0; i < 5; i++) bump(a);
    //        return a.map(stream_index);
    //    });
    //}

    ///* Another layer generator using gamma distributions. */
    //function stream_waves(n, m) {
    //    return d3.range(n).map(function (i) {
    //        return d3.range(m).map(function (j) {
    //            var x = 20 * j / m - i / 3;
    //            return 2 * x * Math.exp(-.5 * x);
    //        }).map(stream_index);
    //    });
    //}

    //function stream_index(d, i) {
    //    return { x: i, y: Math.max(0, d) };
    //}

	activeElement('#SForum','#SForumHome');
	fixFooter();
});