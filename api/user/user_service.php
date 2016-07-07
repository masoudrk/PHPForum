<?php
$app->post('/getSocketData', function() use ($app)  {
    
    $s = $app->session;

    $resQ = $app->db->makeQuery("Select user.ID,user.FullName,user.LastActiveTime,file_storage.FullPath as Image from user LEFT JOIN file_storage on 
    file_storage.ID=user.AvatarID 
 where UserAccepted=1 and user.ID!='$s->UserID' and user.LastActiveTime > NOW() - INTERVAL 3 MINUTE");

    $arr = [];
    $res = [];
    while($r = $resQ->fetch_assoc()){
        $arr[] = $r;
    }

    $res['OnlineUsers'] = $arr;
    echoResponse(200, $res);
});

$app->post('/globalSearch', function() use ($app)  {
    
    $data = json_decode($app->request->getBody());
    $sess = $app->session;

    $searchValue = $data->searchValue;
    $searchType = $data->searchType;

    $sRes = [];
    $sRes['Items'] = [];
    $sRes['Total'] = 0;
    $sRes['SearchType'] = $searchType;
    $p = new Pagination();

    // question
    if($searchType == 0){

        $searchQuery = '';
        $searchArr = explode(" ",$searchValue);
        foreach ($searchArr as $s){
            $searchQuery .= " AND ( (Title LIKE N'%$s%') OR (QuestionText LIKE N'%$s%') )";
        }

        $rateSelection = "(SELECT count(*) from forum_question where forum_question.AuthorID=u.ID and (forum_question.CreationDate > NOW() - 
 INTERVAL 7 DAY))+
 (SELECT count(*) from forum_answer where forum_answer.AuthorID=u.ID)+
 (SELECT count(*)/2 from question_view where question_view.UserID=u.ID and (question_view.ViewDate > NOW() - 
 INTERVAL 7 DAY))";
        $res = $p->getPage($app->db,"SELECT forum_question.* , u.score, u.FullName , file_storage.FullPath as Image,
 (SELECT question_view.ID from question_view 
        where question_view.QuestionID=forum_question.ID AND question_view.UserID=$sess->UserID LIMIT 1) as 'QViewID' ,
 (SELECT count(*) from forum_answer where forum_answer.QuestionID=forum_question.ID and forum_answer.AdminAccepted=1)
   as 'AnswersCount' ,
 ($rateSelection) as Rate
FROM `forum_question` 
    LEFT JOIN user as u on u.ID=forum_question.AuthorID
    LEFT JOIN file_storage on u.AvatarID=file_storage.ID
WHERE forum_question.AdminAccepted=1 $searchQuery");

        $res['SearchType'] = $searchType;
        echoResponse(200, $res);
        return;

    // user
    }else if($searchType == 1){

        $searchQuery = '';
        $searchArr = explode(" ",$searchValue);
        foreach ($searchArr as $s){
            $searchQuery .= " AND u.FullName LIKE N'%$s%' ";
        }

        $rateSelection = "(SELECT count(*) from forum_question where forum_question.AuthorID=u.ID and (forum_question.CreationDate > NOW() - 
 INTERVAL 7 DAY))+
 (SELECT count(*) from forum_answer where forum_answer.AuthorID=u.ID)+
 (SELECT count(*)/2 from question_view where question_view.UserID=u.ID and (question_view.ViewDate > NOW() - 
 INTERVAL 7 DAY))";

        $res = $p->getPage($app->db,"SELECT u.FullName,u.Email ,u.ID,u.Gender,u.PhoneNumber,u.SignupDate 
, u.Description,u.LastActiveTime ,u.Username,u.score, fs.FullPath as Image, 
( SELECT count(*) FROM forum_answer where AuthorID = u.ID and forum_answer.AdminAccepted=1 ) as AnswersCount, 
( SELECT count(*) FROM forum_question where AuthorID = u.ID and  forum_question.AdminAccepted=1 ) as QuestionsCount ,
($rateSelection) as Rate
FROM `user` as u LEFT JOIN file_storage as fs on fs.ID=u.AvatarID 
WHERE u.UserAccepted=1 ".$searchQuery);

        $res['SearchType'] = $searchType;
        echoResponse(200, $res);
        return;
    }else if($searchType == 2){
            $searchQuery = '';
        $searchArr = explode(" ",$searchValue);
        foreach ($searchArr as $s){
            $searchQuery .= " AND ( t.Text LIKE N'%$s%' )";
        }

        $rateSelection = "(SELECT count(*) from forum_question where forum_question.AuthorID=u.ID and (forum_question.CreationDate > NOW() -
 INTERVAL 7 DAY))+
 (SELECT count(*) from forum_answer where forum_answer.AuthorID=u.ID)+
 (SELECT count(*)/2 from question_view where question_view.UserID=u.ID and (question_view.ViewDate > NOW() -
 INTERVAL 7 DAY))";
        $res = $p->getPage($app->db,"SELECT forum_question.* , u.score, u.FullName , file_storage.FullPath as Image,
 (SELECT question_view.ID from question_view
        where question_view.QuestionID=forum_question.ID AND question_view.UserID=$sess->UserID LIMIT 1) as 'QViewID' ,
 (SELECT count(*) from forum_answer where forum_answer.QuestionID=forum_question.ID and forum_answer.AdminAccepted=1)
   as 'AnswersCount' ,
 ($rateSelection) as Rate
FROM `forum_question`
    LEFT JOIN user as u on u.ID=forum_question.AuthorID
    LEFT JOIN file_storage on u.AvatarID=file_storage.ID
    INNER JOIN tag_question as tq on tq.QuestionID = forum_question.ID
    INNER JOIN tag as t on t.ID = tq.TagID
WHERE forum_question.AdminAccepted=1 $searchQuery GROUP by forum_question.ID");

        $res['SearchType'] = 0;
        echoResponse(200, $res);
        return;
    }

    echoResponse(200, $sRes);
});

$app->post('/deleteQuestion', function() use ($app)  {

    require_once '../db/question.php';


    $data = json_decode($app->request->getBody());
    $sess = $app->session;
    $res = deleteQuestion($app->db, $data->QuestionID, $sess->UserID);

    if($res){
        $d = [];
        $d['Status'] = "success";
        $d['DeleteQID'] = $data->QuestionID;
        echoResponse(200,$d);
        return ;
    }
    echoResponse(201, "Error:".$data->QuestionID);
});

$app->post('/getMyFollowing', function() use ($app)  {

    $data = json_decode($app->request->getBody());

    $session = $app->session;
    $pr = new Pagination($data);
    $pageRes = null;

    switch ($data->FollowType){
        case 'Person' :
            $query = "select 
            u.FullName , u.Email , u.ID as UserID ,person_follow.ID as PersonFollowID , u.score,
             person_follow.PersonFollowDate , file_storage.FullPath as Image,
             (select count(*) from forum_question where forum_question.AuthorID=u.ID) as QuestionsCount,
             (select count(*) from forum_answer where forum_answer.AuthorID=u.ID) as AnswersCount
            FROM person_follow
              inner join user as u on u.ID=person_follow.TargetUserID
              left join file_storage on file_storage.ID= u.AvatarID
              
            where person_follow.UserID='$session->UserID'
            ORDER by person_follow.PersonFollowDate desc";
            break;
        case 'Question' :
            $query = "select 
             u.FullName , u.Email , u.ID as UserID ,question_follow.ID as PersonFollowID , u.score
            ,question_follow.ID as QuestionFollowID, question_follow.QuestionFollowDate , file_storage.FullPath as Image
            ,fq.Title , question_follow.QuestionID,
             (select question_view.ID from question_view 
        where question_view.QuestionID=fq.ID AND question_view.UserID=$session->UserID LIMIT 1) as 'QViewID'
            FROM question_follow
              inner join forum_question as fq on fq.ID=question_follow.QuestionID
              inner join user as u on u.ID=fq.AuthorID
              left join file_storage on file_storage.ID= u.AvatarID
              
            where question_follow.UserID='$session->UserID'
            ORDER by question_follow.QuestionFollowDate desc";
            break;
        case 'MainSubject' :
            $query = "select 
             msf.ID as MainSubjectFollowID, msf.MainSubjectFollowDate ,fms.Title , fms.SubjectName 
            FROM main_subject_follow as msf
              inner join forum_main_subject as fms on fms.SubjectID=msf.MainSubjectID              
            where msf.UserID='$session->UserID'
            ORDER by msf.MainSubjectFollowDate desc";
            break;
        case 'Subject' :
            $query = "select 
             sf.ID as SubjectFollowID , sf.SubjectID, sf.SubjectFollowDate ,fs.Title , fms.Title as MainSubjectTitle
            FROM subject_follow as sf
              inner join forum_subject as fs on fs.ID=sf.SubjectID    
              inner join forum_main_subject as fms on fs.ParentSubjectID=fms.SubjectID              
            where sf.UserID='$session->UserID'
            ORDER by sf.SubjectFollowDate desc";
            break;
    }

    $pageRes = $pr->getPage($app->db,$query);
    echoResponse(200, $pageRes);
});

$app->post('/deleteFollow', function() use ($app)  {

    $data = json_decode($app->request->getBody());

    $session = $app->session;
    $res = null;
    switch ($data->FollowType){
        case 'Person' :
            $res = $app->db->deleteFromTable("person_follow","ID='$data->ID' and UserID='$session->UserID'");
            break;
        case 'Question' :
            $res = $app->db->deleteFromTable("question_follow","ID='$data->ID' and UserID='$session->UserID'");
            break;
        case 'MainSubject' :
            $res = $app->db->deleteFromTable("main_subject_follow","ID='$data->ID' and UserID='$session->UserID'");
            break;
        case 'Subject' :
            $res = $app->db->deleteFromTable("subject_follow","ID='$data->ID' and UserID='$session->UserID'");
            break;
    }

    if($res)
        echoSuccess($data->FollowType);
    else
        echoError("Cannot detect type of follow.");
});

$app->post('/deleteSession', function() use ($app)  {

    $data = json_decode($app->request->getBody());

    $sess= $app->session;

    $resQ = $app->db->makeQuery("select * from user_session where user_session.ID='".$data->ID."'");
    $s = $resQ->fetch_assoc();

    if($s['SessionID'] == $sess->SSN)
        echoError("CurrentSession");

    $res = $app->db->deleteFromTable('user_session',"ID='".$data->ID."'");

    if($res)
        echoSuccess();

    echoError("Cannot delete from table.");
});

