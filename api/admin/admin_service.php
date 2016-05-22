<?php

$app->post('/logout', function() use ($app)  {
	$sess = new Session();
	$res = $sess->destroySession();
	echoResponse(200, $res);
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
			if($res)
				echoSuccess();
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
`ValidSessionID`, `UserAccepted`,  admin.ID as 
AdminID FROM user LEFT JOIN admin on admin.UserID = user.ID  ".$where." ORDER BY user.ID desc");

	echoResponse(200, $pageRes);
});

?>