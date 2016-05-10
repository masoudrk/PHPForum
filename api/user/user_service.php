<?php

$app->post('/session', function() use ($app)  {
    $sess = new Session();
    $s = $sess->getSession();
    echoResponse(200, $s);
});

$app->post('/getUserProfile', function() use ($app)  {
    //adminRequire();
    require_once '../db/education.php';
    require_once '../db/skill.php';

    $db = new DbHandler();
    $session = new Session();

    $res = [];
    $resQ = $db->makeQuery("SELECT user.`ID`, `FullName`, `Email`, `Username`, `PhoneNumber`, `Tel`, `SignupDate`, `Gender` , FullPath as 
AvatarImagePath FROM user LEFT JOIN file_storage on file_storage.ID = AvatarID WHERE user.ID = 1");

    $user = $resQ->fetch_assoc();

    $user['Educations'] = getUserEducations($db,1);
    $user['AllEducations'] = getAllEducations($db);

    $user['Skills'] = getUserSkills($db,1);
    $user['AllSkills'] = getAllSkills($db);

    echoResponse(200, $user);
});

$app->post('/saveUserInfo', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());
    $db = new DbHandler();
    $sess = new Session();
    $session = $sess->getSession();

    $resQ = null;

    if(isset($data->Password)){
        $pass = passwordHash::hash($data->Password);

        $resQ = $db->updateRecord('user',"`FullName`='$data->FullName',`Email`='$data->Email',`Username`='$data->Username',`PhoneNumber`='$data->PhoneNumber',
`Tel`='$data->Tel',`Password`='$pass'","user.ID='".$session['ID']."'");
    }else{

        $resQ = $db->updateRecord('user',"`FullName`='$data->FullName',`Email`='$data->Email',`Username`='$data->Username',`PhoneNumber`='$data->PhoneNumber',
`Tel`='$data->Tel'","user.ID='".$session['ID']."'");
    }

    $res = [];
    if($resQ){
        $res["Status"] = "success";
    }
    echoResponse(200, $res);
});


$app->post('/saveUserAddintionalInfo', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());
    $db = new DbHandler();
    $sess = new Session();
    $session = $sess->getSession();
    $userID = $session['ID'];

    $d = $db->deleteFromTable('user_skill','UserID='.$userID);

    foreach($data->Skills as &$s){
        $cq = $db->insertToTable('user_skill',"UserID,SkillID","'$userID','$s->ID'");
    }
    $d = $db->deleteFromTable('user_education','UserID='.$userID);

    foreach($data->Educations as &$e){
        $cq = $db->insertToTable('user_education',"UserID,EducationID","'$userID','$e->ID'");
    }

    $res = [];
    $res["Status"] = "success";
    echoResponse(200, $res);
});

$app->post('/sfasf', function() use ($app)  {
    adminRequire();
    $data = json_decode($app->request->getBody());
    $db = new DbHandler();
    $pr = new Pagination($data);

    $query = "SELECT comment.*, post.Title , concat(user.LastName ,' ', user.FirstName) as FullName, concat(up.LastName ,' ', up.FirstName) as AnswerToFullName ,cp.Identity AS AnswerToIdentity ,up.ID as AnswerToUserID FROM comment LEFT JOIN user on user.ID=comment.UserID LEFT JOIN post on post.ID=comment.PostID LEFT JOIN comment AS cp on comment.ParentID=cp.ID LEFT JOIN user AS up on up.ID=cp.UserID ORDER BY comment.Date Desc";

    $pageRes = $pr->getPage($db,$query);
    /*
    foreach($pageRes['Items'] as &$c){
        $cq = $db->makeQuery($query.' WHERE Accepted=1 AND comment.ParentID='.$c['ID'].' ORDER BY ID ASC');
        $c['Childs'] = [];
        while($cc = $cq->fetch_assoc()){
            $c['Childs'][] = $cc;
        }
    }
*/
    echoResponse(200, $pageRes);
});

?>