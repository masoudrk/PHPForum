<?php
/**
 * Created by PhpStorm.
 * User: HI YOU
 * Date: 2/24/2017
 * Time: 11:51 AM
 */

$app->post('/Survey/getAllSurveyTypes', function() use ($app)  {

    $sess = $app->session;
    $data = json_decode($app->request->getBody());
    $res = $app->db->getRecords("SELECT * from survey_type");
    echoSuccess($res);
});

$app->post('/Survey/saveSurvey', function() use ($app)  {

    $data = json_decode($app->request->getBody());
    $sess = $app->session;
    $resQ = $app->db->makeQuery("select ap.ID as val from user as u INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
where u.ID = '$sess->UserID' and ap.PermissionLevel = 'Base' limit 1");

    $sql =$resQ->fetch_assoc();
    if(!$sql)
        echoError('You don\'t have permision to do this action');

    $surveyID = $app->db->insertToTable('survey','CreationDate,UserID,ExpireDate,StartDate,Name,Description,Title,SurveyTypeID',
        "now(),'$sess->UserID','$data->ExpireDate','$data->StartDate','$data->Name','$data->Description','$data->Title','$data->SurveyTypeID'",true);
    if($surveyID){
        foreach ($data->Options as $item) {
            $app->db->insertToTable('survey_options','SurveyID,SurveyText',
                "'$surveyID','$item->SurveyText'");
        }
        echoSuccess();
    }
    else
        echoError();
    echoSuccess();
});

$app->post('/Survey/getAllSurvey', function() use ($app)  {

    $data = json_decode($app->request->getBody());
    $sess = $app->session;
    $resQ = $app->db->makeQuery("select ap.ID as val from user as u INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
where u.ID = '$sess->UserID' and ap.PermissionLevel = 'Base' limit 1");

    $sql =$resQ->fetch_assoc();
    if(!$sql)
        echoError('You don\'t have permision to do this action');

    $pr = new Pagination($data);
    $resq = $pr->getPage($app->db,"SELECT p.* ,st.SurveyTypeName , (SELECT IF((p.`ExpireDate` > NOW()) and (p.`StartDate` < NOW()), 'true', 'false') )  AS SurveyState FROM `survey` p 
 INNER JOIN survey_type st on st.ID = p.SurveyTypeID 
 INNER JOIN user u on u.id = p.UserID ORDER BY p.ID");
    if($resq)
        echoResponse(200,$resq);
    else
        echoError();
    echoSuccess();
});

$app->post('/Survey/deleteSurvey', function() use ($app)  {

    $data = json_decode($app->request->getBody());
    $sess = $app->session;
    $resQ = $app->db->makeQuery("select ap.ID as val from user as u INNER JOIN admin as a on a.UserID = u.ID
INNER JOIN admin_permission ap on ap.ID = a.PermissionID
where u.ID = '$sess->UserID' and ap.PermissionLevel = 'Base' limit 1");

    $sql =$resQ->fetch_assoc();
    if(!$sql)
        echoError('You don\'t have permision to do this action');

    $app->db->deleteFromTable('survey','ID='.$data->surveyID);
//    $resQ = $app->db->getOneRecord("SELECT * FROM survey_options WHERE SurveyID =".$data->surveyID);
    $app->db->deleteFromTable('survey_options','SurveyID='.$data->surveyID);

    echoSuccess();
});