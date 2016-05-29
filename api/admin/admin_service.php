<?php

$app->post('/getAllTags', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());
	$pr = new Pagination($data);

	$pageRes = $pr->getPage($db,"SELECT t.* , (SELECT COUNT(*) FROM tag_question as tq WHERE tq.TagID= t.ID) as UseCount
FROM tag as t  ORDER BY t.ID desc");

	echoResponse(200, $pageRes);
});

$app->post('/deleteTag', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());

	$sess = new Session();

    $resQ = $db->makeQuery("select ap.ID as val from user as u INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
where u.ID = '$sess->UserID' and ap.PermissionLevel = 'Base' limit 1");

    $sql =$resQ->fetch_assoc();
    if(!$sql)
        echoError('You don\'t have permision to do this action');

	$res = $db->deleteFromTable('tag' , "ID = $data->ID");
    $res2 = $db->deleteFromTable('tag_question' , "TagID = $data->ID");
	if($res && $res2)
		echoSuccess();
	else
		echoError("Cannot delete record.");
});

$app->post('/insertTag', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());

	$sess = new Session();

    $resQ = $db->makeQuery("select ap.ID as val from user as u INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
where u.ID = '$sess->UserID' and ap.PermissionLevel = 'Base' limit 1");

    $sql =$resQ->fetch_assoc();
    if(!$sql)
        echoError('You don\'t have permision to do this action');
    $object = (object) [
            'Text' => $data->Text
          ];

    $column_names = array( 'Text');

	$res = $db->insertIntoTable($object ,$column_names,'tag');
	if($res)
		echoSuccess();
	else
		echoError("Cannot update record.");
});


$app->post('/getAllEducations', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());
	$pr = new Pagination($data);

	$pageRes = $pr->getPage($db,"SELECT e.* , (SELECT COUNT(*) FROM user_education as ue WHERE ue.EducationID = e.ID) as UseCount
FROM education as e  ORDER BY e.ID desc");

	echoResponse(200, $pageRes);
});

$app->post('/deleteEducation', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());

	$sess = new Session();

    $resQ = $db->makeQuery("select ap.ID as val from user as u INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
where u.ID = '$sess->UserID' and ap.PermissionLevel = 'Base' limit 1");

    $sql =$resQ->fetch_assoc();
    if(!$sql)
        echoError('You don\'t have permision to do this action');

	$res = $db->deleteFromTable('education' , "ID = $data->ID");
    $res2 = $db->deleteFromTable('user_education' , "EducationID = $data->ID");
	if($res && $res2)
		echoSuccess();
	else
		echoError("Cannot delete record.");
});

$app->post('/insertEducation', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());

	$sess = new Session();

    $resQ = $db->makeQuery("select ap.ID as val from user as u INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
where u.ID = '$sess->UserID' and ap.PermissionLevel = 'Base' limit 1");

    $sql =$resQ->fetch_assoc();
    if(!$sql)
        echoError('You don\'t have permision to do this action');
    $object = (object) [
            'Name' => $data->Name
          ];

    $column_names = array( 'Name');

	$res = $db->insertIntoTable($object ,$column_names,'education');
	if($res)
		echoSuccess();
	else
		echoError("Cannot update record.");
});

$app->post('/deleteUser', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());

	$sess = new Session();
	if($sess->UserID == $data->UserID)
		echoError('You cannot delete your account in this action.');

    $resQ = $db->makeQuery("select Count(*) as val from user as u INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
where u.ID = '$sess->UserID' and ap.PermissionLevel = 'Base'");

    $sql =$resQ->fetch_assoc();
    if($sql['val'] == 0)
        echoError('You don\'t have permision to do this action');

	$res = $db->deleteFromTable('user',"ID='$data->UserID'");
	if($res)
		echoSuccess();
	else
		echoError("Cannot update record.");
});

