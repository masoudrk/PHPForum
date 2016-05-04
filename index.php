<!DOCTYPE html>
<html lang="fa" ng-app="myApp">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/select.min.css" rel="stylesheet">
    
    <link href="css/hover-min.css" rel="stylesheet">
    <link href="css/imagehover.css" rel="stylesheet">
    <link href="css/treasure-overlay-spinner.css" rel="stylesheet">

    <link href="css/ADM-dateTimePicker.min.css " rel="stylesheet">
    <link href='css/textAngular.css' rel='stylesheet'>

    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/toaster.css" rel="stylesheet">
    <link href="css/angular-tooltips.css" rel="stylesheet" type="text/css" />

    <title ng-bind="($title || 'Loading ...')">Loading ...</title>
    <link rel="icon" href="{{titleIcon}}">
</head>

<body>
    <treasure-overlay-spinner active='spinner.active' >
    </treasure-overlay-spinner>

    <div ui-view></div>

    <toaster-container toaster-options="{'time-out': 10000, 'position-class': 'toast-bottom-right', 'close-button':true, 'animation-class': 'toast-bottom-right'}"></toaster-container>
</body>
<script src="js/angular.js"></script>
<script src="js/angular-route.min.js"></script>
<script src="js/angular-animate.min.js" ></script>
<script src="js/angular-ui-router.js"></script>


<script src="js/toaster.min.js"></script>
<script src="js/ui-bootstrap-tpls-1.2.5.min.js"></script>

<script src="js/lazyLoad/ocLazyLoad.min.js" type="text/javascript" ></script>
<script src="js/ng-file-upload-shim.min.js"></script>
<script src="js/ng-file-upload.min.js"></script>
<script src="js/angular-tooltips.min.js"></script>
<script src="js/select.min.js"></script>
<script src='js/bootstrap-plus.min.js'></script>

<script src="js/editor/ckeditor/ckeditor.js"></script>
<script src='js/editor/ng-ckeditor.js'></script>

<script src="js/ng-sortable.js" type="text/javascript" ></script>

<script src="js/treasure-overlay-spinner.min.js" type="text/javascript"></script>

<script src="js/hotkeys.min.js"></script>
<script src="js/angular-recaptcha.js" type="text/javascript"></script>

<script src="app/app.js"></script>
<script src="js/angular-clipboard.js"></script>
<!--<script src="app/Extention.js"></script>-->
<script src="app/authCtrl.js"></script>
<script src="app/directives/auto-pagination.js"></script>

<script type="text/javascript" src="js/moment.js"></script>
<script type="text/javascript" src="js/moment-jalaali.js"></script>
<script type="text/javascript" src="js/angular-confirm.min.js"></script>
<script type="text/javascript" src="js/ADM-dateTimePicker.min.js"></script>

<!-- jqurey libs -->
<script src="js/jquery.js" type="text/javascript"></script>

<script src="js/bootstrap-toolkit.js"></script>
</html>

