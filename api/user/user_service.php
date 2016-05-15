<?php

$app->post('/logout', function() use ($app)  {
    $sess = new Session();
    $res = $sess->destroySession();
    echoResponse(200, $res);
});

$app->post('/globalSearch', function() use ($app)  {
    $db = new DbHandler(true);
    $data = json_decode($app->request->getBody());
    $searchValue = $data->searchValue;
    $searchType = $data->searchType;

    $sRes = [];
    $sRes['Items'] = [];
    $sRes['Total'] = 0;
    $sRes['SearchType'] = $searchType;
    $p = new Pagination();
    if($searchType == 0){

        $res = $p->getPage($db,'SELECT * FROM `forum_question` WHERE (Title LIKE N\'%"'.$searchValue.'%\') OR 
    (QuestionText LIKE N\'%'.$searchValue.'%\')');

        $sRes['Total'] =$res['Total'];

        foreach ($res['Items'] as &$s){
            $sr = [];
            $sr['ID'] = $s['ID'];
            $sr['Text'] = $s['Title'];
            $sRes['Items'][] = $sr;
        }
    }else if($searchType == 1){

        $res = $p->getPage($db,'SELECT u.* , fs.FullPath as Image, 
( SELECT count(*) FROM forum_Answer where AuthorID = u.ID ) as AnswersCount, 
( SELECT count(*) FROM forum_Question where AuthorID = u.ID ) as QuestionsCount 
FROM `user` as u LEFT JOIN file_storage as fs on fs.ID=u.AvatarID 
WHERE u.FullName LIKE N\'%'.$searchValue.'%\'');

        $res['SearchType'] = $searchType;
        echoResponse(200, $res);
        return;
    }

    echoResponse(200, $sRes);
});

$app->post('/deleteQuestion', function() use ($app)  {

    require_once '../db/question.php';
    $db = new DbHandler(true);

    $data = json_decode($app->request->getBody());
    $sess = new Session();
    $res = deleteQuestion($db, $data->QuestionID, $sess->UserID);

    if($res){
        $d = [];
        $d['Status'] = "success";
        $d['DeleteQID'] = $data->QuestionID;
        echoResponse(200,$d);
        return ;
    }
    echoResponse(201, "Error:".$data->QuestionID);
});

$app->post('/getAllQuestions', function() use ($app)  {

    require_once '../db/question.php';
    $db = new DbHandler(true);

    $data = json_decode($app->request->getBody());
    $sess = new Session();

    $pin = new PaginationInput($data);
    $res = getPageAuthorQuestions($db,$sess->UserID,$pin);

    echoResponse(200, $res);
});

$app->post('/saveQuestion', function() use ($app)  {
    
    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);
    $sess = new Session();

    $qID = -1;

    if(isset($data->ID)){
        $qID = $data->ID;
        $db->updateRecord('forum_question',"SubjectID='".$data->Subject->ID."',QuestionText='$data->QuestionText',
        Title='$data->Title'",
            "ID='$qID'");
        $d = $db->deleteFromTable('tag_question','QuestionID='.$qID);
    }else{
        $qID = $db->insertToTable('forum_question','QuestionText,Title,SubjectID,AuthorID,CreationDate',
            "'$data->QuestionText',
        '$data->Title','".$data->Subject->ID."','".$sess->UserID."',NOW()",true);
    }

    if(isset($data->Tags)){

        foreach($data->Tags as $tag){
            $cq = $db->insertToTable('tag_question',"TagID,QuestionID","'$tag->ID','$qID'");
        }
    }

    $res = [];
    $res['Status'] ='success';
    $res['QuestionID'] = $qID;

    echoResponse(200, $res);
});

$app->post('/getQuestionMetaEdit', function() use ($app)  {
    //userRequire();
    require_once '../db/tag.php';
    require_once '../db/forum_subject.php';
    $data = json_decode($app->request->getBody());

    $db = new DbHandler(true);
    $res = [];

    if(isset($data)) {
        $resQ = $db->makeQuery("select * from forum_question where forum_question.ID='$data->QuestionID'");
        $res['Question'] = $resQ->fetch_assoc();

        $res['Question']['Subject'] = getQuestionSubject($db , $data->QuestionID);
        $res['Question']['MainSubject'] = getSubjectParent($db , $res['Question']['Subject']['ID']);
        $res['Question']['Tags'] = getQuestionTags($db , $data->QuestionID);

    }

    $res['AllTags'] = getAllTags($db);
    $res['AllSubjects'] = getAllMainSubjectsWithChilds($db);

    echoResponse(200, $res);
});

$app->post('/getForumMainData', function() use ($app)  {

    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);
    $sess = new Session();

    $res = [];
    $resQ = $db->makeQuery("SELECT * FROM forum_main_subject WHERE SubjectName='$data->MainSubjectName'");
    $mainSubject = $resQ->fetch_assoc();
    $res['MainSubject'] = $mainSubject;

    $resQ = $db->makeQuery("SELECT forum_subject.*,
(SELECT Count(*) FROM forum_question WHERE SubjectID=forum_subject.ID) as TotalQuestions
FROM forum_subject WHERE forum_subject
    .ParentSubjectID='".$mainSubject['SubjectID']."'");
    $subjectChilds = [];
    while($r = $resQ->fetch_assoc()){
        $subjectChilds[] = $r;
    }

    $res['SubjectChilds'] = $subjectChilds;


    echoResponse(200, $res);
});

$app->post('/getForumLastQuestions', function() use ($app)  {
    //adminRequire();

    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);

    $subjectID = -1;
    if(isset($data->MainSubjectName)){
        $resQ = $db->makeQuery("SELECT forum_main_subject.SubjectID FROM forum_main_subject WHERE 
                                forum_main_subject.SubjectName='$data->MainSubjectName'");

        $subjectID= $resQ->fetch_assoc()['SubjectID'];
    }

    $pr = new Pagination($data);

    $query = "SELECT user.FullName,forum_question.`ID`, `QuestionText`, `Title`, `AuthorID`, `CreationDate`,`FullPath` as Image FROM 
forum_question  LEFT JOIN user
 on user
.ID=forum_question.AuthorID LEFT JOIN file_storage on 
file_storage.ID=user.AvatarID WHERE 
    forum_question
.SubjectID='$subjectID'";

    $pageRes = $pr->getPage($db,$query);

    echoResponse(200, $pageRes);
});

$app->post('/getUserProfile', function() use ($app)  {
    //adminRequire();
    require_once '../db/education.php';
    require_once '../db/skill.php';

    $db = new DbHandler(true);
    $sess = new Session();


    $resQ = $db->makeQuery("SELECT user.`ID`,user.Description, `FullName`, `Email`, `Username`, `PhoneNumber`, `Tel`, 
    `SignupDate`,
 `Gender` , FullPath as AvatarImagePath ,
 (SELECT count(*) FROM forum_question where forum_question.AuthorID='$sess->UserID') as QuestionsCount,
 (SELECT count(*) FROM forum_answer where forum_answer.AuthorID='$sess->UserID') as AnswersCount,
 (SELECT count(*) FROM person_follow where person_follow.TargetUserID='$sess->UserID') as FollowersCount
 FROM user LEFT JOIN file_storage on file_storage.ID = AvatarID WHERE user.ID = $sess->UserID");

    $user = $resQ->fetch_assoc();

    $user['Educations'] = getUserEducations($db,$sess->UserID);
    $user['AllEducations'] = getAllEducations($db);

    $user['Skills'] = getUserSkills($db,$sess->UserID);
    $user['AllSkills'] = getAllSkills($db);

    echoResponse(200, $user);
});

$app->post('/getProfile', function() use ($app)  {

    $r = json_decode($app->request->getBody());
    $db = new DbHandler(true);

    if(!$r->UserID || !$r->TargetUserID)
        echoResponse(201, 'bad request');
        
    $resQ = $db->makeQuery("select u.FullName , u.Email ,u.PhoneNumber, u.Tel , u.SignupDate ,u.Gender, u.Description ,u.score, f.FullPath , o.OrganizationName,
(SELECT count(*) FROM forum_Question where AuthorID = u.ID) as QuestionsCount ,
(SELECT count(*) FROM forum_Answer where AuthorID = u.ID) as AnswerCount ,
(SELECT count(*) FROM person_follow where TargetUserID = '$r->TargetUserID' and UserID = '$r->UserID' ) as PersonFollow
from user as u
inner join file_storage as f on f.ID = u.AvatarID
left join organ_position as o on u.OrganizationID = o.ID
where u.UserAccepted = 1 and u.ID = '$r->TargetUserID'");

    $resp = $resQ->fetch_assoc();
    $resQ = $db->makeQuery("select distinct s.Skill from skill as s
inner join user_skill as us on us.UserID = s.ID
where us.UserID = '$r->UserID'");

    $skills = [];
    while($item = $resQ->fetch_assoc())
            $skills[] = $item;
    $resp['Skills'] = $skills;

    $resQ = $db->makeQuery("select * from forum_question as q where q.AuthorID = '$r->TargetUserID' order by q.CreationDate desc");

    $resp['LastQuestion'] = $resQ->fetch_assoc();

    $resQ = $db->makeQuery("select q.* ,(SELECT sum(RateValue) FROM question_rate where QuestionID = q.ID) as QuestionRate
from forum_question as q
where q.AuthorID = '$r->TargetUserID' and q.AdminAccepted = 1
order by QuestionRate desc
");

    $resp['BestQuestion'] = $resQ->fetch_assoc();

    echoResponse(200, $resp);
});

$app->post('/getQuestionByID', function() use ($app)  {

    $r = json_decode($app->request->getBody());
    $db = new DbHandler(true);

    if(!isset($r->UserID) || !isset($r->QuestionID))
        {echoResponse(201, 'bad request');return;}

    $resQ =$db->makeQuery("select count(*) as val from question_view where UserID = '$r->UserID' and QuestionID = '$r->QuestionID'");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] == 0)
        {$resQ =$db->makeQuery("insert into question_view (UserID, QuestionID , ViewDate) values ('$r->UserID','$r->QuestionID',now())");}

    $resQ = $db->makeQuery("select q.* , u.FullName ,u.ID as UserID, u.Email ,u.score, u.Description , f.FullPath ,s.Title as Subject,ms.Title as MainTitle , o.OrganizationName ,
(SELECT count(*) FROM forum_Question where AuthorID = u.ID) as QuestionsCount ,
(SELECT count(*) FROM forum_Answer where AuthorID = u.ID) as AnswerCount ,
(SELECT count(*) FROM question_view where QuestionID = q.ID) as ViewCount ,
(SELECT q.RateValue FROM question_rate as q where q.UserID = '$r->UserID' and q.QuestionID = '$r->QuestionID' limit 1) as PersonQuestionRate ,
(SELECT count(*) FROM question_follow where QuestionID = q.ID) as FollowCount ,
(SELECT sum(RateValue) FROM question_rate where QuestionID = q.ID) as QuestionScore ,
(SELECT count(*) FROM question_follow where QuestionID = q.ID and UserID = '$r->UserID') as PersonFollow
from forum_question as q
inner join user as u on u.ID = q.AuthorID
inner join file_storage as f on f.ID = u.AvatarID
inner join forum_subject as s on q.SubjectID = s.ID
inner join forum_main_subject as ms on ms.ID = s.ParentSubjectID
left join organ_position as o on u.OrganizationID = o.ID
where q.ID = '$r->QuestionID' and AdminAccepted = 1 and f.IsAvatar = 1 ");

    $resp = $resQ->fetch_assoc();
    $resQ = $db->makeQuery("select t.* from tag as t
inner join tag_question as tg on tg.tagID = t.ID
where tg.QuestionID = '$r->QuestionID'");

    $tags = [];
    while($item = $resQ->fetch_assoc())
            $tags[] = $item;
    $resp['Tags'] = $tags;

        $resQ = $db->makeQuery("select a.* , u.FullName ,u.ID as UserID, u.Email ,u.OrganizationID ,u.score, u.Description , f.FullPath, o.OrganizationName ,
(SELECT count(*) FROM forum_Question where AuthorID = u.ID) as QuestionsCount ,
(SELECT count(*) FROM forum_Answer where AuthorID = u.ID) as AnswerCount ,
(SELECT q.RateValue FROM answer_rate as q where q.UserID = '$r->UserID' and q.AnswerID = a.ID limit 1) as PersonAnswerRate ,
(SELECT sum(RateValue) FROM answer_rate where AnswerID = a.ID) as AnswerScore ,
(SELECT count(*) FROM person_follow where TargetUserID = a.ID and UserID = '$r->UserID') as PersonFollow
from forum_answer as a
inner join user as u on u.ID = a.AuthorID
inner join file_storage as f on f.ID = u.AvatarID
left join organ_position as o on u.OrganizationID = o.ID
where a.QuestionID = '$r->QuestionID' and AdminAccepted = 1 and f.IsAvatar = 1 order by a.CreationDate");

    $Answers = [];
    while($item = $resQ->fetch_assoc())
            $Answers[] = $item;
    $resp['Answers'] = $Answers;

    echoResponse(200, $resp);
});

$app->post('/followMainSubject', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);
    if(!isset($data->UserID) || !isset($data->MainSubjectID))
        {echoResponse(201, 'bad request');return;}

    $resQ =$db->makeQuery("select count(*) as val from main_subject_follow where UserID = '$data->UserID' and MainSubjectID = '$data->MainSubjectID'");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] > 0)
        {echoResponse(200, false);return;}
    $resQ =$db->makeQuery("insert into main_subject_follow (UserID, MainSubjectID , MainSubjectFollowDate) values ('$data->UserID','$data->MainSubjectID',now())");
    echoResponse(200, true);
});

$app->post('/followQuestion', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);
    if(!isset($data->UserID) || !isset($data->QuestionID))
        {echoResponse(201, 'bad request');return;}

    $resQ =$db->makeQuery("select count(*) as val from question_follow where UserID = '$data->UserID' and QuestionID = '$data->QuestionID'");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] > 0)
    {echoResponse(200, false);return;}
    $resQ =$db->makeQuery("insert into question_follow (UserID, QuestionID , QuestionFollowDate) values ('$data->UserID','$data->QuestionID',now())");
    echoResponse(200, true);
    return;
});