$app->post('/changeUserAccepted', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());

	if(isset($data->State)){
		if($data->State=='1' || $data->State=='-1'){
			$sess = new Session();

			if($sess->UserID == $data->UserID)
				echoError('You cannot change your own accepted state.');

                $resQ = $db->makeQuery("select Count(*) as val from user as u INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
where u.ID = '$sess->UserID' and ap.PermissionLevel = 'Base'");

    $sql =$resQ->fetch_assoc();
    if($sql['val'] == 0)
        echoError('You don\'t have permision to do this action');

			$res = $db->updateRecord('user',"UserAccepted='$data->State'","ID='$data->UserID'");

			if($res){
                if($data->State == 1){
                    $resQ = $db->makeQuery("SELECT u.Email,u.FullName FROM user as u WHERE u.ID = '$data->UserID'");
                    $res = $resQ->fetch_assoc();
                    $subject = 'Sepantarai.com';
    $message = '
                <html>
                <head>
                  <title></title>
                </head>
                <body>
<p style="direction: rtl">���� '.$res["FullName"].'</p><br>
                <p style="direction: rtl">
���� �����? ��� �� �?�?� �?� ��??� ��� ��� . ��� �? ����?� �� ����� ����� �� ����?� ��?�.
<br>
'.$res["Email"].'
                </p>
                </body>
                </html>
';
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .=  'From: sepantarai@sepantarai.com' . "\r\n" .
        'Reply-To: '.$res["Email"]."\r\n" .
        'X-Mailer: Sepantarai.com';

    //if(in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', "::1")))
    {
        mail($res["Email"], $subject, $message, $headers);
    }
                }
                	echoSuccess();
            }
			else
				echoError("Cannot update record.");
		}else{
			echoError("Sended value:$data->State is not valid!");
		}
	}

	echoError("User state is not set!");
});

$app->post('/deleteAnswer', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());

	$sess = new Session();
                    $resQ = $db->makeQuery("select a.ID from user as u
INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
WHERE a.UserID = '$sess->UserID' and (ap.PermissionLevel = 'Base' or ap.PermissionLevel = '$data->AdminPermissionLevel')
LIMIT 1");
    $sql =$resQ->fetch_assoc();
    if(!$sql)
        echoError('You don\'t have permision to do this action');

	$res = $db->deleteFromTable('forum_answer',"ID='$data->AnswerID'");
	if($res)
    {
        $db->updateRecord('user',"score=(score-2)" , "ID = '$data->UserID'");
        echoSuccess();
    }
	else
		echoError("Cannot update record.");
});

$app->post('/changeAnswerAccepted', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());

	if(isset($data->State)){
		if($data->State=='1' || $data->State=='-1'){
			$sess = new Session();

                    $resQ = $db->makeQuery("select a.ID from user as u
INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
WHERE a.UserID = '$sess->UserID' and (ap.PermissionLevel = 'Base' or ap.PermissionLevel = '$data->AdminPermissionLevel')
LIMIT 1");

    $sql =$resQ->fetch_assoc();
    if(!$sql)
        echoError('You don\'t have permision to do this action');

			$res = $db->updateRecord('forum_answer',"AdminAccepted='$data->State'","ID='$data->AnswerID'");
			if($res){
                if($data->State == 1)
                    $db->updateRecord('user',"score=(score+2)" , "ID = '$data->UserID'");
                else if($data->State == -1)
                    $db->updateRecord('user',"score=(score-2)" , "ID = '$data->UserID'");
        echoSuccess();
            }
			else
				echoError("Cannot update record.");
		}else{
			echoError("Sended value:$data->State is not valid!");
		}
	}

	echoError("User state is not set!");
});

