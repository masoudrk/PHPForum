<!DOCTYPE html>
<html ng-app="forumApp" ng-controller="MainCtrl as vm" style="background-color: #ECF0F5;" >
<head>
    <?php
        require_once  '../cms/functions.php';
        require_once  '../session_generator.php';

        if (!isset($_SESSION)) {
            session_start();
            if(!hasInfo())
            {?>
                <script>
                    //debugger
                    window.location ="../";
                </script>
                <?php
            }else{
                generateSessionAsJavascriptVariable();
            }
        }
        generateMetas();
        generateRequiredCMSCssFiles();
                ?>

    <title ng-bind="($title || 'Loading ...')">Loading ...</title>

    <link rel="icon" href="../images/title.png" />
    <link rel="icon" href="{{titleIcon}}" />

</head>
<body class="hold-transition skin-blue sidebar-mini vazir-font">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>M</b>S</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg vazir-font">پنل مدیریت</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- Control Sidebar Toggle Button -->
                    <li>
                        <a class="link" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                    </li>
                    <!-- Messages: style can be found in dropdown.less-->
                    <li class="dropdown messages-menu">
                        <a class="dropdown-toggle link" data-toggle="dropdown">
                            <i class="fa fa-envelope-o"></i>
                            <span class="label label-warning" ng-bind="messages.Total || '0'| pNumber"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header"> شما <span>{{messages.Total ||'0'| pNumber}}</span> پیام جدید دارید</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <li ng-repeat="item in messages.All" ng-hide="notificationsUpdating"><!-- start
                                    event -->
                                        <a ui-sref="messages({id:item.ID})">
                                            <div class="pull-right">
                                                <img src="../images/Avatar.jpg" ng-src="{{item.Image}}"
                                                     class="img-circle"
                                                     alt="User Image"
                                                     style="border: solid 2px #f1c40f">
                                                <div class="text-center" style="margin: auto auto auto 10px;">
                                                    <i class="fa fa-envelope  palette-sun-flower" style="font-size: 19px;">
                                                    </i>
                                                </div>
                                            </div>
                                            <h4 class="vazir-font">
                                                <span style="color:#3c8dbc;">{{item.FullName}}</span>
                                                <small class="persian-rtl"><i class="fa fa-clock-o"></i>
                                                    {{item.MessageDate | fromNow |pNumber}}</small>
                                            </h4>
                                            <h5 class="text-right" style="margin-top: 0px;margin-bottom: 5px;">
                                                {{item.EventUser|pNumber}}
                                            </h5>
                                            <p class="text-right text-info"
                                               style="margin-top: 5px;">
                                                <p style="font-size: 14px">
                                                <span class="palette-concrete">موضوع :
                                                </span>
                                                {{item.MessageTitle | subString :30|pNumber}}</p>
                                                <p style="padding-right: 5px">
                                                    <span class="palette-concrete">متن پیام :</span>
                                                    {{item.Message | subString :100|pNumber}}</p>
                                            </p>
                                        </a>
                                    </li><!-- end event -->
                                </ul>
                            </li>
                            <li class="footer">
                                <a class="link" ui-sref="messages">نمایش همه</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Notifications: style can be found in dropdown.less -->
                    <li class="dropdown messages-menu">
                        <a class="dropdown-toggle link" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-success" ng-bind="notifications.Total | pNumber"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">شما <span> {{notifications.Total| pNumber}} </span> اعلان جدید
                                دارید!
                            </li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <li ng-repeat="item in notifications.All"
                                        class="fx-bounce-normal fx-dur-50 fx-ease-none fx-stagger-50"
                                        ng-hide="notificationsUpdating"><!-- start
                                    event -->
                                        <a ui-sref="question({id:item.EventID})">
                                            <div class="pull-right">
                                                <img src="../images/Avatar.jpg" ng-src="{{ou.FullPath}}"
                                                     class="img-circle"
                                                     alt="User Image"
                                                     style="border: solid 2px #1abc9c">
                                                <div class="text-center" style="margin: -12px -15px auto 10px;">
                                                    <i class="fa fa-bell palette-sun-flower" style="font-size: 19px;">
                                                    </i>
                                                </div>
                                            </div>
                                            <h4 class="vazir-font">
                                                {{item.FullName}}
                                                <small class="persian-rtl"><i class="fa fa-clock-o"></i>
                                                    {{item.EventDate | fromNow |pNumber}}</small>
                                            </h4>
                                            <p class="text-right persian-rtl"
                                               style="margin-top: 8px;margin-bottom:5px;">
                                                <span class="palette-turquoise"
                                                      ng-bind="item.EventTypeFA"></span>
                                            </p>
                                            <p class="text-right text-info" ui-sref="question({id:item.EventID})"
                                               style="margin-top: 5px;">
                                                <span style="">
                                                    سرتیتر سوال : {{item.Title | subString :100|pNumber}}
                                                </span>
                                                <span class="pull-left" dir="ltr" ng-show="item.EventScore">
                                                    <i class="fa fa-thumbs-o-up"></i>
                                                    {{item.EventScore|pNumber}}
                                                </span>
                                            </p>
                                        </a>
                                    </li><!-- end event -->
                                </ul>
                            </li>
                            <li class="footer">
                                <a class="link" ng-click="updateNotifications($event)"
                                   style="border-bottom-left-radius: 0;border-bottom-right-radius: 0;">بروزرسانی</a>
                                <a class="link" ng-click="markLastNotifications($event)">علامت زدن اعلان های نمایشی</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Messages: style can be found in dropdown.less-->
                    <li class="dropdown messages-menu">
                        <a class="dropdown-toggle link" data-toggle="dropdown" aria-expanded="true">
                            <i class="fa fa-headphones"></i>
                            <span class="label palette-bg-alizarin" ng-show="socketData.OnlineUsers.length"
                                  ng-bind="socketData.OnlineUsers.length | pNumber">
                            </span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">{{socketData.OnlineUsers.length|pNumber}} نفر آنلاین </li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu" style="overflow: hidden; width: 100%; height: 200px;">

                                    <li ng-repeat="ou in socketData.OnlineUsers"><!-- start message -->
                                        <a ui-sref="UserProfile({id:ou.ID})">
                                            <div class="pull-right">
                                                <img src="../images/Avatar.jpg" ng-src="{{ou.Image}}"
                                                     class="img-circle"
                                                     alt="User Image"
                                                     style="border: solid 2px #3498db">
                                            </div>
                                            <h4 class="vazir-font">
                                               {{ou.FullName}}
                                                <small class="persian-rtl"><i class="fa fa-clock-o"></i>
                                                    {{ou.LastActiveTime | fromNow |pNumber}}</small>
                                            </h4>
                                            <p style="padding-top: 5px"> <i class="fa fa-circle text-green"></i>
                                                آنلاین </p>

                                        </a>
                                    </li><!-- end message -->
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a class="dropdown-toggle link" data-toggle="dropdown">
                            <img ng-src="{{user.Image}}" class="user-image" alt="User Image">
                            <span class="hidden-xs" ng-bind="user.FullName"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img ng-src="{{user.Image}}" class="img-circle link" alt="User Image" ui-sref="profile">
                                <p>
                                    <span ng-bind="user.FullName |pNumber"> </span> - کاربر
                                    <small class="vazir-font"> عضویت در سال
                                        <span ng-bind="user.SignupDate |
                                        jalaliDateSimple:'jYYYY'|pNumber"></span></small>
                                </p>
                            </li>
                            <!-- Menu Body -->
                            <li class="user-body">
                                <div class="col-xs-5 text-center no-padding">
                                    <a >
                                        <small class="vazir-font">دنبال کنندگان</small>
                                    </a>
                                </div>
                                <div class="col-xs-3 text-center no-padding">
                                    <a ui-sref="messages">
                                        <small class="vazir-font">پیام ها</small>
                                    </a>
                                </div>
                                <div class="col-xs-4 text-center no-padding">
                                    <a >
                                        <small class="vazir-font">دوستان</small>
                                    </a>
                                </div>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a ui-sref="profile" class="btn btn-default btn-flat">پروفایل</a>
                                </div>
                                <div class="pull-right">
                                    <a ng-click="logout()" class="btn btn-default btn-flat">خروج</a>
                                </div>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <a ui-sref="profile" class="pull-right image">
                    <img  ng-src="{{user.Image}}" class="img-circle" alt="User Image">
                </a>
                <div class="pull-left info">
                    <p><a ui-sref="profile" ng-bind="user.FullName"></a></p>
                    <a>آنلاین <i class="fa fa-circle text-green"></i></a>
                </div>
            </div>
            <!-- search form -->
            <form class="sidebar-form" style="overflow: visible">
