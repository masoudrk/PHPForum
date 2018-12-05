<?php

$app->post('/checkPopUp', function() use ($app) {
    $r = json_decode($app->request->getBody());
    $resq = $app->db->makeQuery("SELECT * FROM `pop_up` p WHERE p.ExpireDate > NOW() ORDER BY p.ID LIMIT 1");
    $res = $resq->fetch_assoc();
    echoSuccess($res);
});

$app->post('/savePerson', function() use ($app) {

    $r = json_decode($app->request->getBody());

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $postfields = array('secret'=>'6LcX8F0UAAAAAOqhK3oIV389gKXy76XktzMJN_tj',
        'response'=>$r->myRecaptchaResponse);

    $ch = curl_init( $url );

    if (FALSE === $ch)
        throw new Exception('failed to initialize');

    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $postfields);
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = array();
    $googleResponse = curl_exec( $ch );
    if(!$googleResponse){
        $response["Status"] = "error-captcha-response";
        echoResponse(201, $response);
        return;
    }else{
        $resG = json_decode($googleResponse);
        if($resG->success == FALSE){
            $response["Status"] = "error-captcha";
            echoResponse(201, $response);
            return ;
        }
    }

    require_once '../passwordHash.php';
    $password = $r->Password;
    $r->Password = passwordHash::hash($password);

    

    $resq = $app->db->makeQuery("select COUNT(*) as resp FROM user WHERE Email = '$r->Email'");
    $res = $resq->fetch_assoc();

    if ($res['resp'] > 0 )
    {
        $response["Status"] = "emailError";
        echoResponse(201, $response);
    }


    $userID = $app->db->insertToTable('user','FullName,Email,Password,Tel,SignupDate,OrganizationID'
        ,"'$r->FullName','$r->Email','$r->Password','$r->Tel',now(),'$r->OrganizationID'",true);


    $linkID = generateRandomString(30);
    $dateplus_1 = strtotime("+1 day", time());
    require_once "../Services/MailService.php";
    sendConfirmEmail($r->Email,$r->FullName,$linkID);
    $app->db->insertToTable('user_mail','UserID,LinkID,ExpireDate',"'$userID','$linkID','".date('M d, Y', $dateplus_1)."'");

    $result = [];
    if($userID){
        $result['Status'] = 'success';
    }else{
        $result['Status'] = 'error';
    }

    echoResponse(200, $result);
});

$app->post('/forgetPassword', function() use ($app)  {
    
    $data = json_decode($app->request->getBody());
    if(!isset($data->Email))
        echoError('bad request');
    $reqsql = $app->db->makeQuery("select u.FullName ,u.UserAccepted, u.ID from user as u where u.Email = '$data->Email'");
    $res = $reqsql->fetch_assoc();
    if(!$res)
        echoError('this email is not valid');

    $newPassword = generateRandomString(10);
    require_once '../passwordHash.php';
    $password = passwordHash::hash($newPassword);
    $reqsql = $app->db->updateRecord('user' , "Password ='".$password."'" , "ID = ".$res["ID"]);
    $reqsql = $app->db->updateRecord('user' , "Password ='".$password."'" , "ID = ".$res["ID"]);
    if(!$reqsql)
        echoError('failed to update password');

    require_once "../Services/MailService.php";
    sendForgetPasswordEmail($data->Email,$newPassword,$res["FullName"]);
    echoSuccess();
});



$app->post('/getAllPositions', function() use ($app)  {
    
    $resq = $app->db->makeQuery("SELECT * FROM `organ_position` WHERE 1");
    $res=[];
    while($r = $resq->fetch_assoc())
        $res[] = $r;
    echoResponse(200, $res);
});

$app->post('/checkEmail', function() use ($app)  {
    $r = json_decode($app->request->getBody());
    
    $resq = $app->db->makeQuery("select COUNT(*) as resp FROM user WHERE Email = '$r->value'");
    $res = $resq->fetch_assoc();
    if ($res['resp'] > 0 )
    {
        echoSuccess(false);
    }else
        echoSuccess(true);
});

$app->post('/checkUserName', function() use ($app)  {
    $r = json_decode($app->request->getBody());
    
    $resq = $app->db->makeQuery("select COUNT(*) as resp FROM user WHERE Username = '$r->value'");
    $res = $resq->fetch_assoc();
    if ($res['resp'] > 0 )
    {
        echoResponse(200, false);
    }else
        echoResponse(200, true);
});