$app->post('/getAllAnswers', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());
	$pr = new Pagination($data);
	$sess = new Session();

	$where = "WHERE fms.SubjectName = '$data->SubjectName'";
	$hasWhere = FALSE;
    if(isset($data->answerType)){
        $where .=" AND (fa.AdminAccepted ='$data->answerType')";
	}
	if(isset($data->searchValue) && strlen($data->searchValue) > 0){
		$s = mb_convert_encoding($data->searchValue, "UTF-8", "auto");
		$where .= " AND ( fa.AnswerText LIKE '%".$s."%' OR u.FullName LIKE '%".$s."%' OR u.Email LIKE '%".$s."%')";
		$hasWhere = TRUE;
	}

	$pageRes = $pr->getPage($db,"SELECT fa.* ,u.FullName ,u.Email ,fis.FullPath ,u.ID as UserID FROM forum_answer as fa INNER JOIN forum_question as fq on fq.ID = fa.QuestionID
INNER JOIN forum_subject as fs on fs.ID = fq.SubjectID
INNER JOIN forum_main_subject as fms on fms.ID = fs.ParentSubjectID
INNER join user as u on u.ID = fa.AuthorID
LEFT JOIN file_storage as fis on fis.ID = u.AvatarID ".$where." ORDER BY fa.ID desc");

	echoResponse(200, $pageRes);
});

$app->post('/getAllQuestions', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());
	$pr = new Pagination($data);
	$sess = new Session();

	$where = "WHERE fms.SubjectName = '$data->SubjectName'";
	$hasWhere = FALSE;
    if(isset($data->answerType)){
        $where .=" AND (fq.AdminAccepted ='$data->questionType')";
	}
	if(isset($data->searchValue) && strlen($data->searchValue) > 0){
		$s = mb_convert_encoding($data->searchValue, "UTF-8", "auto");
		$where .= " AND (fq.Title LIKE '%".$s."%' OR fq.QuestionText LIKE '%".$s."%' OR u.FullName LIKE '%".$s."%' OR u.Email LIKE '%".$s."%')";
		$hasWhere = TRUE;
	}

	$pageRes = $pr->getPage($db,"SELECT fq.* ,u.FullName ,u.Email ,fis.FullPath ,u.ID as UserID
FROM forum_question as fq
INNER JOIN forum_subject as fs on fs.ID = fq.SubjectID
INNER JOIN forum_main_subject as fms on fms.ID = fs.ParentSubjectID
INNER join user as u on u.ID = fq.AuthorID
LEFT JOIN file_storage as fis on fis.ID = u.AvatarID ".$where." ORDER BY fq.ID desc");

	echoResponse(200, $pageRes);
});


$app->post('/deleteQuestion', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());

	$sess = new Session();
                    $resQ = $db->makeQuery("select a.ID from user as u
INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
WHERE a.UserID = '$sess->UserID' and (ap.PermissionLevel = 'Base' or ap.PermissionLevel = '$data->AdminPermissionLevel')
LIMIT 1");
    $sql =$resQ->fetch_assoc();
    if(!$sql)
        echoError('You don\'t have permision to do this action');

	$res = $db->deleteFromTable('forum_question',"ID='$data->QuestionID'");
	if($res){
        $db->updateRecord('user',"score=(score-5)" , "ID = '$data->UserID'");
		echoSuccess();
    }
	else
		echoError("Cannot update record.");
});

$app->post('/changeQuestionAccepted', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());

	if(isset($data->State)){
		if($data->State=='1' || $data->State=='-1'){
			$sess = new Session();

                    $resQ = $db->makeQuery("select a.ID from user as u
INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
WHERE a.UserID = '$sess->UserID' and (ap.PermissionLevel = 'Base' or ap.PermissionLevel = '$data->AdminPermissionLevel')
LIMIT 1");

    $sql =$resQ->fetch_assoc();
    if(!$sql)
        echoError('You don\'t have permision to do this action');

			$res = $db->updateRecord('forum_question',"AdminAccepted='$data->State'","ID='$data->QuestionID'");
			if($res)
            {
                if($data->State == 1)
                    $db->updateRecord('user',"score=(score+5)" , "ID = '$data->UserID'");
                else if($data->State == -1)
                    $db->updateRecord('user',"score=(score-5)" , "ID = '$data->UserID'");
                echoSuccess();
            }
			else
				echoError("Cannot update record.");
		}else{
			echoError("Sended value:$data->State is not valid!");
		}
	}

	echoError("User state is not set!");
});