$app->post('/followPerson', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);
    if(!isset($data->UserID) || !isset($data->TargetUserID))
        {echoResponse(201, 'bad request');return;}

    $resQ =$db->makeQuery("select count(*) as val from person_follow where UserID = '$data->UserID' and TargetUserID = '$data->TargetUserID'");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] > 0)
        {echoResponse(200, false);return;}
    $resQ =$db->makeQuery("insert into person_follow (UserID, TargetUserID , PersonFollowDate) values ('$data->UserID','$data->TargetUserID',now())");
    echoResponse(200, true);
});

$app->post('/followSubject', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);
    if(!isset($data->UserID) || !isset($data->SubjectID))
        {echoResponse(201, 'bad request');return;}

    $resQ =$db->makeQuery("select count(*) as val from subject_follow where UserID = '$data->UserID' and SubjectID = '$data->SubjectID'");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] > 0)
        {echoResponse(200, false);return;}
    $resQ =$db->makeQuery("insert into subject_follow (UserID, SubjectID , SubjectFollowDate) values ('$data->UserID','$data->SubjectID',now())");
    echoResponse(200, true);
});

$app->post('/unFollowSubject', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);
    if(!isset($data->UserID) || !isset($data->SubjectID))
        {echoResponse(201, 'bad request');return;}

    $db->deleteFromTable('subject_follow',"UserID = '$data->UserID' and SubjectID = '$data->SubjectID'");
    echoResponse(200, true);
});

