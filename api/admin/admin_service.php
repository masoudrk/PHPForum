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

$app->post('/getAllUsers', function() use ($app)  {

	$db = new DbHandler(true);
	$data = json_decode($app->request->getBody());
	$pr = new Pagination($data);
	$sess = new Session();

	$where = "WHERE (user.ID!='$sess->UserID')";
	$hasWhere = FALSE;
	if(isset($data->searchValue) && strlen($data->searchValue) > 0){
		$s = mb_convert_encoding($data->searchValue, "UTF-8", "auto");
		$where = " AND (Username LIKE '%".$s."%' OR FullName LIKE '%".$s."%' OR Email LIKE '%".$s."%')";
		$hasWhere = TRUE;
	}

	$pageRes = $pr->getPage($db,"SELECT user.`ID`, `FullName`, `Email`, `Password`, `Username`, 
`PhoneNumber`, `Tel`, `SignupDate`, `Gender`, `Description`, `SessionID`, 
`ValidSessionID`, `UserAccepted`,  admin.ID as 
AdminID FROM user LEFT JOIN admin on admin.UserID = user.ID  ".$where." ORDER BY user.ID desc");

	echoResponse(200, $pageRes);
});

?>