$app->post('/getUserLastQuestion', function() use ($app)  {


    $data = json_decode($app->request->getBody());
    if(!isset($data->UserID))
    {echoResponse(201,"bad request"); return;}
    $resQ= $app->db->makeQuery("select DISTINCT q.ID ,q.Title ,q.CreationDate,
(select sum(RateValue) from question_rate where QuestionID = q.ID) as QuestionRate,
(select count(*) from question_follow where QuestionID = q.ID) as QuestionUserFollow,
(select count(*) from question_view where QuestionID = q.ID) as questionView,
(select count(*) from forum_answer where QuestionID = q.ID) as questionAnswers
from forum_question as q  where q.AuthorID = '$data->UserID' and q.AdminAccepted = 1 order by q.CreationDate limit 5");

    $resp = [];
    while($r = $resQ->fetch_assoc()){
        $resp[] = $r;}

echoResponse(200 , $resp);
});

$app->post('/markLastNotifications', function() use ($app)  {

    require_once '../db/event.php';

    $sess = $app->session;

    $app->db->makeQuery("update event set event.EventSeen='1' 
                           where event.EventUserID='$sess->UserID' and event.EventSeen='0' 
                           order by event.EventDate desc limit 25");

    $notify = [];
    $notify['Total']= getUserTotalNotifications($app->db,$sess->UserID);
    $notify['All'] = getUserLastNotifications($app->db, $sess->UserID , 25);
    echoSuccess($notify);
});

$app->post('/getUserNotifications', function() use ($app)  {

    require_once '../db/event.php';

    $sess = $app->session;

    $notify = [];
    $notify['Total']= getUserTotalNotifications($app->db,$sess->UserID);
    $notify['All'] = getUserLastNotifications($app->db, $sess->UserID , 25);
    echoSuccess($notify);
});

$app->post('/markAsReadNotification', function() use ($app)  {

    $sess = $app->session;
    $data = json_decode($app->request->getBody());

    $res = $app->db->updateRecord('event',"EventSeen='1'" , "ID='$data->EventID' and EventUserID='$sess->UserID'");
    echoSuccess($res);
});

$app->post('/markAsReadMessage', function() use ($app)  {

    $sess = $app->session;
    $data = json_decode($app->request->getBody());

    $res = $app->db->updateRecord('message',"MessageViewed='1'"
        , "ID='$data->MessageID' and UserID='$sess->UserID'");
    echoSuccess($res);
});

$app->post('/getUserMessages', function() use ($app)  {

    require_once '../db/message.php';

    $sess = $app->session;

    $res = [];
    $res['All'] = getUserUnreadMessages($app->db,$sess->UserID,20);
    $res['Total'] = getUserUnreadMessagesCount($app->db,$sess->UserID);
    echoResponse(200 , $res);
});

$app->post('/getUserMessageByID', function() use ($app)  {

    require_once '../db/message.php';
    $data = json_decode($app->request->getBody());

    $sess = $app->session;

    $res = getUserMessageByID($app->db,$sess->UserID,$data->MessageID);
    echoResponse(200 , $res);
});

$app->post('/getAllUserMessages', function() use ($app)  {

    require_once '../db/message.php';

    $data = json_decode($app->request->getBody());
    $sess = $app->session;

    $pin = new PaginationInput($data);
    $res = getPageUserMessages($app->db,$sess->UserID,$pin);

    echoResponse(200, $res);
});

$app->post('/markAsReadMessage', function() use ($app)  {

    $data = json_decode($app->request->getBody());

    $sess = $app->session;
    $resQ = $app->db->makeQuery("SELECT 1 FROM message where message.UserID='$sess->UserID' 
AND message.ID='$data->MessageID' LIMIT 1");

    $c = mysqli_num_rows($resQ);
    if($c > 0){
        $app->db->updateRecord('message',"MessageViewed=1","ID='$data->MessageID'");
        echoSuccess($data->MessageID);
    }
    echoError("Error in updating because message '$data->MessageID' is not belong to you.");
});

$app->post('/getAllQuestions', function() use ($app)  {

    require_once '../db/question.php';

    $data = json_decode($app->request->getBody());
    $sess = $app->session;

    $pin = new PaginationInput($data);
    $res = getPageAuthorQuestions($app->db,$sess->UserID,$pin);

    echoResponse(200, $res);
});

$app->post('/getAllMyAnswers', function() use ($app)  {

    require_once '../db/forum_answer.php';

    $data = json_decode($app->request->getBody());
    $sess = $app->session;

    $pin = new PaginationInput($data);
    $res = getPageUserAnswers($app->db,$sess->UserID,$pin);

    echoResponse(200, $res);
});

$app->post('/editQuestion', function() use ($app)  {

    $data = json_decode($app->request->getBody());
    $sess = $app->session;

    $qID = $data->ID;

    $resQ = $app->db->makeQuery("Select forum_question.AdminAccepted from forum_question 
where ID='$qID' and AuthorID='$sess->UserID'");
    $count = mysqli_num_rows($resQ);
    if($count > 0 ){
        $resA = $resQ->fetch_assoc();
        if($resA['AdminAccepted'] != 0){
            $res = [];
            $res['Status'] ='CanNotEdit';
            $res['QuestionID'] = $qID;
            echoResponse(200, $res);
        }
    }else{
        $res = [];
        $res['Status'] ='Deleted';
        $res['QuestionID'] = $qID;
        echoResponse(200, $res);
    }

    $app->db->updateRecord('forum_question',"SubjectID='".$data->Subject->ID."',QuestionText='$data->QuestionText',
        Title='$data->Title'", "ID='$qID' and AuthorID='$sess->UserID'");
    $d = $app->db->deleteFromTable('tag_question','QuestionID='.$qID);

    if(isset($data->Tags)){

        foreach($data->Tags as $tag){
            $cq = $app->db->insertToTable('tag_question',"TagID,QuestionID","'$tag->ID','$qID'");
        }
    }

    $res = [];
    $res['Status'] ='success';
    $res['QuestionID'] = $qID;

    echoResponse(200, $res);
});

$app->post('/saveQuestion', function() use ($app)  {

    $data = json_decode($_POST['formData']);

    $sess = $app->session;

    $qID = -1;

    if(isset($data->ID)){
        $qID = $data->ID;
        $app->db->updateRecord('forum_question',"SubjectID='".$data->Subject->ID."',QuestionText='$data->QuestionText',
        Title='$data->Title'", "ID='$qID'");
        $d = $app->db->deleteFromTable('tag_question','QuestionID='.$qID);
    }else{
        $qID = $app->db->insertToTable('forum_question','QuestionText,Title,SubjectID,AuthorID,CreationDate',
            "'$data->QuestionText','$data->Title','".$data->Subject->ID."','".$sess->UserID."',NOW()",true);

        if (isset($_FILES['file'])) {
            $file_ary = reArrayFiles($_FILES['file']);

            if (!file_exists('../content/user_upload/')) {
                mkdir('../content/user_upload/', 0777, true);
            }

            foreach ($file_ary as $file) {
                $filename = $file['name'];
                $rand = generateRandomString(18);
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                $destination ='content/user_upload/'.$rand.'.'.$ext;
                $uploadSuccess = move_uploaded_file( $file['tmp_name'] , '../../'.$destination );
                if($uploadSuccess){
                    $fileTypeQ = $app->db->makeQuery("select file_type.ID from file_type where file_type.TypeName='$ext'");

                    $fileTypeID = -1;
                    if(mysqli_num_rows($fileTypeQ) > 0)
                        $fileTypeID = $fileTypeQ->fetch_assoc()['ID'];

                    $fileSize = $file['size'] / 1024;
                    $fid = $app->db->insertToTable('file_storage','AbsolutePath,FullPath,Filename,IsAvatar,UserID,FileTypeID,
                FileSize,UploadDate',
                        "'$destination','../$destination','$filename','0','$sess->UserID','$fileTypeID','$fileSize',NOW()",
                        true);

                    $app->db->insertToTable('question_attachment','QuestionID,FileID',
                        "'$qID','$fid'");
                }
            }
        }
    }

    if(isset($data->Tags)){

        foreach($data->Tags as $tag){
            $cq = $app->db->insertToTable('tag_question',"TagID,QuestionID","'$tag->ID','$qID'");
        }
    }

    $res = [];
    $res['Status'] ='success';
    $res['QuestionID'] = $qID;

    echoResponse(200, $res);
});

$app->post('/getFileByID', function() use ($app)  {
$data = json_decode($app->request->getBody());

if(!isset($data->FileID))
    echoError('bad request');
$res = $app->db->makeQuery("SELECT file_storage.*  , library.ID as LibraryID , library.Title ,
forum_main_subject.Title as MainSubjectTitle , forum_subject.Title as SubjectTitle , file_type.GeneralType,
fs_user.FullPath as UserAvatar,user.FullName
FROM library
inner join file_storage on file_storage.ID=library.FileID and file_storage.FileSize > 0
left join file_type on file_type.ID=file_storage.FileTypeID
left join forum_main_subject on forum_main_subject.ID=library.MainSubjectID
left join forum_subject on forum_subject.ID=library.SubjectID
inner join user on user.ID=file_storage.UserID
left join file_storage as fs_user on user.AvatarID=fs_user.ID where library.ID = '$data->FileID' limit 1");

    echoSuccess($res->fetch_assoc());
});

$app->post('/getLibraryFiles', function() use ($app)  {
    $data = json_decode($app->request->getBody());

    $res = [];
    $pr = new Pagination($data);
    $res = $pr->getPage($app->db,"SELECT file_storage.*  , library.ID as LibraryID , library.Title ,
forum_main_subject.Title as MainSubjectTitle , forum_subject.Title as SubjectTitle , file_type.GeneralType,
fs_user.FullPath as UserAvatar,user.FullName
FROM library 
inner join file_storage on file_storage.ID=library.FileID and file_storage.FileSize > 0
left join file_type on file_type.ID=file_storage.FileTypeID
left join forum_main_subject on forum_main_subject.ID=library.MainSubjectID
left join forum_subject on forum_subject.ID=library.SubjectID
inner join user on user.ID=file_storage.UserID
left join file_storage as fs_user on user.AvatarID=fs_user.ID
order by file_storage.UploadDate desc");

    echoResponse(200, $res);
});

$app->post('/getQuestionMetaEdit', function() use ($app)  {
    //userRequire();
    require_once '../db/tag.php';
    require_once '../db/forum_subject.php';
    require_once '../db/question_attachment.php';
    $data = json_decode($app->request->getBody());
    
    $res = [];

    if(isset($data)) {
        $resQ = $app->db->makeQuery("select * from forum_question where forum_question.ID='$data->QuestionID'");
        $res['Question'] = $resQ->fetch_assoc();

        $res['Question']['Subject'] = getQuestionSubject($app->db , $data->QuestionID);
        $res['Question']['MainSubject'] = getSubjectParent($app->db , $res['Question']['Subject']['ID']);
        $res['Question']['Tags'] = getQuestionTags($app->db , $data->QuestionID);
        $res['Question']['Attachments'] = getQuestionAttachments($app->db , $data->QuestionID);
    }

    $res['AllTags'] = getAllTags($app->db);
    $res['AllSubjects'] = getAllMainSubjectsWithChilds($app->db);

    echoResponse(200, $res);
});

$app->post('/getMainForumData', function() use ($app)  {

    $data = json_decode($app->request->getBody());

    $sess = $app->session;

    $res = [];
    $resQ = $app->db->makeQuery("SELECT * ,
(SELECT count(*) FROM main_subject_follow where MainSubjectID = fms.ID and UserID = '$sess->UserID') as PersonFollow 
FROM forum_main_subject as fms WHERE SubjectName='$data->MainSubjectName'");
    $mainSubject = $resQ->fetch_assoc();
    $res['MainSubject'] = $mainSubject;
    $mainSubjectID = $mainSubject['SubjectID'];

    $resQ = $app->db->makeQuery("SELECT fs.*,
(SELECT Count(*) FROM forum_question WHERE SubjectID=fs.ID AND AdminAccepted=1) as TotalQuestions,
(SELECT count(*) FROM subject_follow where SubjectID = fs.ID) as FollowCount ,
(SELECT count(*) FROM subject_follow where SubjectID = fs.ID and UserID = '$sess->UserID') as PersonFollow ,
(SELECT Count(*) FROM forum_answer inner join forum_question on forum_question.ID=forum_answer.QuestionID WHERE 
forum_question.SubjectID=fs.ID AND forum_question.AdminAccepted=1 AND forum_answer.AdminAccepted=1) as TotalAnswers
FROM forum_subject as fs WHERE fs.ParentSubjectID='$mainSubjectID'");

    $subjectChilds = [];
    while($r = $resQ->fetch_assoc()){
        $subjectChilds[] = $r;
    }

    $res['SubjectChilds'] = $subjectChilds;

    $curDate = date('Y-m-d');
    $resCQ = $app->db->makeQuery("select * from calendar_day where calendar_day.IntervalDay='$curDate'");
    $resC  = $resCQ->fetch_assoc();

    $resQ = $app->db->makeQuery("

SELECT cd.IntervalDay as date,
  (select count(*) from forum_question
    left join forum_subject on forum_subject.ID=forum_question.SubjectID
  where forum_subject.ParentSubjectID='$mainSubjectID' 
  and forum_question.AdminAccepted=1 
  and Date(forum_question.CreationDate) < cd.IntervalDay) as IQuestionCount
  ,
  (select count(*) from forum_answer
    left join forum_question on forum_answer.QuestionID=forum_question.ID
    left join forum_subject on forum_subject.ID=forum_question.SubjectID
  where forum_subject.ParentSubjectID='$mainSubjectID' 
  and forum_answer.AdminAccepted=1
  and Date(forum_answer.CreationDate) < cd.IntervalDay) 
  as IAnswerCount,
  (select count(*) from forum_question
    left join forum_subject on forum_subject.ID=forum_question.SubjectID
  where forum_subject.ParentSubjectID='$mainSubjectID' 
  and forum_question.AdminAccepted=1 
  and Date(forum_question.CreationDate) = cd.IntervalDay) 
  as QuestionCount
  ,
  (select count(*) from forum_answer
    left join forum_question on forum_answer.QuestionID=forum_question.ID
    left join forum_subject on forum_subject.ID=forum_question.SubjectID
  where forum_subject.ParentSubjectID='$mainSubjectID'
  and forum_answer.AdminAccepted=1 
  and Date(forum_answer.CreationDate) = cd.IntervalDay)
  as AnswerCount  
  
from calendar_day as cd
where cd.ID BETWEEN ".($resC['ID'] - 10)." and ".($resC['ID']+1));

    $cqData = [];
    while($r = $resQ->fetch_assoc())
        $cqData[] = $r;
    $res['ChartData'] = $cqData;

    $res['PieChartData']=[];

    $resQ = $app->db->makeQuery("select 'سوال' as Name, count(*) as Value from forum_question
    inner join forum_subject on forum_subject.ID=forum_question.SubjectID
  where forum_subject.ParentSubjectID='$mainSubjectID' 
  and forum_question.AdminAccepted=1 
  ");
    $res['PieChartData'][0] =$resQ->fetch_assoc();

    $resQ = $app->db->makeQuery("select 'جواب' as Name, count(*) as Value from forum_answer
    inner join forum_question on forum_answer.QuestionID=forum_question.ID
    inner join forum_subject on forum_subject.ID=forum_question.SubjectID
  where forum_subject.ParentSubjectID='$mainSubjectID'
  and forum_answer.AdminAccepted=1 and forum_question.AdminAccepted=1");
    $res['PieChartData'][1] =$resQ->fetch_assoc();

    $resQ = $app->db->makeQuery("select 
admin.UserID ,user.FullName ,user.Email ,user.Tel, file_storage.FullPath as Image 
from admin_permission 
left join admin on admin.PermissionID=admin_permission.ID
inner join user on user.ID =admin.UserID
left join file_storage on file_storage.ID=user.AvatarID
LEFT JOIN forum_main_subject on forum_main_subject.ID = admin.ForumID
where admin_permission.Permission='$data->MainSubjectName' or forum_main_subject.SubjectName = '$data->MainSubjectName'");

    $admins = [];
    while($r = $resQ->fetch_assoc())
        $admins[] =$r;
    $res["Admins"] = $admins;

    echoResponse(200, $res);
});

$app->post('/getSubForumData', function() use ($app)  {

    $data = json_decode($app->request->getBody());


    $resQ = $app->db->makeQuery("SELECT forum_subject.*,forum_main_subject.Title as MainTitle,
  forum_main_subject.SubjectName as MainSubjectName
  FROM forum_subject left join 
    forum_main_subject on   forum_main_subject.SubjectID=forum_subject.ParentSubjectID WHERE  forum_subject
    .ID='$data->SubjectID'");

    $res = [];
    $res['Subject'] = $resQ->fetch_assoc();


    $curDate = date('Y-m-d');
    $resCQ = $app->db->makeQuery("select * from calendar_day where calendar_day.IntervalDay='$curDate'");
    $resC  = $resCQ->fetch_assoc();

    $resQ = $app->db->makeQuery("
SELECT cd.IntervalDay as date,
  (select count(*) from forum_question
  where forum_question.SubjectID='$data->SubjectID' 
  and forum_question.AdminAccepted=1 
  and Date(forum_question.CreationDate) < cd.IntervalDay) 
  as 'IQuestionCount'
  ,
  (select count(*) from forum_answer
    left join forum_question on forum_answer.QuestionID=forum_question.ID
  where forum_question.SubjectID='$data->SubjectID' 
  and forum_answer.AdminAccepted=1 
  and Date(forum_answer.CreationDate) < cd.IntervalDay) 
  as 'IAnswerCount',
  (select count(*) from forum_question
  where forum_question.SubjectID='$data->SubjectID' 
  and forum_question.AdminAccepted=1 
  and Date(forum_question.CreationDate) = cd.IntervalDay) 
  as 'QuestionCount'
  ,
  (select count(*) from forum_answer
    left join forum_question on forum_answer.QuestionID=forum_question.ID
  where forum_question.SubjectID='$data->SubjectID' 
  and forum_answer.AdminAccepted=1 
  and Date(forum_answer.CreationDate) = cd.IntervalDay) 
  as 'AnswerCount'

from calendar_day as cd
where cd.ID BETWEEN ".($resC['ID'] - 10)." and ".($resC['ID']+1));

    $cqData = [];
    while($r = $resQ->fetch_assoc())
        $cqData[] = $r;
    $res['ChartData'] = $cqData;

    echoResponse(200, $res);
});

$app->post('/getLastFollowingQuestions', function() use ($app)  {


    $data = json_decode($app->request->getBody());
    $sess = $app->session;
    $pageRes = [];
    $pageRes['Action'] = 'Get Following Questions';
    $pageRes['Items'] = [];

    $rateSelection =
        "(SELECT count(*) from forum_question where forum_question.AuthorID=u.ID and (forum_question.CreationDate > NOW() - 
 INTERVAL 7 DAY)) + (SELECT count(*) from forum_answer where forum_answer.AuthorID=u.ID)+
 (SELECT count(*)/2 from question_view where question_view.UserID=u.ID and (question_view.ViewDate > NOW() - 
 INTERVAL 7 DAY))";

    if(isset($data->MainSubjectName)){

        $resQ = $app->db->makeQuery("SELECT forum_main_subject.SubjectID FROM forum_main_subject WHERE 
                                forum_main_subject.SubjectName='$data->MainSubjectName' ");

        $subjectID= $resQ->fetch_assoc()['SubjectID'];
        $pageRes['Total'] = $app->db->makeQuery("
SELECT count(*) as Total 
FROM (
  (SELECT forum_question.*
   FROM question_follow
     inner JOIN user ON user.ID = question_follow.UserID
     inner JOIN forum_question ON forum_question.ID = question_follow.QuestionID
        AND forum_question.AdminAccepted=1 and question_follow.UserID='$sess->UserID'
  )
  UNION
  (SELECT forum_question.*
   FROM person_follow
     LEFT JOIN user ON user.ID = person_follow.TargetUserID
     LEFT JOIN forum_question ON forum_question.AuthorID = user.ID
     LEFT JOIN forum_subject ON forum_subject.ID = forum_question.SubjectID
   WHERE person_follow.UserID = '$sess->UserID'
         AND forum_subject.ParentSubjectID = '$subjectID'
  )
  UNION
  (
  SELECT forum_question.*
FROM main_subject_follow
  LEFT JOIN forum_subject ON forum_subject.ParentSubjectID=main_subject_follow.MainSubjectID
  LEFT JOIN forum_question ON forum_question.SubjectID=forum_subject.ID
 WHERE main_subject_follow.UserID = '$sess->UserID'
      AND main_subject_follow.MainSubjectID = '$subjectID'
  )
  UNION
  (SELECT forum_question.*
FROM subject_follow
  LEFT JOIN forum_subject ON forum_subject.ID=subject_follow.SubjectID
  LEFT JOIN forum_question ON forum_question.SubjectID=forum_subject.ID
 WHERE subject_follow.UserID = '$sess->UserID'
      AND forum_subject.ParentSubjectID='$subjectID'
      AND forum_question.AdminAccepted=1
  )
) res ORDER BY res.CreationDate DESC")->fetch_assoc()['Total'];

        $offset = ($data->pageIndex - 1) * $data->pageSize;

        $resQ = $app->db->makeQuery("SELECT DISTINCT res.* , file_storage.FullPath as Image, u.FullName , u.score, ($rateSelection) as Rate,
 (SELECT count(*) from forum_answer where forum_answer.QuestionID=res.ID) as 'AnswersCount' ,
 
(SELECT count(*) FROM question_view where QuestionID = res.ID) 
 as 'ViewCount' ,
 (SELECT question_view.ID from question_view 
        where question_view.QuestionID=res.ID AND question_view.UserID=$sess->UserID LIMIT 1) as 'QViewID'
FROM (
  (SELECT forum_question.*
   FROM question_follow
     inner JOIN user ON user.ID = question_follow.UserID
     inner JOIN forum_question ON forum_question.ID = question_follow.QuestionID
        AND forum_question.AdminAccepted=1 and question_follow.UserID='$sess->UserID'
  )
  UNION
  (SELECT forum_question.*
   FROM person_follow
     LEFT JOIN user ON user.ID = person_follow.TargetUserID
     LEFT JOIN forum_question ON forum_question.AuthorID = user.ID
     LEFT JOIN forum_subject ON forum_subject.ID = forum_question.SubjectID
   WHERE person_follow.UserID = '$sess->UserID'
         AND forum_subject.ParentSubjectID = '$subjectID'
        AND forum_question.AdminAccepted=1
  )
  UNION
  (SELECT forum_question.*
FROM main_subject_follow
  LEFT JOIN forum_subject ON forum_subject.ParentSubjectID=main_subject_follow.MainSubjectID
  LEFT JOIN forum_question ON forum_question.SubjectID=forum_subject.ID
 WHERE main_subject_follow.UserID = '$sess->UserID'
      AND main_subject_follow.MainSubjectID = '$subjectID'
      AND forum_question.AdminAccepted=1
  )
  UNION
  (SELECT forum_question.*
FROM subject_follow
  LEFT JOIN forum_subject ON forum_subject.ID=subject_follow.SubjectID
  LEFT JOIN forum_question ON forum_question.SubjectID=forum_subject.ID
 WHERE subject_follow.UserID = '$sess->UserID'
      AND forum_subject.ParentSubjectID='$subjectID'
      AND forum_question.AdminAccepted=1
  )
) as res 
LEFT JOIN user as u on u.ID=res.AuthorID
LEFT JOIN file_storage on file_storage.ID=u.AvatarID
ORDER BY res.CreationDate DESC
"." LIMIT $offset, $data->pageSize");

        while($r = $resQ->fetch_assoc())
            $pageRes['Items'][] = $r;

    }else if(isset($data->SubjectID)){
        $pageRes['Total'] = $app->db->makeQuery("
SELECT count(*) as Total 
FROM (
  (SELECT forum_question.*
   FROM question_follow
     inner JOIN user ON user.ID = question_follow.UserID
     inner JOIN forum_question ON forum_question.ID = question_follow.QuestionID
        AND forum_question.AdminAccepted=1 and question_follow.UserID='$sess->UserID'
  )
  UNION
  (SELECT forum_question.*
   FROM person_follow
     LEFT JOIN user ON user.ID = person_follow.TargetUserID
     LEFT JOIN forum_question ON forum_question.AuthorID = user.ID
   WHERE person_follow.UserID = '$sess->UserID'
         AND forum_question.SubjectID = '$data->SubjectID'
        AND forum_question.AdminAccepted=1
  )
  UNION
  (SELECT forum_question.*
FROM subject_follow
  LEFT JOIN forum_subject ON forum_subject.ID=subject_follow.SubjectID
  LEFT JOIN forum_question ON forum_question.SubjectID=forum_subject.ID
 WHERE subject_follow.UserID = '$sess->UserID'
      AND subject_follow.SubjectID = '$data->SubjectID'
      AND forum_question.AdminAccepted=1
  )
) as res 
LEFT JOIN user as u on u.ID=res.AuthorID
LEFT JOIN file_storage on file_storage.ID=u.AvatarID")->fetch_assoc()['Total'];

        $offset = ($data->pageIndex - 1) * $data->pageSize;

        $resQ = $app->db->makeQuery("SELECT DISTINCT res.* , file_storage.FullPath as Image, u.FullName , u.score, 
($rateSelection) as Rate,
 (SELECT count(*) from forum_answer where forum_answer.QuestionID=res.ID) as 'AnswersCount' ,
 
(SELECT count(*) FROM question_view where QuestionID = res.ID) 
 as 'ViewCount' ,
 (SELECT question_view.ID from question_view 
        where question_view.QuestionID=res.ID AND question_view.UserID=$sess->UserID LIMIT 1) as 'QViewID'
FROM (
  (SELECT forum_question.*
   FROM question_follow
     inner JOIN user ON user.ID = question_follow.UserID
     inner JOIN forum_question ON forum_question.ID = question_follow.QuestionID
        AND forum_question.AdminAccepted=1 and question_follow.UserID='$sess->UserID'
  )
  UNION
  (SELECT forum_question.*
   FROM person_follow
     LEFT JOIN user ON user.ID = person_follow.TargetUserID
     LEFT JOIN forum_question ON forum_question.AuthorID = user.ID
   WHERE person_follow.UserID = '$sess->UserID'
         AND forum_question.SubjectID = '$data->SubjectID'
        AND forum_question.AdminAccepted=1
  )
  UNION
  (SELECT forum_question.*
FROM subject_follow
  LEFT JOIN forum_subject ON forum_subject.ID=subject_follow.SubjectID
  LEFT JOIN forum_question ON forum_question.SubjectID=forum_subject.ID
 WHERE subject_follow.UserID = '$sess->UserID'
      AND subject_follow.SubjectID = '$data->SubjectID'
      AND forum_question.AdminAccepted=1
  )
) as res 
LEFT JOIN user as u on u.ID=res.AuthorID
LEFT JOIN file_storage on file_storage.ID=u.AvatarID
ORDER BY res.CreationDate DESC
"." LIMIT $offset, $data->pageSize");

        while($r = $resQ->fetch_assoc())
            $pageRes['Items'][] = $r;

    }
    echoResponse(200, $pageRes);

});

$app->post('/getForumBestQuestions', function() use ($app)  {
    $data = json_decode($app->request->getBody());
    
    $sess = $app->session;
    $pr = new Pagination($data);

    $rateSelection = "(SELECT count(*) from forum_question where forum_question.AuthorID=u.ID and (forum_question.CreationDate > NOW() - 
 INTERVAL 7 DAY))+
 (SELECT count(*) from forum_answer where forum_answer.AuthorID=u.ID)+
 (SELECT count(*)/2 from question_view where question_view.UserID=u.ID and (question_view.ViewDate > NOW() - 
 INTERVAL 7 DAY))";

    if(isset($data->MainSubjectName)){
        $resQ = $app->db->makeQuery("SELECT forum_main_subject.SubjectID FROM forum_main_subject WHERE 
                                forum_main_subject.SubjectName='$data->MainSubjectName' ");

        $subjectID= $resQ->fetch_assoc()['SubjectID'];
        $offset = $pr->calculateOffset();

        $query = "SELECT q.* from (SELECT u.score,u.FullName,forum_question.`ID`, `QuestionText`, forum_question
        .`Title`, `AuthorID`, `CreationDate`, `FullPath` as Image ,
 (SELECT count(*) from forum_answer where forum_answer.QuestionID=forum_question.ID and forum_answer.AdminAccepted=1)
   as 'AnswersCount' ,
(SELECT count(*) FROM question_view where QuestionID = forum_question.ID and forum_question.AdminAccepted=1 ) 
   as 'ViewCount' ,
 (SELECT question_view.ID from question_view 
        where question_view.QuestionID=forum_question.ID AND question_view.UserID=$sess->UserID LIMIT 1) as 'QViewID' ,
 ($rateSelection) as Rate,
 (SELECT sum(question_rate.RateValue) FROM question_rate 
  where question_rate.QuestionID=forum_question.ID and question_rate.QuestionRateDate > NOW() - INTERVAL 30 DAY) 
  as 'QScoreInterval',
 (SELECT sum(question_rate.RateValue) FROM question_rate 
  where question_rate.QuestionID=forum_question.ID) 
  as 'QScore'
 FROM forum_question 
 LEFT JOIN user as u on u.ID=forum_question.AuthorID 
 LEFT JOIN file_storage on file_storage.ID=u.AvatarID 
 LEFT JOIN forum_subject on forum_subject.ID=forum_question.SubjectID 
 WHERE forum_question.AdminAccepted='1' AND forum_subject.ParentSubjectID='$subjectID' ) as q
 order by q.ViewCount desc limit $offset , $data->pageSize";

        $pageResQ = $app->db->makeQuery($query);
        $items = [];
        while($r = $pageResQ->fetch_assoc()){
            $items[] = $r;
        }

        $total = $app->db->makeQuery("SELECT count(*) as Total
 FROM forum_question 
 LEFT JOIN user as u on u.ID=forum_question.AuthorID 
 LEFT JOIN file_storage on file_storage.ID=u.AvatarID 
 LEFT JOIN forum_subject on forum_subject.ID=forum_question.SubjectID 
 WHERE forum_question.AdminAccepted='1' AND forum_subject.ParentSubjectID='$subjectID' ")->fetch_assoc()['Total'];
        $pageRes =[];
        $pageRes['Items'] = $items;
        $pageRes['Total'] = $total;
        $pageRes['PageSize'] = $data->pageSize;
        $pageRes['PageIndex'] = $data->pageIndex;
        echoResponse(200, $pageRes);
    }
    else if(isset($data->SubjectID)){

        $offset = $pr->calculateOffset();

        $query = "SELECT q.* from (SELECT u.score,u.FullName,forum_question.`ID`, `QuestionText`, forum_question
        .`Title`, 
        `AuthorID`, `CreationDate`,
`FullPath` as Image ,
 (SELECT count(*) from forum_answer where forum_answer.QuestionID=forum_question.ID and forum_answer.AdminAccepted=1)
   as 'AnswersCount' ,
(SELECT count(*) FROM question_view where QuestionID = forum_question.ID and forum_question.AdminAccepted=1) as 
'ViewCount' ,
 (SELECT question_view.ID from question_view 
        where question_view.QuestionID=forum_question.ID AND question_view.UserID=$sess->UserID LIMIT 1) as 'QViewID' ,
 ($rateSelection) as Rate,
 (SELECT sum(question_rate.RateValue) FROM question_rate where question_rate.QuestionID=forum_question.ID
 and question_rate.QuestionRateDate > NOW() - INTERVAL 30 DAY) as 'QScoreInterval',
 (SELECT sum(question_rate.RateValue) FROM question_rate 
  where question_rate.QuestionID=forum_question.ID) as 'QScore'
 FROM forum_question 
 LEFT JOIN user as u on u.ID=forum_question.AuthorID 
 LEFT JOIN file_storage on file_storage.ID=u.AvatarID 
 LEFT JOIN forum_subject on forum_subject.ID=forum_question.SubjectID 
 WHERE forum_question.AdminAccepted='1' AND forum_subject.ID='$data->SubjectID)' ) as q
 order by q.ViewCount desc limit $offset , $data->pageSize";

        $pageResQ = $app->db->makeQuery($query);
        $items = [];
        while($r = $pageResQ->fetch_assoc()){
            $items[] = $r;
        }

        $total = $app->db->makeQuery("SELECT count(*) as Total
 FROM forum_question 
 LEFT JOIN user as u on u.ID=forum_question.AuthorID 
 LEFT JOIN file_storage on file_storage.ID=u.AvatarID 
 LEFT JOIN forum_subject on forum_subject.ID=forum_question.SubjectID 
 WHERE forum_question.AdminAccepted='1' AND forum_subject.ID='$data->SubjectID' ")->fetch_assoc()['Total'];
        $pageRes =[];
        $pageRes['Items'] = $items;
        $pageRes['Total'] = $total;
        $pageRes['PageSize'] = $data->pageSize;
        $pageRes['PageIndex'] = $data->pageIndex;
        echoResponse(200, $pageRes);
    }

    echoError('Subject ID is not found.');

});

$app->post('/getForumBestAnswers', function() use ($app)  {
    $data = json_decode($app->request->getBody());


    $sess = $app->session;
    $pr = new Pagination($data);

    $rateSelection = "(SELECT count(*) from forum_question where forum_question.AuthorID=u.ID and (forum_question.CreationDate > NOW() - 
 INTERVAL 7 DAY))+
 (SELECT count(*) from forum_answer where forum_answer.AuthorID=u.ID)+
 (SELECT count(*)/2 from question_view where question_view.UserID=u.ID and (question_view.ViewDate > NOW() - 
 INTERVAL 7 DAY))";

    $offset = $pr->calculateOffset();

    if(isset($data->MainSubjectName)){
        $resQ = $app->db->makeQuery("SELECT forum_main_subject.SubjectID FROM forum_main_subject WHERE 
                                forum_main_subject.SubjectName='$data->MainSubjectName' ");

        $subjectID= $resQ->fetch_assoc()['SubjectID'];

        $query = "SELECT q.* from (SELECT u.score,u.FullName,fq.`ID`,
fq.Title, fq.`CreationDate`, fq.BestAnswerID,
`FullPath` as Image ,
 (SELECT question_view.ID from question_view 
        where question_view.QuestionID=fq.ID AND question_view.UserID=$sess->UserID LIMIT 1) as 'QViewID' ,
 ($rateSelection) as Rate
 ,
(SELECT count(*) FROM question_view where QuestionID = fq.ID and fq.AdminAccepted=1) as 
'ViewCount' ,
(SELECT sum(answer_rate.RateValue) FROM answer_rate 
 Left join forum_answer on forum_answer.ID = answer_rate.AnswerID
 Left join forum_question on forum_question.ID = forum_answer.QuestionID
 where forum_question.ID=fq.ID) as 'AScore',
 (SELECT count(*) from forum_answer where forum_answer.QuestionID=fq.ID and forum_answer.AdminAccepted=1)
   as 'AnswersCount' 
 FROM forum_question as fq
 inner JOIN forum_answer on fq.BestAnswerID = forum_answer.ID 
 LEFT JOIN user as u on u.ID=fq.AuthorID 
 LEFT JOIN file_storage on file_storage.ID=u.AvatarID 
 LEFT JOIN forum_subject on forum_subject.ID=fq.SubjectID 
 WHERE fq.AdminAccepted='1' AND  forum_answer.AdminAccepted='1' 
 AND forum_subject.ParentSubjectID='$subjectID' ) as q 
 order by q.AScore desc limit $offset , $data->pageSize";

        $pageResQ = $app->db->makeQuery($query);
        $items = [];
        while($r = $pageResQ->fetch_assoc()){
            $items[] = $r;
        }

        $total = $app->db->makeQuery("SELECT count(*) as Total
 FROM forum_question 
 LEFT JOIN user as u on u.ID=forum_question.AuthorID 
 LEFT JOIN file_storage on file_storage.ID=u.AvatarID 
 LEFT JOIN forum_subject on forum_subject.ID=forum_question.SubjectID 
 WHERE forum_question.AdminAccepted='1' AND forum_subject.ParentSubjectID='$subjectID' ")->fetch_assoc()['Total'];
        $pageRes =[];
        $pageRes['Items'] = $items;
        $pageRes['Total'] = $total;
        $pageRes['PageSize'] = $data->pageSize;
        $pageRes['PageIndex'] = $data->pageIndex;
        echoResponse(200, $pageRes);
    }
    else if(isset($data->SubjectID)){

        $query = "SELECT q.* from (SELECT u.score,u.FullName,fq.`ID`,
fq.Title, fq.`CreationDate`,
`FullPath` as Image ,
 (SELECT question_view.ID from question_view 
        where question_view.QuestionID=fq.ID AND question_view.UserID=$sess->UserID LIMIT 1) as 'QViewID' ,
 ($rateSelection) as Rate
 ,
  (SELECT sum(answer_rate.RateValue) FROM answer_rate 
 Left join forum_answer on forum_answer.ID = answer_rate.AnswerID
 Left join forum_question on forum_question.ID = forum_answer.QuestionID
 where forum_question.ID=fq.ID) as 'AScore'
 ,
(SELECT count(*) FROM question_view where QuestionID = fq.ID and fq.AdminAccepted=1) as 
'ViewCount' ,
 (SELECT count(*) from forum_answer where forum_answer.QuestionID=fq.ID and forum_answer.AdminAccepted=1)
   as 'AnswersCount' 
 FROM forum_question as fq
 LEFT JOIN forum_answer  on fq.BestAnswerID = forum_answer.ID 
 LEFT JOIN user as u on u.ID=fq.AuthorID 
 LEFT JOIN file_storage on file_storage.ID=u.AvatarID 
 LEFT JOIN forum_subject on forum_subject.ID=fq.SubjectID 
 WHERE fq.AdminAccepted='1' AND  forum_answer.AdminAccepted='1' AND forum_subject.ID='$data->SubjectID' ) as q 
 order by q.AScore desc limit $offset , $data->pageSize";

        $pageResQ = $app->db->makeQuery($query);
        $items = [];
        while($r = $pageResQ->fetch_assoc()){
            $items[] = $r;
        }

        $total = $app->db->makeQuery("SELECT count(*) as Total
 FROM forum_question 
 LEFT JOIN user as u on u.ID=forum_question.AuthorID 
 LEFT JOIN file_storage on file_storage.ID=u.AvatarID 
 LEFT JOIN forum_subject on forum_subject.ID=forum_question.SubjectID 
 WHERE forum_question.AdminAccepted='1' AND forum_subject.ID='$data->SubjectID' ")->fetch_assoc()['Total'];
        $pageRes =[];
        $pageRes['Items'] = $items;
        $pageRes['Total'] = $total;
        $pageRes['PageSize'] = $data->pageSize;
        $pageRes['PageIndex'] = $data->pageIndex;
        echoResponse(200, $pageRes);
    }

    echoError('Subject ID is not found.');

});

$app->post('/getForumLastQuestions', function() use ($app)  {
    $data = json_decode($app->request->getBody());


    $sess = $app->session;
    $pr = new Pagination($data);

    $rateSelection = "(SELECT count(*) from forum_question where forum_question.AuthorID=u.ID and (forum_question.CreationDate > NOW() - 
 INTERVAL 7 DAY))+
 (SELECT count(*) from forum_answer where forum_answer.AuthorID=u.ID)+
 (SELECT count(*)/2 from question_view where question_view.UserID=u.ID and (question_view.ViewDate > NOW() - 
 INTERVAL 7 DAY))";

    if(isset($data->MainSubjectName)){
        $resQ = $app->db->makeQuery("SELECT forum_main_subject.SubjectID FROM forum_main_subject WHERE 
                                forum_main_subject.SubjectName='$data->MainSubjectName' ");

        $subjectID= $resQ->fetch_assoc()['SubjectID'];
        $query = "SELECT u.score,u.FullName,forum_question.`ID`, `QuestionText`, forum_question.`Title`, `AuthorID`, `CreationDate`,
`FullPath` as Image ,
 (SELECT count(*) from forum_answer where forum_answer.QuestionID=forum_question.ID and forum_answer.AdminAccepted=1)
   as 'AnswersCount' ,
(SELECT count(*) FROM question_view where QuestionID = forum_question.ID and forum_question.AdminAccepted=1) as 
'ViewCount' ,
 (SELECT question_view.ID from question_view 
        where question_view.QuestionID=forum_question.ID AND question_view.UserID=$sess->UserID LIMIT 1) as 'QViewID' ,
 ($rateSelection) as Rate
 FROM forum_question 
 LEFT JOIN user as u on u.ID=forum_question.AuthorID 
 LEFT JOIN file_storage on file_storage.ID=u.AvatarID 
 LEFT JOIN forum_subject on forum_subject.ID=forum_question.SubjectID 
 WHERE forum_question.AdminAccepted='1' AND forum_subject.ParentSubjectID='$subjectID' 
 order by forum_question.CreationDate desc";

        $pageRes = $pr->getPage($app->db,$query);
        echoResponse(200, $pageRes);
    }
    else if(isset($data->SubjectID)){

        $query = "SELECT u.FullName ,u.score,forum_question.`ID` ,`QuestionText`, forum_question.`Title`, `AuthorID`, 
        `CreationDate`,
`FullPath` as Image ,($rateSelection) as Rate,
 (SELECT count(*) from forum_answer where forum_answer.QuestionID=forum_question.ID and forum_answer.AdminAccepted=1) as 'AnswersCount' ,
(SELECT count(*) FROM question_view where QuestionID = forum_question.ID and forum_question.AdminAccepted=1) as 'ViewCount' ,
 (SELECT question_view.ID from question_view 
        where question_view.QuestionID=forum_question.ID AND question_view.UserID=$sess->UserID LIMIT 1) as 'QViewID' 
 FROM forum_question LEFT JOIN user as u on u.ID=forum_question.AuthorID LEFT JOIN file_storage on 
file_storage.ID=u.AvatarID LEFT  JOIN forum_subject on forum_subject.ID=forum_question.SubjectID WHERE forum_question
.AdminAccepted='1' 
AND forum_question.SubjectID='$data->SubjectID' order by forum_question.CreationDate desc";

        $pageRes = $pr->getPage($app->db,$query);
        echoResponse(200, $pageRes);
    }

    echoError('Subject ID is not found.');

});

$app->post('/getLastForumAnsweredQuestions', function() use ($app)  {
    $data = json_decode($app->request->getBody());


    $sess= $app->session;
    $pr = new Pagination($data);

    $rateSelection = "(SELECT count(*) from forum_question where forum_question.AuthorID=u.ID and (forum_question.CreationDate > NOW() - 
 INTERVAL 7 DAY))+
 (SELECT count(*) from forum_answer where forum_answer.AuthorID=u.ID)+
 (SELECT count(*)/2 from question_view where question_view.UserID=u.ID and (question_view.ViewDate > NOW() - 
 INTERVAL 7 DAY))";

    if(isset($data->MainSubjectName)){
        $resQ = $app->db->makeQuery("SELECT forum_main_subject.SubjectID FROM forum_main_subject WHERE 
                                forum_main_subject.SubjectName='$data->MainSubjectName'");

        $subjectID= $resQ->fetch_assoc()['SubjectID'];
        $query = "SELECT u.score,u.FullName, forum_question.`ID`, `QuestionText`, forum_question.`Title`,
 forum_question.`CreationDate`,`FullPath` as Image ,s.AnswersCount,
(SELECT count(*) FROM question_view where QuestionID = forum_question.ID) as 'ViewCount' ,
 ($rateSelection) as Rate,
 (SELECT question_view.ID from question_view 
        where question_view.QuestionID=forum_question.ID AND question_view.UserID=$sess->UserID LIMIT 1) as 'QViewID'
  FROM forum_question 
 LEFT JOIN user as u on u.ID=forum_question.AuthorID LEFT JOIN file_storage on file_storage.ID=u.AvatarID 
 LEFT JOIN forum_subject on forum_subject.ID=forum_question.SubjectID 
 LEFT JOIN (select count(*) as AnswersCount ,forum_answer.QuestionID from forum_answer group by forum_answer.QuestionID) s on 
 s.QuestionID=forum_question.ID 
 WHERE forum_question.AdminAccepted='1' AND forum_subject.ParentSubjectID='$subjectID' AND s.AnswersCount > 0 order 
 by s.AnswersCount 
 desc";

        $pageRes = $pr->getPage($app->db,$query);
        echoResponse(200, $pageRes);
    }
    else if(isset($data->SubjectID)){

        $query = "SELECT u.score,u.FullName, forum_question.`ID`, `QuestionText`, forum_question.`Title`,
 forum_question.`CreationDate`,`FullPath` as Image ,s.AnswersCount ,
(SELECT count(*) FROM question_view where QuestionID = forum_question.ID) as 'ViewCount' ,
($rateSelection) as Rate,
 (SELECT question_view.ID from question_view 
        where question_view.QuestionID=forum_question.ID AND question_view.UserID=$sess->UserID LIMIT 1) as 'QViewID'
 FROM forum_question 
 LEFT JOIN user as u on u.ID=forum_question.AuthorID LEFT JOIN file_storage on file_storage.ID=u.AvatarID
 LEFT JOIN (select count(*) as AnswersCount ,forum_answer.QuestionID from forum_answer group by forum_answer.QuestionID) s on 
 s.QuestionID=forum_question.ID 
 WHERE forum_question.AdminAccepted='1' AND forum_question.SubjectID='$data->SubjectID' AND s.AnswersCount > 0 order 
 by s.AnswersCount 
 desc";

        $pageRes = $pr->getPage($app->db,$query);
        echoResponse(200, $pageRes);
    }



});

$app->post('/getUserProfile', function() use ($app)  {
    //adminRequire();
    require_once '../db/education.php';
    require_once '../db/skill.php';
    require_once '../db/user_session.php';


    $sess = $app->session;


    $resQ = $app->db->makeQuery("SELECT user.`ID`,user.Description, `FullName`, `Email`, `Username`, `PhoneNumber`, `Tel`, 
    `SignupDate`,
 `Gender` , FullPath as AvatarImagePath ,
 (SELECT count(*) FROM forum_question where forum_question.AuthorID='$sess->UserID') as QuestionsCount,
 (SELECT count(*) FROM forum_answer where forum_answer.AuthorID='$sess->UserID') as AnswersCount,
 (SELECT count(*) FROM person_follow where person_follow.TargetUserID='$sess->UserID') as FollowersCount
 FROM user LEFT JOIN file_storage on file_storage.ID = AvatarID WHERE user.ID = $sess->UserID");

    $user = $resQ->fetch_assoc();

    $user['Educations'] = getUserEducations($app->db,$sess->UserID);
    $user['AllEducations'] = getAllEducations($app->db);

    $user['Skills'] = getUserSkills($app->db,$sess->UserID);
    $user['AllSkills'] = getAllSkills($app->db);

    $user['ActiveSessions'] = getAllUserActiveSessions($app->db,$sess->UserID);

    echoResponse(200, $user);
});

$app->post('/getProfile', function() use ($app)  {

    $r = json_decode($app->request->getBody());
    $session = $app->session;

    if(!$r->TargetUserID)
        echoError('bad request');

    $resQ =$app->db->makeQuery("select count(*) as val from user where ID = '$r->TargetUserID' and UserAccepted = 1");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] == 0)
        {echoError('not found');}

    $resQ = $app->db->makeQuery("select u.FullName , u.Email ,u.PhoneNumber, u.Tel , u.SignupDate ,u.Gender, u.Description ,u.score, f.FullPath , o.OrganizationName,
(SELECT count(*) FROM forum_question where AuthorID = u.ID) as QuestionsCount ,
(SELECT count(*) FROM forum_answer where AuthorID = u.ID) as AnswerCount ,
(SELECT count(*) FROM person_follow where TargetUserID = '$r->TargetUserID' and UserID = '$session->UserID' ) as PersonFollow
from user as u
inner join file_storage as f on f.ID = u.AvatarID
left join organ_position as o on u.OrganizationID = o.ID
where u.UserAccepted = 1 and u.ID = '$r->TargetUserID'");

    $resp = $resQ->fetch_assoc();
    $resQ = $app->db->makeQuery("select distinct s.Skill from skill as s
inner join user_skill as us on us.UserID = s.ID
where us.UserID = '$session->UserID'");

    $skills = [];
    while($item = $resQ->fetch_assoc())
            $skills[] = $item;
    $resp['Skills'] = $skills;

    $resQ = $app->db->makeQuery("select * from forum_question as q where q.AuthorID = '$r->TargetUserID' order by q.CreationDate desc");

    $resp['LastQuestion'] = $resQ->fetch_assoc();

    $resQ = $app->db->makeQuery("select q.* ,(SELECT sum(RateValue) FROM question_rate where QuestionID = q.ID) as QuestionRate
from forum_question as q
where q.AuthorID = '$r->TargetUserID' and q.AdminAccepted = 1
order by QuestionRate desc
");

    $resp['BestQuestion'] = $resQ->fetch_assoc();

    $curDate = date('Y-m-d');
    $resCQ = $app->db->makeQuery("select * from calendar_day where calendar_day.IntervalDay='$curDate'");
    $cid  = $resCQ->fetch_assoc()['ID'];

    $resQ = $app->db->makeQuery("

SELECT cd.IntervalDay as date,
  (select count(*) from forum_question
  where forum_question.AuthorID='$r->TargetUserID' 
  and forum_question.AdminAccepted=1 
  and Date(forum_question.CreationDate) = cd.IntervalDay) as QuestionCount
  ,
  (select count(*) from forum_answer
  where forum_answer.AuthorID='$r->TargetUserID' 
  and forum_answer.AdminAccepted=1
  and Date(forum_answer.CreationDate) = cd.IntervalDay) 
  as AnswerCount,
  (select count(*) from forum_question
  where forum_question.AuthorID='$r->TargetUserID' 
  and forum_question.AdminAccepted=1 
  and Date(forum_question.CreationDate) < cd.IntervalDay) as IQuestionCount
  ,
  (select count(*) from forum_answer
  where forum_answer.AuthorID='$r->TargetUserID' 
  and forum_answer.AdminAccepted=1
  and Date(forum_answer.CreationDate) < cd.IntervalDay) 
  as IAnswerCount
from calendar_day as cd
where cd.ID BETWEEN ".($cid - 20)." and ".($cid+1));

    $cqData = [];
    while($r = $resQ->fetch_assoc())
        $cqData[] = $r;
    $resp['ChartData'] = $cqData;


    echoSuccess($resp);
});

$app->post('/getAdminPostID', function() use ($app)  {

    $r = json_decode($app->request->getBody());


    if(!isset($r->AdminPostID))
        {echoError('bad request');}


    $resQ =$app->db->makeQuery("select count(*) as val from admin_post where ID = '$r->AdminPostID'");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] == 0)
        {echoError('not fount');}

    $resQ = $app->db->makeQuery("select q.* , u.FullName ,u.ID as UserID, u.Email ,u.score, u.Description , f.FullPath ,s.Title as Subject,ms.Title as MainSubject , ms.SubjectName , o.OrganizationName ,
(SELECT count(*) FROM forum_question where AuthorID = u.ID) as QuestionsCount ,
(SELECT count(*) FROM forum_answer where AuthorID = u.ID) as AnswerCount
from admin_post as q
inner join user as u on u.ID = q.AuthorID
inner join file_storage as f on f.ID = u.AvatarID
inner join forum_subject as s on q.SubjectID = s.ID
inner join forum_main_subject as ms on ms.ID = s.ParentSubjectID
inner join organ_position as o on u.OrganizationID = o.ID
where q.ID = '$r->AdminPostID' and f.IsAvatar = 1 ");

    $resp = $resQ->fetch_assoc();
    $resQ = $app->db->makeQuery("select fs.*,ft.GeneralType from admin_post_attachment as qt
inner join file_storage as fs on fs.ID = qt.FileID
left join file_type as ft on ft.ID=fs.FileTypeID
where qt.AdminPostID='$r->AdminPostID'");

    $aAtt = [];
    while($item = $resQ->fetch_assoc())
        $aAtt[] = $item;
    $resp['Attachments'] = $aAtt;

    echoSuccess( $resp);
});


$app->post('/getQuestionByID', function() use ($app)  {

    $r = json_decode($app->request->getBody());


    if(!isset($r->UserID) || !isset($r->QuestionID))
        {echoError('bad request');}


    $resQ =$app->db->makeQuery("select count(*) as val from forum_question where ID = '$r->QuestionID' and AdminAccepted = 1");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] == 0)
        {echoError('not fount');}

    $resQ =$app->db->makeQuery("select count(*) as val from question_view where UserID = '$r->UserID' and QuestionID = '$r->QuestionID'");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] == 0)
        {$resQ =$app->db->makeQuery("insert into question_view (UserID, QuestionID , ViewDate) values ('$r->UserID','$r->QuestionID',now())");}

    $resQ = $app->db->makeQuery("select q.* , u.FullName ,u.ID as UserID, u.Email ,u.score, u.Description , f.FullPath ,s.Title as Subject,ms.Title as MainSubject , ms.SubjectName , o.OrganizationName ,
(SELECT count(*) FROM forum_question where AuthorID = u.ID) as QuestionsCount ,
(SELECT count(*) FROM forum_answer where AuthorID = u.ID) as AnswerCount ,
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
inner join organ_position as o on u.OrganizationID = o.ID
where q.ID = '$r->QuestionID' and AdminAccepted = 1 and f.IsAvatar = 1 ");

    $resp = $resQ->fetch_assoc();
    $resQ = $app->db->makeQuery("select fs.*,ft.GeneralType from question_attachment as qt
inner join file_storage as fs on fs.ID = qt.FileID
left join file_type as ft on ft.ID=fs.FileTypeID
where qt.QuestionID='$r->QuestionID'");

    $aAtt = [];
    while($item = $resQ->fetch_assoc())
        $aAtt[] = $item;
    $resp['Attachments'] = $aAtt;

    $resQ = $app->db->makeQuery("select t.* from tag as t
inner join tag_question as tg on tg.tagID = t.ID
where tg.QuestionID = '$r->QuestionID'");

    $tags = [];
    while($item = $resQ->fetch_assoc())
            $tags[] = $item;
    $resp['Tags'] = $tags;

        $resQ = $app->db->makeQuery("SELECT fq.*, u.Email , u.FullName ,fs.FullPath FROM link_question lq
INNER JOIN forum_question fq on fq.ID = lq.LinkedQuestionID
INNER JOIN user u on u.ID = fq.AuthorID
INNER JOIN file_storage as fs on fs.ID = u.AvatarID
where lq.TargetQuestionID = '$r->QuestionID'");

    $Related = [];
    while($item = $resQ->fetch_assoc())
            $Related[] = $item;
    $resp['RelatedQuestions'] = $Related;

        $resQ = $app->db->makeQuery("select a.* , u.FullName ,u.ID as UserID, u.Email ,u.OrganizationID ,u.score, u.Description , f.FullPath, o.OrganizationName ,
(SELECT count(*) FROM forum_question where AuthorID = u.ID) as QuestionsCount ,
(SELECT count(*) FROM forum_answer where AuthorID = u.ID) as AnswerCount ,
(SELECT q.RateValue FROM answer_rate as q where q.UserID = '$r->UserID' and q.AnswerID = a.ID limit 1) as PersonAnswerRate ,
(SELECT sum(RateValue) FROM answer_rate where AnswerID = a.ID) as AnswerScore ,
(SELECT count(*) FROM person_follow where TargetUserID = a.AuthorID and UserID = '$r->UserID') as PersonFollow,
(SELECT count(*) FROM forum_question as qst where qst.ID = '$r->QuestionID' and qst.BestAnswerID = a.ID) as BestAnswer
from forum_answer as a
inner join user as u on u.ID = a.AuthorID
inner join file_storage as f on f.ID = u.AvatarID
left join organ_position as o on u.OrganizationID = o.ID
where a.QuestionID = '$r->QuestionID' and AdminAccepted = 1 and f.IsAvatar = 1 order by a.CreationDate");

    $Answers = [];
    while($item = $resQ->fetch_assoc()){
        $aID = $item['ID'];

        $resaQ = $app->db->makeQuery("select fs.*,ft.GeneralType from answer_attachment as att
inner join file_storage as fs on fs.ID = att.FileID
left join file_type as ft on ft.ID=fs.FileTypeID
where att.AnswerID='$aID'");

        $aAtt = [];
        while($itema = $resaQ->fetch_assoc())
            $aAtt[] = $itema;
        $item['Attachments'] = $aAtt;

        $Answers[] = $item;
    }
    $resp['Answers'] = $Answers;

    echoSuccess( $resp);
});

$app->post('/followMainSubject', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());

    $sess = $app->session;
    if(!isset($data->MainSubjectID))
        {echoResponse(201, 'bad request');return;}

    $resQ =$app->db->makeQuery("select count(*) as val from main_subject_follow where UserID = '$sess->UserID' and MainSubjectID = '$data->MainSubjectID'");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] > 0)
        {echoError('you followed this main subject erlier');return;}
    $resQ =$app->db->makeQuery("insert into main_subject_follow (UserID, MainSubjectID , MainSubjectFollowDate) values ('$sess->UserID','$data->MainSubjectID',now())");
    echoSuccess();
});

$app->post('/followQuestion', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());

    if(!isset($data->UserID) || !isset($data->QuestionID)|| !isset($data->AuthorID) )
        {echoResponse(201, 'bad request');return;}

    $resQ =$app->db->makeQuery("select count(*) as val from question_follow where UserID = '$data->UserID' and QuestionID = '$data->QuestionID'");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] > 0)
    {echoError('you followed this question erlier');return;}
    $resQ =$app->db->makeQuery("insert into question_follow (UserID, QuestionID , QuestionFollowDate) values ('$data->UserID','$data->QuestionID',now())");
    if($data->AuthorID != $data->UserID)
        $app->db->insertToTable('event','EventUserID,EventTypeID , EventDate , EventCauseID , EvenLinkID',"$data->AuthorID,6,now(),$data->UserID,$data->QuestionID");
    echoSuccess();
    return;
});

$app->post('/followPerson', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());

    if(!isset($data->UserID) || !isset($data->TargetUserID))
        {echoResponse(201, 'bad request');return;}

    $resQ =$app->db->makeQuery("select count(*) as val from person_follow where UserID = '$data->UserID' and TargetUserID = '$data->TargetUserID'");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] > 0)
        {echoError('you followed this question erlier');return;}
    $resQ =$app->db->makeQuery("insert into person_follow (UserID, TargetUserID , PersonFollowDate) values ('$data->UserID','$data->TargetUserID',now())");
    if($data->TargetUserID != $data->UserID)
        $app->db->insertToTable('event','EventUserID,EventTypeID , EventDate , EventCauseID',"$data->TargetUserID,7,now(),$data->UserID");
    echoSuccess();
});

$app->post('/followSubject', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());

    $sess = $app->session;
    if( !isset($data->SubjectID))
        {echoResponse(201, 'bad request');return;}

    $resQ =$app->db->makeQuery("select count(*) as val from subject_follow where UserID = '$sess->UserID' and SubjectID = '$data->SubjectID'");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] > 0)
        {echoError('you followed this subject erlier');return;}
    $resQ = $app->db->makeQuery("insert into subject_follow (UserID, SubjectID , SubjectFollowDate) values 
    ('$sess->UserID','$data->SubjectID',now())");
    echoSuccess();
});

$app->post('/unFollowSubject', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());

    $sess = $app->session;
    if( !isset($data->SubjectID))
        {echoResponse(201, 'bad request');return;}

    $app->db->deleteFromTable('subject_follow',"UserID = '$sess->UserID' and SubjectID = '$data->SubjectID'");
    echoSuccess();
});

$app->post('/unFollowMainSubject', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());

    $sess = $app->session;
    if(!isset($data->MainSubjectID))
        {echoResponse(201, 'bad request');return;}

    $app->db->deleteFromTable('main_subject_follow',"UserID = '$sess->UserID' and MainSubjectID = '$data->MainSubjectID'");
    echoSuccess();
});

$app->post('/unFollowPerson', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());

    if(!isset($data->UserID) || !isset($data->TargetUserID))
        {echoResponse(201, 'bad request');return;}

    $app->db->deleteFromTable('person_follow',"UserID = '$data->UserID' and TargetUserID = '$data->TargetUserID'");
    echoSuccess();
});