$app->post('/unFollowMainSubject', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);
    if(!isset($data->UserID) || !isset($data->MainSubjectID))
        {echoResponse(201, 'bad request');return;}

    $db->deleteFromTable('main_subject_follow',"UserID = '$data->UserID' and MainSubjectID = '$data->MainSubjectID'");
    echoResponse(200, true);
});

$app->post('/unFollowPerson', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);
    if(!isset($data->UserID) || !isset($data->TargetUserID))
        {echoResponse(201, 'bad request');return;}

    $db->deleteFromTable('person_follow',"UserID = '$data->UserID' and TargetUserID = '$data->TargetUserID'");
    echoResponse(200, true);
});

$app->post('/unFollowQuestion', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);
    if(!isset($data->UserID) || !isset($data->QuestionID))
        {echoResponse(201, 'bad request');return;}

    $db->deleteFromTable('Question_follow',"UserID = '$data->UserID' and QuestionID = '$data->QuestionID'");
    echoResponse(200, true);
});

$app->post('/rateQuestion', function() use ($app)  {
    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);
    if(!isset($data->UserID) || !isset($data->QuestionID) || !isset($data->RateValue)|| !isset($data->TargetUserID) || $data->RateValue ==0)
        {echoResponse(201, 0);return;}

    if($data->RateValue > 1)
        $data->RateValue = 1;
    else if($data->RateValue < -1)
        $data->RateValue = -1;

    $resQ =$db->makeQuery("select count(*) as val from question_rate where UserID = '$data->UserID' and QuestionID = '$data->QuestionID'");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] > 0)
    {
        $db->updateRecord('question_rate',"RateValue='$data->RateValue' , QuestionRateDate = now()" , "UserID = '$data->UserID' and QuestionID = '$data->QuestionID'");
        echoResponse(200, $data->RateValue);
        return;
    }
    else
    {
        if($data->RateValue == 1)
            $db->updateRecord('user',"score=($data->RateValue+score)" , "ID = '$data->TargetUserID'");
    	$db->insertToTable('question_rate',"UserID,QuestionID,RateValue,QuestionRateDate","'$data->UserID','$data->QuestionID','$data->RateValue',now()");
        echoResponse(200, $data->RateValue);
        return;
    }
});