$app->post('/getAllUsers', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());
	$pr = new Pagination($data);
	$sess = new Session();

	$where = "WHERE (user.ID!='$sess->UserID')";
	$hasWhere = FALSE;
    if(isset($data->userType)){
        $where .=" AND (user.UserAccepted ='$data->userType')";
	}
	if(isset($data->searchValue) && strlen($data->searchValue) > 0){
		$s = mb_convert_encoding($data->searchValue, "UTF-8", "auto");
		$where .= " AND (Username LIKE '%".$s."%' OR FullName LIKE '%".$s."%' OR Email LIKE '%".$s."%')";
		$hasWhere = TRUE;
	}

	$pageRes = $pr->getPage($db,"SELECT user.`ID`, `FullName`, `Email`, `Password`, `Username`,
`PhoneNumber`, `Tel`, `SignupDate`, `Gender`, `Description`, `SessionID`,
`MailAccepted`, `ValidSessionID`, `UserAccepted`,  admin.ID as 
AdminID FROM user LEFT JOIN admin on admin.UserID = user.ID  ".$where." ORDER BY user.ID desc");

	echoResponse(200, $pageRes);
});

$app->post('/getAllAdminTypes', function() use ($app)  {

    $db = new DbHandler(true);

    $pageRes = $db->makeQuery("SELECT * FROM `admin_permission`");
    $res=[];
    while($r = $pageRes->fetch_assoc())
        $res[] = $r;
    echoSuccess($res);
});


$app->post('/updateAdmin', function() use ($app)  {

    $db = new DbHandler(true);
	$data = json_decode($app->request->getBody());
	$sess = new Session();

$resQ = $db->makeQuery("select a.ID from user as u
INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
WHERE a.UserID = '$sess->UserID' and (ap.PermissionLevel = 'Base')
LIMIT 1");

    $sql =$resQ->fetch_assoc();
    if(!$sql)
        echoError('You don\'t have permision to do this action');

    $resQ = $db->makeQuery("SELECT COUNT(*) as val FROM `user` WHERE `ID` = '$data->UserID'");

    $sql =$resQ->fetch_assoc();
    if($sql["val"] == 0)
        echoError('there is no user with this UserID');

    $resQ = $db->makeQuery("SELECT COUNT(*) as val FROM `admin` WHERE `UserID` = '$data->UserID'");

    $sql =$resQ->fetch_assoc();
    if($sql["val"] == 0){
        $resQ = $db->insertToTable('admin',"`UserID`, `PermissionID`","'$data->UserID','$data->PermissionID'");
    }else
    {
        $resQ = $db->updateRecord('admin',"`PermissionID`= '$data->PermissionID'","UserID = '$data->UserID'");
    }
    if($sql)
        echoSuccess();
    else
        echoError('error in updateing admin');
});

$app->post('/getAllAdmins', function() use ($app)  {

    $db = new DbHandler(true);
    $data = json_decode($app->request->getBody());
    $pr = new Pagination($data);

    $pageRes = $pr->getPage($db,"SELECT u.ID as UserID , u.FullName, u.SignupDate , u.Email , ap.ID as PID , ap.Permission , ap.PermissionLevel ,a.ID FROM `admin` as a INNER JOIN user as u on u.ID = a.`UserID` INNER JOIN admin_permission as ap on ap.ID = a.`PermissionID`");

    echoResponse(200, $pageRes);
});

$app->post('/deleteAdmin', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());

	$sess = new Session();
	if($sess->UserID == $data->AdminID)
		echoError('You cannot delete your account in this action.');

    $resQ = $db->makeQuery("select Count(*) as val from user as u INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
where u.ID = '$sess->UserID' and ap.PermissionLevel = 'Base'");

    $sql =$resQ->fetch_assoc();
    if($sql['val'] == 0)
        echoError('You don\'t have permision to do this action');

	$res = $db->deleteFromTable('admin',"ID='$data->AdminID'");
	if($res)
		echoSuccess();
	else
		echoError("Cannot update record.");
});

?>