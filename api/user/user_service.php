<?php

$app->post('/logout', function() use ($app)  {
    $sess = new Session();
    $res = $sess->destroySession();
    echoResponse(200, $res);
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

    foreach($data->Tags as $tag){
        $cq = $db->insertToTable('tag_question',"TagID,QuestionID","'$tag->ID','$qID'");
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
        $res['Question']['Tags'] = getQuestionTags($db , $data->QuestionID);
    }

    $res['AllTags'] = getAllTags($db);
    $res['AllSubjects'] = getAllForumSubjects($db);

    echoResponse(200, $res);
});

$app->post('/getForumMainData', function() use ($app)  {
    //adminRequire();

    $db = new DbHandler(true);
    $sess = new Session();
    $session = $sess->getSession();
    $userID = $session['Session']['UserID'];

    $res = [];
    $resQ = $db->makeQuery("SELECT * FROM ");

    $user = $resQ->fetch_assoc();

    $user['Educations'] = getUserEducations($db,$userID);
    $user['AllEducations'] = getAllEducations($db);

    $user['Skills'] = getUserSkills($db,$userID);
    $user['AllSkills'] = getAllSkills($db);

    echoResponse(200, $user);
});

$app->post('/getForumLastQuestions', function() use ($app)  {
    //adminRequire();

    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);

    $subjectID = -1;
    if(isset($data->ForumName)){
        $resQ = $db->makeQuery("SELECT forum_main_subject.SubjectID FROM forum_main_subject WHERE 
                                forum_main_subject.SubjectName='$data->ForumName'");

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


    $resQ = $db->makeQuery("SELECT user.`ID`, `FullName`, `Email`, `Username`, `PhoneNumber`, `Tel`, `SignupDate`, `Gender` , FullPath as 
AvatarImagePath FROM user LEFT JOIN file_storage on file_storage.ID = AvatarID WHERE user.ID = $sess->UserID");

    $user = $resQ->fetch_assoc();

    $user['Educations'] = getUserEducations($db,$sess->UserID);
    $user['AllEducations'] = getAllEducations($db);

    $user['Skills'] = getUserSkills($db,$sess->UserID);
    $user['AllSkills'] = getAllSkills($db);

    echoResponse(200, $user);
});

$app->post('/getQuestionByID', function() use ($app)  {

    $r = json_decode($app->request->getBody());
    $db = new DbHandler(true);

    $resQ = $db->makeQuery("select q.* , u.FullName , u.Email ,u.score, u.Description , f.FullPath ,s.Title as Subject,ms.Title as MainTitle , o.OrganizationName ,
(SELECT count(*) FROM forum_Question where AuthorID = u.ID) as QuestionsCount ,
(SELECT count(*) FROM forum_Answer where AuthorID = u.ID) as AnswerCount ,
(SELECT count(*) FROM question_view where QuestionID = q.ID) as ViewCount ,
(SELECT q.RateValue FROM question_rate as q where q.UserID = '$r->UserID') as PersonQuestionRate ,
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
    $resQ = $db->makeQuery("select t.Text from tag as t
inner join tag_question as tg on tg.tagID = t.ID
where tg.QuestionID = '$r->QuestionID'");

    $tags = [];
    while($item = $resQ->fetch_assoc())
            $tags[] = $item;
    $resp['Tags'] = $tags;

        $resQ = $db->makeQuery("select a.* , u.FullName , u.Email ,u.OrganizationID ,u.score, u.Description , f.FullPath, o.OrganizationName ,
(SELECT count(*) FROM forum_Question where AuthorID = u.ID) as QuestionsCount ,
(SELECT count(*) FROM forum_Answer where AuthorID = u.ID) as AnswerCount ,
(SELECT q.RateValue FROM answer_rate as q where q.UserID = '$r->UserID') as PersonAnswerRate ,
(SELECT sum(RateValue) FROM answer_rate where AnswerID = a.ID) as AnswerScore ,
(SELECT count(*) FROM person_follow where TargetUserID = a.ID and UserID = '$r->UserID') as PersonFollow
from forum_answer as a
inner join user as u on u.ID = a.AuthorID
inner join file_storage as f on f.ID = u.AvatarID
left join organ_position as o on u.OrganizationID = o.ID
where a.QuestionID = '$r->QuestionID' and AdminAccepted = 1 and f.IsAvatar = 1");

    $Answers = [];
    while($item = $resQ->fetch_assoc())
            $Answers[] = $item;
    $resp['Answers'] = $Answers;

    //$resp['AllEducations'] = getAllEducations($db);

    //$resp['Skills'] = getUserSkills($db,$sess->UserID);
    //$resp['AllSkills'] = getAllSkills($db);

    echoResponse(200, $resp);
});

$app->post('/saveUserInfo', function() use ($app)  {
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
    $d = $db->deleteFromTable('user_skill','UserID='.$sess->UserID);

    foreach($data->Skills as &$s){
        $cq = $db->insertToTable('user_skill',"UserID,SkillID","'$sess->UserID','$s->ID'");
    }
    $d = $db->deleteFromTable('user_education','UserID='.$sess->UserID);

    foreach($data->Educations as &$e){
        $cq = $db->insertToTable('user_education',"UserID,EducationID","'$sess->UserID','$e->ID'");
    }

    $res = [];
    $res["Status"] = "success";
    echoResponse(200, $res);
});

$app->post('/sfasf', function() use ($app)  {
    adminRequire();
    $data = json_decode($app->request->getBody());
    $db = new DbHandler(true);
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