$app->post('/rateAnswer', function() use ($app)  {
    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);
    if(!isset($data->UserID) || !isset($data->AnswerID) || !isset($data->RateValue)|| !isset($data->TargetUserID) || $data->RateValue==0)
        {echoResponse(201, 0);return;}

    if($data->RateValue > 1)
        $data->RateValue = 1;
    else if($data->RateValue < -1)
        $data->RateValue = -1;

    $resQ =$db->makeQuery("select count(*) as val from answer_rate where UserID = '$data->UserID' and AnswerID = '$data->AnswerID'");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] > 0)
    {
        $db->updateRecord('answer_rate',"RateValue='$data->RateValue' , AnswerRateDate = now()" , "UserID = '$data->UserID' and AnswerID = '$data->AnswerID'");
        echoResponse(200, $data->RateValue);
        return;
    }
    else
    {
        if($data->RateValue == 1)
            $db->updateRecord('user',"score=($data->RateValue+score)" , "ID = '$data->TargetUserID'");
    	$db->insertToTable('answer_rate',"UserID,AnswerID,RateValue,AnswerRateDate","'$data->UserID','$data->AnswerID','$data->RateValue',now()");
        echoResponse(200, $data->RateValue);
        return;
    }
});

$app->post('/saveAnswer', function() use ($app)  {
    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);
    if(!isset($data->AuthorID) || !isset($data->QuestionID) || !isset($data->AnswerText))
        {echoResponse(201, 'bad Request');return;}

    $db->insertToTable('forum_answer',"AuthorID,QuestionID,AnswerText,CreationDate","'$data->AuthorID','$data->QuestionID','$data->AnswerText',now()");
    echoResponse(200, true);
    return;
});

