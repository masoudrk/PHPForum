<!DOCTYPE html>
<html lang="fa" ng-app="myApp">
<head>
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Expires" content="Sat, 20 May 2016 00:00:00 GMT">
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
    <link href="css/custom.css" rel="stylesheet">
    <link href="css/toaster.css" rel="stylesheet">
    <link href="css/menubar-styles.css" rel="stylesheet">
    <link href="css/sortable/ng-sortable.css" rel="stylesheet">

    <link href="css/angular-tooltips.css" rel="stylesheet" type="text/css" />

    <link href="css/ui-navbar.css" rel="stylesheet">
    
    <link href="css/angular-hamburger-toggle.min.css" rel="stylesheet"></link>
    
    <title ng-bind="($title || 'Loading ...')">Loading ...</title>
    <link rel="icon" href="{{titleIcon}}">
</head>

<body>
    <treasure-overlay-spinner active='spinner.active' >
    </treasure-overlay-spinner>

    <div ui-view></div>

    <toaster-container toaster-options="{'time-out': 10000, 'position-class': 'toast-bottom-right', 'close-button':true, 'animation-class': 'toast-bottom-right'}"></toaster-container>
</body>
<!--  -->

<!-- Libs -->
<!--<script src="https://www.google.com/recaptcha/api.js" async defer></script>-->
<script
  src="https://www.google.com/recaptcha/api.js?onload=vcRecaptchaApiLoaded&render=explicit&hl=en"
  async defer
></script>

<script src="Scripts/angular.js"></script>
<script src="Scripts/angular-route.min.js"></script>
<script src="Scripts/angular-animate.min.js" ></script>
<script src="Scripts/angular-ui-router.js"></script>


<script src="js/toaster.min.js"></script>
<script src="js/ui-bootstrap-tpls-1.2.5.min.js"></script>

<script src="Scripts/lazyLoad/ocLazyLoad.min.js" type="text/javascript" ></script>
<script src="js/ng-file-upload-shim.min.js"></script>
<script src="js/ng-file-upload.min.js"></script>
<script src="js/angular-tooltips.min.js"></script>
<script src="js/select.min.js"></script>
<script src='js/bootstrap-plus.min.js'></script>

<script src="js/editor/ckeditor/ckeditor.js"></script>
<script src='js/editor/ng-ckeditor.js'></script>

<script src="js/ng-sortable.js" type="text/javascript" ></script>
<script src="js/ui-navbar.js" type="text/javascript"></script>

<script src="js/treasure-overlay-spinner.min.js" type="text/javascript"></script>
<!--<script src="js/masonry.js"></script>
<script src="js/angular-masonry.js"></script>-->

<script src="js/ng-map.min.js"></script>

<script src="js/hotkeys.min.js"></script>
<script src="js/angular-recaptcha.js" type="text/javascript"></script>

<script src="js/angular-hamburger-toggle.js" type="text/javascript"></script>

<script src="app/app.js"></script>
<script src="js/angular-clipboard.js"></script>
<!--<script src="app/Extention.js"></script>-->
<script src="app/authCtrl.js"></script>
<script src="app/directives/auto-pagination.js"></script>

<script src="app/directives/directives.js"></script>
<script src="app/directives/SlideShow/SlideShow.js"></script>
<script src="app/directives/Menu/menu.js"></script>

<script type="text/javascript" src="js/moment.js"></script>
<script type="text/javascript" src="js/moment-jalaali.js"></script>
<script type="text/javascript" src="js/angular-confirm.min.js"></script>
<script type="text/javascript" src="js/ADM-dateTimePicker.min.js"></script>

<!-- jqurey libs -->
<script src="js/jquery.js" type="text/javascript"></script>

<script src="js/bootstrap-toolkit.js"></script>
<!--<script src="js/bootstrap-toolkit-1.5.0.js"></script>-->

<script src="defaultPage.js" ></script>
<script src="js/menubar.js"></script>
<!--
<link href="css/pushy.css" rel="stylesheet" />
<script src="jquery/pushy.js"></script>-->
</html>