$app->post('/unFollowQuestion', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());

    if(!isset($data->UserID) || !isset($data->QuestionID))
        {echoResponse(201, 'bad request');return;}

    $app->db->deleteFromTable('question_follow',"UserID = '$data->UserID' and QuestionID = '$data->QuestionID'");
    echoSuccess();
});

$app->post('/rateQuestion', function() use ($app)  {
    $data = json_decode($app->request->getBody());

    if(!isset($data->UserID) || !isset($data->QuestionID) || !isset($data->RateValue)|| !isset($data->TargetUserID) || $data->RateValue ==0)
        {echoResponse(201, 0);return;}

    if($data->RateValue > 1)
        $data->RateValue = 1;
    else if($data->RateValue < -1)
        $data->RateValue = -1;

    $resQ =$app->db->makeQuery("select count(*) as val from question_rate where UserID = '$data->UserID' and QuestionID = '$data->QuestionID'");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] > 0)
    {
        $app->db->updateRecord('question_rate',"RateValue='$data->RateValue' , QuestionRateDate = now()" , "UserID = '$data->UserID' and QuestionID = '$data->QuestionID'");
        echoResponse(200, $data->RateValue);
        return;
    }
    else
    {
        if($data->RateValue == 1)
        {
            $app->db->updateRecord('user',"score=($data->RateValue+score)" , "ID = '$data->TargetUserID'");
            if($data->AuthorID != $data->UserID)
                $app->db->insertToTable('event','EventUserID,EventTypeID , EventDate , EventCauseID , EvenLinkID',"$data->AuthorID,2,now(),$data->UserID,$data->QuestionID");
        }
    	$app->db->insertToTable('question_rate',"UserID,QuestionID,RateValue,QuestionRateDate","'$data->UserID','$data->QuestionID','$data->RateValue',now()");
        echoResponse(200, $data->RateValue);
        return;
    }
});