$app->post('/saveMainSubject', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);
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
    $db = new DbHandler(true);
    $sess = new Session();

    $db->updateRecord('user',"Description='$data->Description'",'ID='.$sess->UserID);

    $d = $db->deleteFromTable('user_skill','UserID='.$sess->UserID);
    if(isset($data->Skills)){
        foreach($data->Skills as &$s){
            $cq = $db->insertToTable('user_skill',"UserID,SkillID","'$sess->UserID','$s->ID'");
        }
    }

    $d = $db->deleteFromTable('user_education','UserID='.$sess->UserID);
    if(isset($data->Educations)){
        foreach($data->Educations as &$e){
            $cq = $db->insertToTable('user_education',"UserID,EducationID","'$sess->UserID','$e->ID'");
        }
    }

    $res = [];
    $res["Status"] = "success";
    echoResponse(200, $res);
});

$app->post('/saveUserInfo', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);
    $sess = new Session();

    $resQ = null;

    if(isset($data->Password)){
        $pass = passwordHash::hash($data->Password);

        $resQ = $db->updateRecord('user',"`FullName`='$data->FullName',`Email`='$data->Email',`Username`='$data->Username',`PhoneNumber`='$data->PhoneNumber',
`Tel`='$data->Tel',`Password`='$pass'","user.ID='$sess->UserID'");
    }else{

        $resQ = $db->updateRecord('user',"`FullName`='$data->FullName',`Email`='$data->Email',`Username`='$data->Username',`PhoneNumber`='$data->PhoneNumber',
`Tel`='$data->Tel'","user.ID='$sess->UserID'");
    }

    $sess->updateFullName($data->FullName);

    $res = [];
    if($resQ){
        $res["Status"] = "success";
        $res["FullName"] = $data->FullName;
    }
    echoResponse(200, $res);
});

$app->post('/updateAvatar', function() use ($app)  {
    $filename = $_FILES['file']['name'];

    $rand = generateRandomString(18);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);

    $absPath =  '../content/avatars/';
    if (!file_exists('../'.$absPath)) {
        mkdir('../'.$absPath, 0777, true);
    }

    $destination =$absPath.$rand.'.'.$ext;
    move_uploaded_file( $_FILES['file']['tmp_name'] , '../'.$destination );

    $sess = new Session();
    $db = new DbHandler(true);
    $fileID = $db->insertToTable('file_storage','FullPath,UploadDate,UserID',"'$destination',NOW(),'$sess->UserID'");
    $resUpdate = $db->updateRecord('user',"AvatarID='$fileID'","ID='$sess->UserID'");

    if($resUpdate){
        $sess->updateImage($destination);

        $res = [];
        $res['Status'] = 'success';
        $res['Image'] = $destination;
        echoResponse(200, $res);
        return;
    }

    echoError("Error");
});
?>