<!--                <div class="input-group persian-rtl">-->
<!--                    <input type="text" name="q" class="form-control vazir-font" placeholder="جستجو ...">-->
<!--              <span class="input-group-btn">-->
<!--                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>-->
<!--                </button>-->
<!--              </span>-->
<!--                </div>-->

                <div class="input-group input-group input-group-ltr">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-search" style="padding-right: 5px"></i>
                            <span class="fa fa-caret-down"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li ng-class="{'active':pagingParams.searchType==0}">
                                <a ng-click="searchTypeChanges(0)" class="link">
                                    <i class="fa fa-question-circle"></i>
                                    جستجو در انجمن
                                </a>
                            </li>
                            <li ng-class="{'active':pagingParams.searchType==1}">
                                <a ng-click="searchTypeChanges(1)" class="link">
                                    <i class="fa fa-user"></i>
                                    جستجو کاربر
                                </a>
                            </li>
                            <li ng-class="{'active':pagingParams.searchType==2}">
                                <a ng-click="searchTypeChanges(2)" class="link">
                                    <i class="fa fa-bullhorn"></i>
                                    جستجو کامل
                                </a>
                            </li>
                        </ul>
                    </div><!-- /btn-group -->
                    <input type="text" class="form-control persian-rtl" ng-model="pagingParams.searchValue"
                           placeholder="جستجو ..." ng-change="searchBoxChanged()" ng-enter="search()"
                           ng-model-options="{debounce : 100}" >
                </div>
            </form>
            <!-- /.search form -->
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">


                <li id="SForum" class="treeview">
                    <a class="link">
                        <i class="fa fa-users"></i> <span>انجمن</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                        <ul class="treeview-menu">
                            <li id="SForumHome">
                                <a ui-sref="forum_home">
                                    <i class="fa fa-circle-o"></i>
                                    خانه
                                </a>
                            </li>
                            <li id="STransition">
                                <a ui-sref="main_forum({id:'Transition'})">
                                    <i class="fa fa-circle-o"></i>
                                    خطوط انتقال
                                </a>
                            </li>
                            <li id="STransportManagement">
                                <a ui-sref="main_forum({id:'TransportManagement'})">
                                    <i class="fa
                        fa-circle-o"></i>
                                    <span style="font-size:13px">نظارت بر سیستم های انتقال</span>
                                </a>
                            </li>
                        <li id="SDataSwitch"><a ui-sref="main_forum({id:'DataSwitch'})"><i class="fa fa-circle-o"></i>