$app->post('/rateAnswer', function() use ($app)  {
    $data = json_decode($app->request->getBody());

    if(!isset($data->UserID) || !isset($data->AnswerID) || !isset($data->RateValue)|| !isset($data->TargetUserID) || $data->RateValue==0)
        {echoResponse(201, 0);return;}

    if($data->RateValue > 1)
        $data->RateValue = 1;
    else if($data->RateValue < -1)
        $data->RateValue = -1;

    $resQ =$app->db->makeQuery("select count(*) as val from answer_rate where UserID = '$data->UserID' and AnswerID = '$data->AnswerID'");
    $sql =$resQ->fetch_assoc();
    if($sql['val'] > 0)
    {
        $app->db->updateRecord('answer_rate',"RateValue='$data->RateValue' , AnswerRateDate = now()" , "UserID = '$data->UserID' and AnswerID = '$data->AnswerID'");
        echoResponse(200, $data->RateValue);
        return;
    }
    else
    {
        if($data->RateValue == 1)
        {
            $app->db->updateRecord('user',"score=($data->RateValue+score)" , "ID = '$data->TargetUserID'");
            if($data->AuthorID != $data->UserID)
                $app->db->insertToTable('event','EventUserID,EventTypeID , EventDate , EventCauseID , EvenLinkID',"$data->AuthorID,1,now(),$data->UserID,$data->QuestionID");
        }
    	$app->db->insertToTable('answer_rate',"UserID,AnswerID,RateValue,AnswerRateDate","'$data->UserID','$data->AnswerID','$data->RateValue',now()");
        echoResponse(200, $data->RateValue);
        return;
    }
});

