<!DOCTYPE html>
<html ng-app="adminApp" ng-controller="MainCtrl as vm" style="background-color: #ECF0F5;">
<head>
    <?php
        require_once  '../cms/functions.php';
        require_once  '../session_generator.php';

        if (!isset($_SESSION)) {
            session_start();
            if(!hasInfo()){?>
        <script>
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
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
</head>
<body class="hold-transition skin-blue sidebar-mini vazir-font">
    <div class="wrapper">

        <header class="main-header">
            <!-- Logo -->
            <a class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini">
                    <b>M</b>
                    S
                </span>
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
                            <a class="link" data-toggle="control-sidebar">
                                <i class="fa fa-gears"></i>
                            </a>
                        </li>
                        <!-- Messages: style can be found in dropdown.less-->
                        <li class="dropdown messages-menu">
                            <a class="dropdown-toggle link" data-toggle="dropdown">
                                <i class="fa fa-envelope-o"
                                    ng-class="(messages && messages.Total != '0' )?'faa-horizontal animated':''"></i>
                                <span class="label label-warning" ng-bind="messages.Total || '0'| pNumber"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">
                                    شما
                                    <span>{{messages.Total ||'0'| pNumber}}</span>
                                    پیام جدید دارید
                                </li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        <li ng-repeat="item in messages.All" ng-hide="notificationsUpdating"
                                            class="fx-bounce-normal fx-dur-50 fx-ease-none fx-stagger-50">
                                            <!-- start
                                    event -->
                                            <a>
                                                <div class="pull-right link" ui-sref="messages({id:item.ID})">
                                                    <img err-src="../images/Avatar.jpg" ng-src="{{item.Image}}"
                                                        class="img-circle"
                                                        alt="User Image"
                                                        style="border: solid 2px #f1c40f" />
                                                    <div class="text-center" style="margin: auto auto auto 10px;">
                                                        <i class="fa fa-envelope  palette-sun-flower" style="font-size: 19px;"></i>
                                                    </div>
                                                </div>
                                                <h4>
                                                    <span class="link" ui-sref="messages({id:item.ID})"
                                                        style="color:#3c8dbc;">
                                                        {{item.FullName}}
                                                    </span>
                                                    <small class="persian-rtl" style="margin-top: 2px;margin-left: 20px;">
                                                        <i class="fa fa-clock-o"></i>
                                                        {{item.MessageDate | fromNow |pNumber}}
                                                    </small>
                                                    <small class="persian-rtl">
                                                        <i class="fa fa-times link palette-alizarin faa-pulse animated-hover"
                                                            style="font-size: 16px;"
                                                            ng-click="markAsReadMessage($event, item)"></i>
                                                    </small>
                                                </h4>
                                                <h5 class="text-right link" ui-sref="messages({id:item.ID})"
                                                    style="margin-top: 0px;margin-bottom: 5px;">
                                                    {{item.EventUser|pNumber}}
                                                </h5>
                                                <p class="text-right text-info "
                                                    style="margin-top: 5px;">
                                                    <p class="link" style="font-size: 14px" ui-sref="messages({id:item.ID})">
                                                        <span class="palette-concrete">
                                                            موضوع :
                                                        </span>
                                                        {{item.MessageTitle | subString :30|pNumber}}
                                                    </p>
                                                    <p class="link" style="padding-right: 5px" ui-sref="messages({id:item.ID})">
                                                        <span class="palette-concrete">متن پیام :</span>
                                                        <span compile="item.Message | subString :100|pNumber"></span>
                                                    </p>
                                                </p>
                                            </a>
                                        </li>
                                        <!-- end event -->
                                    </ul>
                                </li>
                                <li class="footer">
                                    <a class="link faa-parent animated-hover" ng-click="updateMessages($event)"
                                        ng-class="(messagesUpdating)? 'text-muted unlink':''"
                                        style="border-bottom-left-radius: 0;border-bottom-right-radius: 0;">
                                        بروزرسانی
                                        <i
                                            class="fa fa-refresh faa-spin animated-hover"
                                            ng-class="(messagesUpdating)? 'fa-spin ':''"></i>
                                    </a>
                                    <a class="link faa-parent animated-hover" ui-sref="messages">
                                        نمایش همه پیام ها
                                        <i class="fa fa-paper-plane-o faa-burst animated-hover"></i>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Notifications: style can be found in dropdown.less -->
                        <li class="dropdown messages-menu">
                            <a class="dropdown-toggle link" data-toggle="dropdown">
                                <i class="fa fa-bell-o"
                                    ng-class="(notifications && notifications.Total != '0' )?'faa-shake animated':''"></i>
                                <span class="label label-success"
                                    ng-bind="notifications.Total | pNumber"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">
                                    شما
                                    <span>{{notifications.Total| pNumber}} </span>
                                    اعلان جدید
                                دارید!
                                </li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        <li ng-repeat="item in notifications.All"
                                            class="fx-bounce-normal fx-dur-50 fx-ease-none fx-stagger-150"
                                            ng-hide="notificationsUpdating">
                                            <!-- start
                                    event -->
                                            <a>
                                                <div class="pull-right link" ui-sref="question({id:item.QuestionID})">
                                                    <img err-src="../images/Avatar.jpg" ng-src="{{ou.FullPath}}"
                                                        class="img-circle"
                                                        alt="User Image"
                                                        style="border: solid 2px #1abc9c;margin-top: " />
                                                    <div class="text-center" style="margin: -12px -15px auto 10px;">
                                                        <i class="fa fa-bell palette-sun-flower" style="font-size: 19px;"></i>
                                                    </div>
                                                </div>
                                                <h4>
                                                    <span class="link" ui-sref="question({id:item.QuestionID})">
                                                        {{item.FullName}}
                                                    </span>
                                                    <small class="persian-rtl" style="margin-top: 2px;margin-left: 20px;">
                                                        <i class="fa fa-clock-o"></i>
                                                        {{item.EventDate | fromNow |pNumber}}
                                                    </small>
                                                    <small class="persian-rtl">
                                                        <i class="fa fa-times link palette-alizarin faa-pulse animated-hover"
                                                            style="font-size: 16px;"
                                                            ng-click="markAsReadNotification($event, item)"></i>
                                                    </small>
                                                </h4>
                                                <p class="text-right persian-rtl link"
                                                    ui-sref="question({id:item.QuestionID})"
                                                    style="margin-top: 8px;margin-bottom:5px;">
                                                    <span class="palette-turquoise"
                                                        ng-bind="item.EventTypeFA"></span>
                                                </p>
                                                <p class="text-right text-info link"
                                                    ui-sref="question({id:item.QuestionID})"
                                                    style="margin-top: 5px;" ng-if="item.HasQuestion">
                                                    <span class="persian-rtl">
                                                        سرتیتر سوال : {{item.Title | subString :100|pNumber}}
                                                    </span>
                                                </p>
                                            </a>
                                        </li>
                                        <!-- end event -->
                                    </ul>
                                </li>
                                <li class="footer">
                                    <a class="link col-xs-6 faa-parent animated-hover"
                                        ui-sref="profile({action:'Timeline'})"
                                        style="border-bottom-left-radius: 0;border-bottom-right-radius: 0;clear: initial;
                                   ">
                                        تایم لاین
                                        <i class="fa fa-clock-o faa-spin"></i>
                                    </a>
                                    <a class="link col-xs-6 faa-parent animated-hover" ng-click="updateNotifications($event)"
                                        style="border-bottom-left-radius: 0;border-bottom-right-radius: 0;clear: initial;"
                                        ng-class="(notificationsUpdating)? 'text-muted unlink':''">
                                        بروزرسانی
                                        <i class="fa fa-refresh faa-spin"
                                            ng-class="(notificationsUpdating)? 'fa-spin ':''"></i>
                                    </a>

                                    <a class="link faa-parent animated-hover" ng-click="markLastNotifications($event)">
                                        علامت زدن اعلان های نمایشی
                                        <i class="fa fa-check faa-flash animated-hover"></i>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Messages: style can be found in dropdown.less-->
                        <li class="dropdown messages-menu">
                            <a class="dropdown-toggle  faa-parent animated-hover link"
                                data-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-headphones faa-flash animated-hover"></i>
                                <span class="label palette-bg-alizarin" ng-show="socketData.OnlineUsers.length"
                                    ng-bind="socketData.OnlineUsers.length | pNumber"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">{{socketData.OnlineUsers.length|pNumber}} نفر آنلاین </li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu" style="overflow: hidden; width: 100%; height: 200px;">

                                        <li ng-repeat="ou in socketData.OnlineUsers">
                                            <!-- start message -->
                                            <a ui-sref="UserProfile({id:ou.ID})">
                                                <div class="pull-right">
                                                    <img err-src="../images/Avatar.jpg" ng-src="{{ou.Image}}"
                                                        class="img-circle"
                                                        alt="User Image"
                                                        style="border: solid 2px #3498db" />
                                                </div>
                                                <h4 class="vazir-font">
                                                    {{ou.FullName}}
                                                    <small class="persian-rtl">
                                                        <i class="fa fa-clock-o"></i>
                                                        {{ou.LastActiveTime | fromNow |pNumber}}
                                                    </small>
                                                </h4>
                                                <p style="padding-top: 5px">
                                                    <i class="fa fa-circle text-green"></i>
                                                    آنلاین
                                                </p>

                                            </a>
                                        </li>
                                        <!-- end message -->
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a class="dropdown-toggle link" data-toggle="dropdown">
                                <img ng-src="{{session.Image}}" src="../images/Avatar.jpg" class="user-image" alt="User Image" />
                                <span class="hidden-xs" ng-bind="session.FullName"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header">
                                    <img ng-src="{{session.Image}}" src="../images/Avatar.jpg" class="img-circle link"
                                        alt="User Image" />
                                    <p>
                                        <span ng-bind="session.FullName"></span>
                                        - ادمین
                                        <small class="vazir-font">
                                            عضویت در سال {{userSession.SignupDate |  jalaliDateSimple:'jYYYY'}}
                                        </small>
                                    </p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a  href="../Forum/#/Profile/Info" class="btn btn-default btn-flat">پروفایل</a>
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
                    <div class="pull-right image">
                        <img ng-src="{{session.Image}}" src="../images/Avatar.jpg" class="img-circle" alt="User Image" />
                    </div>
                    <div class="pull-left info">
                        <p ng-bind="session.FullName"></p>
                        <a>
                            آنلاین
                            <i class="fa fa-circle text-success"></i>
                        </a>
                    </div>
                </div>
                <!-- search form -->
                <!--<form action="#" method="get" class="sidebar-form">
                    <div class="input-group persian-rtl">
                        <input type="text" name="q" class="form-control vazir-font" placeholder="جستجو ..." />
                        <span class="input-group-btn">
                            <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </form>-->
                <!-- /.search form -->
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <ul class="sidebar-menu">
                    <li class="treeview" id="SDashboard">
                        <a ui-sref="dashboard">
                            <i class="fa fa-tachometer"></i>
                            <span>داشبورد</span>
                        </a>
                    </li>
                    <li ng-if="session.AdminPermissionLevel =='Base'" class="treeview" id="SUsers">
                        <a ui-sref="all_users">
                            <i class="fa fa-user"></i>
                            <span>مدیریت اعضا</span>
                            <small ng-show="UserBadge.UserCount != 0" class="label pull-right bg-orange">
                                <span ng-bind="UserBadge.UserCount | pNumber"></span>
                            </small>
                        </a>
                    </li>
                    <li ng-if="session.AdminPermissionLevel =='Base'" class="treeview" id="SAdmins">
                        <a ui-sref="all_admins">
                            <i class="fa fa-gavel"></i>
                            <span>مدیران</span>
                        </a>
                    </li>
                    <li class="treeview" id="SAdminPost">
                        <a ui-sref="admin_post">
                            <i class="fa fa-magic"></i>
                            <span>مطالب ادمین</span>
                        </a>
                    </li>
                    <li id="SAwardQuestion"  ng-if="session.AdminPermissionLevel =='Base'">
                        <a>
                            <i class="fa fa-star"></i>
                            <span>سوال های جایزه دار</span>
                            <i class="fa fa-angle-right pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li id="SAllAwardQuestion" ng-if="session.AdminPermissionLevel =='Base'">
                                <a ui-sref="award_questions">
                                    <i class="fa fa-circle-o"></i>
                                    سوال ها
                                </a>
                            </li>
                            <li id="SAddAwardQuestion" ng-if="session.AdminPermissionLevel =='Base'">
                                <a ui-sref="award_question">
                                    <i class="fa
                        fa-circle-o"></i>
                                    <span>افزودن سوال جدید</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li id="SQuestions">
                        <a>
                            <i class="fa fa-question-circle"></i>
                            <span>مدیریت سوال ها</span>
                            <i class="fa fa-angle-right pull-right"></i>
                            <small ng-show="QuestionBadge.AllQuestions != 0" class="label bg-green">
                                <span ng-bind="QuestionBadge.AllQuestions | pNumber"></span>
                            </small>
                        </a>
                        <ul class="treeview-menu">
                            <li id="SSTransition" ng-if="session.AdminPermissionLevel =='Base' || session.AdminPermission == 'Transition'">
                                <a ui-sref="questions({id:'Transition'})" href="#/MainForum/Transition">
                                    <i class="fa fa-circle-o"></i>
                                    خطوط انتقال
                                    <small ng-show="QuestionBadge.QuestionTransition != 0" class="label bg-orange pull-right">
                                        <span ng-bind="QuestionBadge.QuestionTransition | pNumber"></span>
                                    </small>
                                </a>
                            </li>
                            <li id="SSTransportManagement" ng-if="session.AdminPermissionLevel =='Base' || session.AdminPermission == 'TransportManagement'">
                                <a ui-sref="questions({id:'TransportManagement'})" href="#/MainForum/TransportManagement">
                                    <i class="fa
                        fa-circle-o"></i>
                                    <span style="font-size:13px">نظارت بر سیستم های انتقال</span>
                                    <small ng-show="QuestionBadge.QuestionTransportManagement != 0" class="label bg-orange pull-right">
                                        <span ng-bind="QuestionBadge.QuestionTransportManagement | pNumber"></span>
                                    </small>
                                </a>
                            </li>
                            <li id="SSDataSwitch" ng-if="session.AdminPermissionLevel =='Base' || session.AdminPermission == 'DataSwitch'">
                                <a ui-sref="questions({id:'DataSwitch'})" href="#/MainForum/DataSwitch">
                                    <i class="fa fa-circle-o"></i>
                                    مراکز خودکار و دیتا سوئیچ
                                    <small ng-show="QuestionBadge.QuestionDataSwitch != 0" class="label bg-orange pull-right">
                                        <span ng-bind="QuestionBadge.QuestionDataSwitch | pNumber"></span>
                                    </small>
                                </a>
                            </li>
                            <li id="SSRadio" ng-if="session.AdminPermissionLevel =='Base' || session.AdminPermission == 'Radio'">
                                <a ui-sref="questions({id:'Radio'})" href="#/MainForum/Radio">
                                    <i class="fa fa-circle-o"></i>
                                    رادیوئی
                                    <small ng-show="QuestionBadge.QuestionRadio != 0" class="label bg-orange pull-right">
                                        <span ng-bind="QuestionBadge.QuestionRadio | pNumber"></span>
                                    </small>
                                </a>
                            </li>
                            <li id="SSCommonTopics" class="active" ng-if="session.AdminPermissionLevel =='Base' || session.AdminPermission == 'CommonTopics'">
                                <a ui-sref="questions({id:'CommonTopics'})" href="#/MainForum/CommonTopics">
                                    <i class="fa fa-circle-o"></i>
                                    مباحث مشترک
                                    <small ng-show="QuestionBadge.QuestionCommonTopics != 0" class="label bg-orange pull-right">
                                        <span ng-bind="QuestionBadge.QuestionCommonTopics | pNumber"></span>
                                    </small>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li id="SAnswers">
                        <a>
                            <i class="fa fa-pencil"></i>
                            <span>مدیریت جواب ها</span>
                            <i class="fa fa-angle-right pull-right"></i>
                            <small ng-show="AnswerBadge.AllAnswers != 0" class="label bg-green">
                                <span ng-bind="AnswerBadge.AllAnswers | pNumber"></span>
                            </small>
                        </a>

                        <ul class="treeview-menu">
                            <li id="STransition" ng-if="session.AdminPermissionLevel =='Base' || session.AdminPermission == 'Transition'">
                                <a ui-sref="answers({id:'Transition'})" href="#/MainForum/Transition">
                                    <i class="fa fa-circle-o"></i>
                                    خطوط انتقال
                                    <small ng-show="AnswerBadge.AnswerTransition != 0" class="label bg-orange pull-right">
                                        <span ng-bind="AnswerBadge.AnswerTransition | pNumber"></span>
                                    </small>
                                </a>
                            </li>
                            <li id="STransportManagement" ng-if="session.AdminPermissionLevel =='Base' || session.AdminPermission == 'TransportManagement'">
                                <a ui-sref="answers({id:'TransportManagement'})" href="#/MainForum/TransportManagement">
                                    <i class="fa
                        fa-circle-o"></i>
                                    نظارت بر سیستم های انتقال
                                    <small ng-show="AnswerBadge.AnswerTransportManagement != 0" class="label bg-orange pull-right">
                                        <span ng-bind="AnswerBadge.AnswerTransportManagement | pNumber"></span>
                                    </small>
                                </a>
                            </li>
                            <li id="SDataSwitch" ng-if="session.AdminPermissionLevel =='Base' || session.AdminPermission == 'DataSwitch'">
                                <a ui-sref="answers({id:'DataSwitch'})" href="#/MainForum/DataSwitch">
                                    <i class="fa fa-circle-o"></i>
                                    مراکز خودکار و دیتا سوئیچ
                                    <small ng-show="AnswerBadge.AnswerDataSwitch != 0" class="label bg-orange pull-right">
                                        <span ng-bind="AnswerBadge.AnswerDataSwitch | pNumber"></span>
                                    </small>
                                </a>
                            </li>
                            <li id="SRadio" ng-if="session.AdminPermissionLevel =='Base' || session.AdminPermission == 'Radio'">
                                <a ui-sref="answers({id:'Radio'})" href="#/MainForum/Radio">
                                    <i class="fa fa-circle-o"></i>
                                    رادیوئی
                                    <small ng-show="AnswerBadge.AnswerRadio != 0" class="label bg-orange pull-right">
                                        <span ng-bind="AnswerBadge.AnswerRadio | pNumber"></span>
                                    </small>
                                </a>
                            </li>
                            <li id="SCommonTopics" class="active" ng-if="session.AdminPermissionLevel =='Base' || session.AdminPermission == 'CommonTopics'">
                                <a ui-sref="answers({id:'CommonTopics'})" href="#/MainForum/CommonTopics">
                                    <i class="fa fa-circle-o"></i>
                                    مباحث مشترک
                                    <small ng-show="AnswerBadge.AnswerCommonTopics != 0" class="label bg-orange pull-right">
                                        <span ng-bind="AnswerBadge.AnswerCommonTopics | pNumber"></span>
                                    </small>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!--<li id="SSubjects" class="treeview">
                        <a class="link">
                            <i class="fa fa-bookmark"></i>
                            <span>مدیریت موضوعات</span>
                        </a>
                    </li>-->
                    <li id="SReporting" ng-if="session.AdminPermissionLevel =='Base'" class="treeview">
                        <a class="link">
                            <i class="fa fa-bar-chart"></i>
                            <span>گزارش گیری</span>
                            <i class="fa fa-angle-right pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li id="SPersonReport">
                                <a ui-sref="reporting()">
                                    <i class="fa fa-circle-o"></i>
                                    گزارش فردی
                                </a>
                            </li>
                            <li id="SOrganReport">
                                <a ui-sref="organ_report">
                                    <i class="fa fa-circle-o"></i>
                                  نمودار مناطق
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li id="SPopUp" ng-if="session.AdminPermissionLevel =='Base'" class="treeview">
                        <a class="link">
                            <i class="fa fa-paper-plane-o"></i>
                            <span>مدیریت پاپ آپ</span>
                            <i class="fa fa-angle-right pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li id="SPopUps">
                                <a ui-sref="new_popup">
                                    <i class="fa fa-circle-o"></i>
                                    مدیریت پاپ آپ                                </a>
                            </li>
                            <li id="SNewPopUp">
                                <a ui-sref="new_popup">
                                    <i class="fa fa-circle-o"></i>
                                    پاپ آپ جدید
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li id="SMeta" ng-if="session.AdminPermissionLevel =='Base'" class="treeview">
                        <a class="link">
                            <i class="fa fa-tag"></i>
                            <span>مدیریت متا</span>
                            <i class="fa fa-angle-right pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li id="STag">
                                <a ui-sref="tag">
                                    <i class="fa fa-circle-o"></i>
                                    مدیریت تگ ها
                                </a>
                            </li>
                            <li id="SEducation">
                                <a ui-sref="education">
                                    <i class="fa fa-circle-o"></i>
                                    مدیریت تحصیلات
                                </a>
                            </li>
                            <li id="SSkill">
                                <a ui-sref="skill">
                                    <i class="fa fa-circle-o"></i>
                                    مدیریت مهارت ها
                                </a>
                            </li>
                            <li id="SCommonMessages">
                                <a ui-sref="common_messages">
                                    <i class="fa fa-circle-o"></i>
                                      پیام های رایج
                                </a>
                            </li>
                        </ul>
                    </li>

                   <li id="SLibrary" class="treeview">
                        <a class="link">
                            <i class="fa fa-book"></i>
                            <span>کتابخانه</span>
                            <i class="fa fa-angle-right pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li id="SUpload">
                                <a ui-sref="upload_library">
                                    <i class="fa fa-circle-o"></i>
                                      آپلود فایل
                                </a>
                            </li>
                            <li id="SFilaeManage">
                                <a ui-sref="manage_library">
                                    <i class="fa fa-circle-o"></i>
                                    مدیریت فایل ها
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a>
                            <i class="fa fa-group"></i>
                            <span>ورود به فروم </span>
                            <i class="fa fa-angle-right pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li id="SSTransition" >
                                <a href="../Forum/home.php">
                                    <i class="fa fa-circle-o"></i>
                                    خانه
                                </a>
                            </li>
                            <li id="SSTransition" >
                                <a href="../Forum/#/MainForum/Transition">
                                    <i class="fa fa-circle-o"></i>
                                    خطوط انتقال
                                </a>
                            </li>
                            <li id="SSTransportManagement" >
                                <a href="../Forum/#/MainForum/TransportManagement">
                                    <i class="fa
                        fa-circle-o"></i>
                                    <span style="font-size:13px">نظارت بر سیستم های انتقال</span>
                                </a>
                            </li>
                            <li id="SSDataSwitch">
                                <a href="../Forum/#/MainForum/DataSwitch">
                                    <i class="fa fa-circle-o"></i>
                                    مراکز خودکار و دیتا سوئیچ
                                </a>
                            </li>
                            <li id="SSRadio" >
                                <a href="../Forum/#/MainForum/Radio">
                                    <i class="fa fa-circle-o"></i>
                                    رادیوئی
                                </a>
                            </li>
                            <li id="SSCommonTopics" class="active">
                                <a href="../Forum/#/MainForum/CommonTopics">
                                    <i class="fa fa-circle-o"></i>
                                    مباحث مشترک
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="treeview" id="SContact">
                        <a ui-sref="message({id:session.UserID})">
                            <i class="fa fa-envelope"></i>
                            <span>ارسال پیام</span>
                        </a>
                    </li>
                    <li id="SProfile">
                        <a href="../Forum/#/Profile/Info">
                            <i class="fa fa-male"></i>
                            <span>پروفایل من </span>
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

        <script id="notifyModal.html" type="text/ng-template">
        <div class="modal-header">
          <h3 class="modal-title">Error in XHR request </h3>
        </div>
        <div class="modal-body">
          <data compile="data"></data>
        </div>
        </script>

        <toaster-container toaster-options="{'time-out': 10000, 'position-class': 'toast-bottom-right', 'close-button':true, 'animation-class': 'toast-bottom-right'}"></toaster-container>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            <treasure-overlay-spinner active='spinner.active' spinner-storke-width="3" spinner-size="60"></treasure-overlay-spinner>
            <!-- Content Wrapper. Contains page content -->
            <div ui-view ng-hide="globalSearchActive" id="mainContent" data-anim-speed="600"
                class="anim-in-out anim-slide-below-fade" data-anim-sync="false" style="min-height: 600px;"></div>
            <!--        <div ng-show="globalSearchActive" ng-include src="'partials/GlobalSearch.html'" id="searchContent" ></div>-->
            <!-- /.content-wrapper -->
        </div>
        <?php generateFooter(); ?>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Create the tabs -->
            <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
            </ul>
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
                    </ul>
                    <!-- /.control-sidebar-menu -->

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
                    </ul>
                    <!-- /.control-sidebar-menu -->

                </div>
                <!-- /.tab-pane -->
                <!-- Stats tab content -->
                <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
                <!-- /.tab-pane -->
                <!-- Settings tab content -->
                <div class="tab-pane" id="control-sidebar-settings-tab">
                    <form method="post">
                        <h3 class="control-sidebar-heading">General Settings</h3>
                        <div class="form-group">
                            <label class="control-sidebar-subheading">
                                Report panel usage
                                <input type="checkbox" class="pull-right" checked />
                            </label>
                            <p>
                                Some information about this general settings option
                            </p>
                        </div>
                        <!-- /.form-group -->

                        <div class="form-group">
                            <label class="control-sidebar-subheading">
                                Allow mail redirect
                                <input type="checkbox" class="pull-right" checked />
                            </label>
                            <p>
                                Other sets of options are available
                            </p>
                        </div>
                        <!-- /.form-group -->

                        <div class="form-group">
                            <label class="control-sidebar-subheading">
                                Expose author name in posts
                                <input type="checkbox" class="pull-right" checked />
                            </label>
                            <p>
                                Allow the user to show his name in blog posts
                            </p>
                        </div>
                        <!-- /.form-group -->

                        <h3 class="control-sidebar-heading">Chat Settings</h3>

                        <div class="form-group">
                            <label class="control-sidebar-subheading">
                                Show me as online
                                <input type="checkbox" class="pull-right" checked />
                            </label>
                        </div>
                        <!-- /.form-group -->

                        <div class="form-group">
                            <label class="control-sidebar-subheading">
                                Turn off notifications
                                <input type="checkbox" class="pull-right" />
                            </label>
                        </div>
                        <!-- /.form-group -->

                        <div class="form-group">
                            <label class="control-sidebar-subheading">
                                Delete chat history
                                <a href="javascript::;" class="text-red pull-right">
                                    <i class="fa fa-trash-o"></i>
                                </a>
                            </label>
                        </div>
                        <!-- /.form-group -->
                    </form>
                </div>
                <!-- /.tab-pane -->
            </div>
        </aside>
        <!-- /.control-sidebar -->
        <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

    <?php generateRequiredCMSJavaFiles() ?>
    <script src="ng-vendor.min.js"></script>
    <script src="templates.min.js"></script>
    <script src="../app/directives/auto-pagination.js"></script>

</body>
</html>