مراکز خودکار و دیتا سوئیچ</a></li>
                        <li id="SRadio"><a ui-sref="main_forum({id:'Radio'})"><i class="fa fa-circle-o"></i>
                                رادیوئی</a></li>
                        

                        <li id="SCommonTopics"><a ui-sref="main_forum({id:'CommonTopics'})"><i class="fa fa-circle-o"></i>
                                مباحث مشترک</a></li>
                    </ul>
                </li>

                <li id="SQuestion" class="treeview">
                    <a class="link">
                        <i class="fa fa-bullhorn"></i> <span>فعالیت ها</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="SQuestionNew">
                            <a ui-sref="new_question">
                                <i class="fa fa-circle-o"></i>ایجاد سوال جدید
                            </a>
                        </li>
                        <li id="SQuestions">
                            <a ui-sref="questions">
                                <i class="fa fa-circle-o"></i>سوالات شما
                            </a>
                        </li>
                        <li id="SAnswers">
                            <a ui-sref="my_answers">
                                <i class="fa fa-circle-o"></i>جواب های شما
                            </a>
                        </li>
                    </ul>
                </li>

                <li id="SQuestion" class="treeview">
                    <a class="link">
                        <i class="fa fa-graduation-cap"></i> <span>سنجش</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id
                            ="SRating">
                            <a ui-sref1="rating">
                                <i class="fa fa-circle-o"></i>نظر سنجی
                            </a>
                        </li>
                        <li id="SQuiz">
                            <a ui-sref1="quiz">
                                <i class="fa fa-circle-o"></i>آزمون
                            </a>
                        </li>
                    </ul>
                </li>
                <li id="SMessage">
                    <a ui-sref="messages">
                        <i class="fa fa-envelope"></i><span>  پیام های من </span>
                        <small class="label pull-right bg-orange">
                            <span ng-bind="messages.Total ||'0'| pNumber"></span>
                        </small>

                    </a>
                </li>
                <li id="SProfile">
                    <a ui-sref="profile">
                        <i class="fa fa-user"></i><span>  پروفایل من </span>
                        <small class="label pull-right bg-green">جدید</small>
                    </a>
                </li>
                <li ng-if="user.IsAdmin=='1'" >
                    <a href="../Admin/#/">
                        <i class="fa fa-briefcase"></i><span> پنل ادمین </span>
                    </a>
                </li>
                <li class="treeview">
                    <a class="link" ng-click="logout()">
                        <i class="fa fa-power-off"></i>
                            <span>خروج</span>
                    </a>
                </li>
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <script  id="notifyModal.html" type="text/ng-template">
        <div class="modal-header">
            <h3 class="modal-title">Error in XHR request </h3>
        </div>
        <div class="modal-body">
            <data compile="data"></data>
        </div>
    </script>

    <toaster-container toaster-options="{'time-out': 10000, 'position-class': 'toast-bottom-right', 'close-button':true, 'animation-class': 'toast-bottom-right'}"></toaster-container>

    <div class="content-wrapper" id="cont" style="min-height: 600px;">
        <treasure-overlay-spinner active='spinner.active' spinner-storke-width="3" spinner-size="60">
        </treasure-overlay-spinner>
        <!-- Content Wrapper. Contains page content -->

        <div ng-hide="globalSearchActive" ui-view id="mainContent" data-anim-speed="600"
             class="anim-in-out anim-slide-below-fade"
             data-anim-sync="true"></div>

        <div ng-show="globalSearchActive"
             ng-include src="'partials/GlobalSearch.html'"
             id="searchContent"
             class="fx-bounce-normal fx-dur-600 fx-ease-none"></div>
        <!-- /.content-wrapper -->
    </div>

    <footer class="main-footer persian-rtl">
        <div class="pull-left hidden-xs">
            <b>نسخه</b> {{'1.0.0'| pNumber}}
        </div>
            <strong class="persian-rtl"></strong>
            کلیه حقوق مادی و معنوی این سایت متعلق به اداره کل ارتباطات و علائم الکتریکی راه آهن جمهوری اسلامی ایران می باشد.
          Copyright &copy;
        2014-2015 <!--<a class="persian-rtl">طراحی شده توسط MagicCube.ir</a>-->.
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark" id="side-bar-fixer">
       
        <!-- Tab panes -->
        <div class="tab-content">
            <!-- Home tab content -->
            <div class="tab-pane" id="control-sidebar-home-tab">
                <h3 class="control-sidebar-heading">Recent Activity</h3>
                <ul class="control-sidebar-menu">
                    <li>
                        <a href="javascript::;">
                            <i class="menu-icon fa fa-birthday-cake bg-red"></i>
                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>
                                <p>Will be 23 on April 24th</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <i class="menu-icon fa fa-user bg-yellow"></i>
                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>
                                <p>New phone +1(800)555-1234</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>
                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>
                                <p>nora@example.com</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <i class="menu-icon fa fa-file-code-o bg-green"></i>
                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>
                                <p>Execution time 5 seconds</p>
                            </div>
                        </a>
                    </li>
                </ul><!-- /.control-sidebar-menu -->

                <h3 class="control-sidebar-heading">Tasks Progress</h3>
                <ul class="control-sidebar-menu">
                    <li>
                        <a href="javascript::;">
                            <h4 class="control-sidebar-subheading">
                                Custom Template Design
                                <span class="label label-danger pull-right">70%</span>
                            </h4>
                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <h4 class="control-sidebar-subheading">
                                Update Resume
                                <span class="label label-success pull-right">95%</span>
                            </h4>
                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-success" style="width: 95%"></div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <h4 class="control-sidebar-subheading">
                                Laravel Integration
                                <span class="label label-warning pull-right">50%</span>
                            </h4>
                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <h4 class="control-sidebar-subheading">
                                Back End Framework
                                <span class="label label-primary pull-right">68%</span>
                            </h4>
                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
                            </div>
                        </a>
                    </li>
                </ul><!-- /.control-sidebar-menu -->

            </div><!-- /.tab-pane -->
            <!-- Stats tab content -->
            <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div><!-- /.tab-pane -->
            <!-- Settings tab content -->
            <div class="tab-pane" id="control-sidebar-settings-tab">
                <form method="post">
                    <h3 class="control-sidebar-heading">General Settings</h3>
                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Report panel usage
                            <input type="checkbox" class="pull-right" checked>
                        </label>
                        <p>
                            Some information about this general settings option
                        </p>
                    </div><!-- /.form-group -->

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Allow mail redirect
                            <input type="checkbox" class="pull-right" checked>
                        </label>
                        <p>
                            Other sets of options are available
                        </p>
                    </div><!-- /.form-group -->

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Expose author name in posts
                            <input type="checkbox" class="pull-right" checked>
                        </label>
                        <p>
                            Allow the user to show his name in blog posts
                        </p>
                    </div><!-- /.form-group -->

                    <h3 class="control-sidebar-heading">Chat Settings</h3>

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Show me as online
                            <input type="checkbox" class="pull-right" checked>
                        </label>
                    </div><!-- /.form-group -->

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Turn off notifications
                            <input type="checkbox" class="pull-right">
                        </label>
                    </div><!-- /.form-group -->

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Delete chat history
                            <a href="javascript::;" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
                        </label>
                    </div><!-- /.form-group -->
                </form>
            </div><!-- /.tab-pane -->
        </div>
    </aside><!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div><!-- ./wrapper -->

    <?php  generateRequiredCMSJavaFiles(); ?>
    <script src="angular/forum-app.js"></script>
    <script src="partials/MainCtrl.js"></script>
    <script src="../app/directives/auto-pagination.js"></script>

</body>
</html>