$app->post('/saveAnswer', function() use ($app)  {
    $data = json_decode($_POST['data']);

    $sess = $app->session;
    if(!isset($data->QuestionID) || !isset($data->AnswerText))
        {echoResponse(201, 'bad Request');return;}

    $aID = -1;
    if($sess->IsAdmin)
    {
        $aID = $app->db->insertToTable('forum_answer',"AuthorID,QuestionID,AnswerText,CreationDate,AdminAccepted",
            "'$sess->UserID','$data->QuestionID','$data->AnswerText',now(),1",true);
        $app->db->updateRecord('user',"score=(2+score)" , "ID = '$sess->UserID'");
    }
    else{
        $aID = $app->db->insertToTable('forum_answer',"AuthorID,QuestionID,AnswerText,CreationDate","'$sess->UserID',
        '$data->QuestionID','$data->AnswerText',now()",true);
    }

    if (isset($_FILES['file'])) {
        $file_ary = reArrayFiles($_FILES['file']);

        if (!file_exists('../content/user_upload/')) {
            mkdir('../content/user_upload/', 0777, true);
        }

        foreach ($file_ary as $file) {
            $filename = $file['name'];
            $rand = generateRandomString(18);
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            $destination ='content/user_upload/'.$rand.'.'.$ext;
            $uploadSuccess = move_uploaded_file( $file['tmp_name'] , '../../'.$destination );
            if($uploadSuccess){
                $fileTypeQ = $app->db->makeQuery("select file_type.ID from file_type where file_type.TypeName='$ext'");

                $fileTypeID = -1;
                if(mysqli_num_rows($fileTypeQ) > 0)
                    $fileTypeID = $fileTypeQ->fetch_assoc()['ID'];

                $fileSize = $file['size'] / 1024;
                $fid = $app->db->insertToTable('file_storage','AbsolutePath,FullPath,Filename,IsAvatar,UserID,FileTypeID,
                FileSize,UploadDate',
                    "'$destination','../$destination','$filename','0','$sess->UserID','$fileTypeID','$fileSize',NOW()",
                    true);

                $app->db->insertToTable('answer_attachment','AnswerID,FileID',
                    "'$aID','$fid'");
            }
        }
    }
    echoResponse(200, true);
    return;
});

$app->post('/saveMainSubject', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());

    $sess = $app->session;
    $session = $sess->getSession();

    $resQ = null;

    if(isset($data->Password)){
        $pass = passwordHash::hash($data->Password);

        $resQ = $app->db->updateRecord('user',"`FullName`='$data->FullName',`Email`='$data->Email',`Username`='$data->Username',`PhoneNumber`='$data->PhoneNumber',
`Tel`='$data->Tel',`Password`='$pass'","user.ID='".$session['ID']."'");
    }else{

        $resQ = $app->db->updateRecord('user',"`FullName`='$data->FullName',`Email`='$data->Email',`Username`='$data->Username',`PhoneNumber`='$data->PhoneNumber',
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

    $sess = $app->session;

    $q = "Description='$data->Description'".((isset($data->GenderSelected))?",Gender='$data->GenderSelected'":"");

    $app->db->updateRecord('user' ,$q ,"ID='$sess->UserID'");

    $d = $app->db->deleteFromTable('user_skill','UserID='.$sess->UserID);
    if(isset($data->Skills)){
        foreach($data->Skills as &$s){
            $cq = $app->db->insertToTable('user_skill',"UserID,SkillID","'$sess->UserID','$s->ID'");
        }
    }

    $d = $app->db->deleteFromTable('user_education','UserID='.$sess->UserID);
    if(isset($data->Educations)){
        foreach($data->Educations as &$e){
            $cq = $app->db->insertToTable('user_education',"UserID,EducationID","'$sess->UserID','$e->ID'");
        }
    }

    $res = [];
    $res["Status"] = "success";
    echoResponse(200, $res);
});

$app->post('/saveUserInfo', function() use ($app)  {
    //adminRequire();
    $data = json_decode($app->request->getBody());

    $sess = $app->session;

    $resQ = $app->db->makeQuery("select 1 from user where Email='$data->Email' and ID!='$sess->UserID' LIMIT 1");
    $count = mysqli_num_rows($resQ);
    if($count > 0){
        echoError("EmailExists");
    }

    if(isset($data->Password)){

        if(!isset($data->OldPassword)){
            echoError("OldPasswordIsNotValid");
        }else if(strlen($data->OldPassword) < 5){
            echoError("OldPasswordIsNotValid");
        }

        if(!isset($data->Password)){
            echoError("PasswordIsNotValid");
        }else if(strlen($data->Password) < 5){
            echoError("PasswordIsNotValid");
        }

        $userPassword = $app->db->makeQuery("SELECT user.Password from user where user.ID='$sess->UserID'")->fetch_assoc()['Password'];
        if(passwordHash::check_password($userPassword,$data->OldPassword)){

            $pass = passwordHash::hash($data->Password);

            $resQ = $app->db->updateRecord('user',"`FullName`='$data->FullName',`Email`='$data->Email',`Username`='$data->Username',`PhoneNumber`='$data->PhoneNumber',
`Tel`='$data->Tel',`Password`='$pass'","user.ID='$sess->UserID'");
        }else{
            echoError("OldPasswordIsNotValid");
        }
    }else{

        $resQ = $app->db->updateRecord('user',"`FullName`='$data->FullName',`Email`='$data->Email',`Username`='$data->Username',`PhoneNumber`='$data->PhoneNumber',
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

    $sess = $app->session;

    $fileID = $app->db->insertToTable('file_storage','FullPath,UploadDate,UserID,IsAvatar',"'$destination',NOW(),
    '$sess->UserID','1'",true);
    $resUpdate = $app->db->updateRecord('user',"AvatarID='$fileID'","ID='$sess->UserID'");

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

$app->post('/deleteBestAnswer', function() use ($app)  {

    $data = json_decode($app->request->getBody());

    $sess = $app->session;

    if(!isset($data->QuestionID))
        {echoResponse(201, 'bad Request');return;}

                        $resQ = $app->db->makeQuery("select a.ID from user as u
INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
WHERE a.UserID = '$sess->UserID' and (ap.PermissionLevel = 'Base' or ap.PermissionLevel = '$sess->AdminPermissionLevel')
LIMIT 1");

    $sql = $resQ->fetch_assoc();

    if($sess->IsAdmin && $sql)
    {
        $app->db->updateRecord('forum_question',"BestAnswerID = null" , "ID = '$data->QuestionID'");
        echoSuccess();
        return;
    }else{

        echoError('you dont have permission to do this action');
    }
});

$app->post('/setBestAnswer', function() use ($app)  {

    $data = json_decode($app->request->getBody());

    $sess = $app->session;

    if(!isset($data->QuestionID) || !isset($data->AnswerID))
        {echoResponse(201, 'bad Request');return;}
$resQ = $app->db->makeQuery("select a.ID from user as u
INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
WHERE a.UserID = '$sess->UserID' and (ap.PermissionLevel = 'Base' or ap.PermissionLevel = '$sess->AdminPermissionLevel')
LIMIT 1");

    $sql = $resQ->fetch_assoc();

    if($sess->IsAdmin && $sql)
    {
        $app->db->updateRecord('forum_question',"BestAnswerID = '$data->AnswerID'" , "ID = '$data->QuestionID'");
        echoSuccess();
        return;
    }else{

        echoError('you dont have permission to do this action');
    }
});

$app->post('/getUserTimeline', function() use ($app)  {

    $data = json_decode($app->request->getBody());
    $p = new Pagination($data);

    $session = $app->session;
    $offset = $p->calculateOffset();

    $total = $app->db->makeQuery("select count(*) as Total from event 
where event.EventUserID='$session->UserID'")->fetch_assoc()['Total'];

    $resQ = $app->db->makeQuery("
select
  event.ID ,event.EventDate as EventDateTime, Date( event.EventDate ) as EventDate, event_type.EventTypeFA ,
  user.FullName , file_storage.FullPath ,event.EventSeen ,event_type.EventType ,
   event.EventCauseID ,event_type.HasQuestion, forum_question.Title ,forum_question.QuestionText ,
    forum_question.ID as QuestionID , event.EventTypeID
from event
  left join user on user.ID=event.EventCauseID
  left join file_storage on file_storage.ID=user.AvatarID
  left join event_type on event_type.ID=event.EventTypeID
  left join forum_question on forum_question.ID=event.EvenLinkID
where event.EventUserID='$session->UserID'
order by Date(event.EventDate) desc
limit $offset,$data->pageSize");

    $cqData = [];
    while($r = $resQ->fetch_assoc())
        $cqData[] = $r;

    $lastDate = '';
    $i = 0;
    foreach ($cqData as $value){
        if($lastDate != $value['EventDate']){
            $dateItem =[];
            $dateItem['CID'] = $value['ID'];
            $dateItem['EventDate'] = $value['EventDate'];
            $dateItem['EventType'] = 'DayBP';
            if($i == 0)
                array_splice($cqData, 0, 0, array($dateItem));
            else
                array_splice($cqData, $i+1, 0, array($dateItem));
            $lastDate= $value['EventDate'];
        }
        $i++;
    }

    if($total <= $offset + $data->pageSize){
        $dateItem =[];
        $dateItem['CID'] = 1;
        $dateItem['EventTypeID'] = '-1';
        $dateItem['EventType'] = 'SU';
        $dateItem['User'] =
            $app->db->makeQuery("select user.SignupDate from user where ID='$session->UserID'")->fetch_assoc();
        array_push($cqData, $dateItem);
    }

    $page = [];
    $page['Items'] = $cqData;
    $page['Total'] = $total;
    $page['PageSize'] = $data->pageSize;
    $page['PageIndex'] = $data->pageIndex;

    echoResponse(200, $page);
});

$app->post('/getAllAdminsForAbout', function() use ($app)  {

    //$data = json_decode($app->request->getBody());

    $resQ = $app->db->makeQuery("
select user.FullName, user.Username , user.Email , user.PhoneNumber , user.Tel ,admin.UserID,
forum_main_subject.Title , admin_permission.PermissionLevel,forum_main_subject.SubjectName
from user
inner join admin on admin.UserID=user.ID
left join admin_permission on admin_permission.ID=admin.PermissionID
left join forum_main_subject on forum_main_subject.SubjectID=admin_permission.MainSubjectID");

    $items = [];
    while($r = $resQ->fetch_assoc()){
        $items[] = $r;
    }

    $resp = [];
    $resp['Admins'] = $items;

    echoResponse(200, $resp);
});
?>