$app->post('/signInUser', function() use ($app)  {
    require_once '../passwordHash.php';
    $r = json_decode($app->request->getBody());
    $response = array();
    
    $password = $r->Password;
    $email = $r->Username;

    $user = $app->db->getOneRecord("select user.ID,UserAccepted,FullName,Email,Password,Username,SignupDate,file_storage.FullPath as Image from 
    user LEFT JOIN 
    file_storage ON file_storage.ID=user.AvatarID where Email='$email'");

    if ($user != NULL && $user["UserAccepted"] == 1) {
        if(passwordHash::check_password($user['Password'],$password)){

            $sessionID = generateSessionID(100);
            $resSessionIDQ = $app->db->insertToTable('user_session',"UserID,SessionID,LoginDate,DeviceName,IP","'"
                .$user['ID']."','$sessionID',NOW(),'".getOperatingSystem()."','".getIPAddress()."'");

            $IsAdmin=false;
            $admin = $app->db->getOneRecord("select * from admin left join admin_permission on admin_permission.ID=admin.PermissionID
             where UserID=".$user['ID']);

            if($admin){
                $IsAdmin=true;
            }

            $response['Status'] = "success";
            $response['SSN'] = $sessionID;
            $response['FullName'] = $user['FullName'];
            $response['Email'] = $user['Email'];
            $response['IsAdmin'] = $IsAdmin;
            $response['UserID'] = $user['ID'];
            $response['Image'] = $user['Image'];
            $response['SignupDate'] = $user['SignupDate'];
            if($IsAdmin){
                $response['AdminID'] = $admin['ID'];
                $response['AdminPermissionLevel'] = $admin['PermissionLevel'];
                $response['AdminPermission'] = $admin['Permission'];
            }

            if (!isset($_SESSION)) {
                session_start();
            }

            $cookiePath = '/';
            $cookieTime = time() + 914748364;
            $cookieTime =(int)$cookieTime;
            setcookie("SSN", $sessionID, $cookieTime ,$cookiePath);
            setcookie("FullName", $user['FullName'],$cookieTime,$cookiePath);
            setcookie("IsAdmin", ($IsAdmin)?1:0, $cookieTime,$cookiePath);
            setcookie("Email", $email, $cookieTime,$cookiePath);
            setcookie("UserID", $user['ID'], $cookieTime,$cookiePath);
            setcookie("SignupDate", $user['SignupDate'], $cookieTime,$cookiePath);
            setcookie("Image", $user['Image'],$cookieTime,$cookiePath);

            $_SESSION['Status'] = "success";
            $_SESSION['SSN'] = $sessionID;
            $_SESSION['FullName'] = $user['FullName'];
            $_SESSION['IsAdmin'] = $IsAdmin;
            $_SESSION['Email'] = $email;
            $_SESSION['UserID'] = $user['ID'];
            $_SESSION['SignupDate'] = $user['SignupDate'];
            $_SESSION['Image'] = $user['Image'];

            if($IsAdmin){
                $_SESSION['AdminID'] = $admin['ID'];
                $_SESSION['AdminPermissionLevel'] = $admin['PermissionLevel'];
                $_SESSION['AdminPermission'] = $admin['Permission'];

                setcookie("AdminID", $admin['ID'], $cookieTime,$cookiePath);
                setcookie("AdminPermissionLevel", $admin['PermissionLevel'], $cookieTime,$cookiePath);
                setcookie("AdminPermission", $admin['Permission'],$cookieTime,$cookiePath);
            }
        } else {
            $response['Status'] = "error";
            $response['Message'] = 'Login failed. Incorrect credentials';
        }
    }else {
        $response['Status'] = "error";
        $response['Message'] = 'No such user is registered';
        if($user != NULL && $user["UserAccepted"] == 0)
            $response['Status'] = "notAccepted";
    }
    echoResponse(200, $response);
});

$app->post('/getSiteTitleIcon', function() use ($app)  {
    
    //$data = json_decode($app->request->getBody());
    $r = $app->db -> makeQuery("SELECT SiteTitleIcon FROM `global_settings` ORDER BY ID DESC LIMIT 1");
    $res = $r->fetch_assoc();
    echoResponse(200, $res);
});
$app->post('/getSiteName', function() use ($app)  {
    
    //$data = json_decode($app->request->getBody());
    $r = $app->db -> makeQuery("SELECT SiteName FROM `global_settings` ORDER BY ID DESC LIMIT 1");
    $res = $r->fetch_assoc();

    echoResponse(200, $res);
});
$app->post('/getSiteNameEN', function() use ($app)  {
    
    //$data = json_decode($app->request->getBody());
    $r = $app->db -> makeQuery("SELECT SiteNameEN as SiteName FROM `global_settings` ORDER BY ID DESC LIMIT 1");
    $res = $r->fetch_assoc();
    echoResponse(200, $res);
});


$app->get('/testEmail', function() use ($app)  {
    $subject = 'Sepantarai.com';
// message
    $message = '
                <html>
                <head>
                  <title></title>
                </head>
                <body>
<p style="direction: rtl">سلام </p><br>
                <p style="direction: rtl">پسورد جدید شما :
<br>
<br> ایمیل شما:
                </p>
                </body>
                </html>
';
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .=  'From: sepanta-domainadmin@sepantarai.com' . "\r\n" .
        'Reply-To: sepanta-domainadmin@sepantarai.com'."\r\n" .
        'X-Mailer: sepantarai.com' ;

    mail("miladbonak@gmail.com", $subject, $message, $headers);
    echoSuccess();
});
