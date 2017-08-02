<?php
/**
 * Created by PhpStorm.
 * User: HI YOU
 * Date: 2/24/2017
 * Time: 11:51 AM
 */

$app->post('/Assessment/addOrUpdateAssessmentEducation', function () use ($app) {

    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session,['Base','AssessmentManager']);//Base admin
    if (isset($data->ID)) {
        //update
        $app->db->updateRecord('assessment_education', "NameFA='$data->NameFA'"
            , "ID='$data->ID'");

        echoSuccess('آپدیت شد');
    } else {
        //insert
        $app->db->insertToTable('assessment_education', 'NameFA',
            "'$data->NameFA'");
        echoSuccess('وارد شد');
    }
});

$app->post('/Assessment/addOrUpdateAssessmentEducationLevel', function () use ($app) {

    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session,['Base','AssessmentManager']);//Base admin
    if (isset($data->ID)) {
        //update
        $app->db->updateRecord('assessment_education_level', "NameFA='$data->NameFA'"
            , "ID='$data->ID'");
        echoSuccess('آپدیت شد');
    } else {
        //insert
        $app->db->insertToTable('assessment_education_level', 'NameFA',
            "'$data->NameFA'");
        echoSuccess('وارد شد');
    }
});

$app->post('/Assessment/addOrUpdateAssessmentSystem', function () use ($app) {

    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session,['Base','AssessmentManager']);//Base admin
    if (isset($data->ID)) {
        //update
        $app->db->updateRecord('assessment_systems', "SystemName='$data->SystemName'"
            , "ID='$data->ID'");
        echoSuccess('آپدیت شد');
    } else {
        //insert
        $app->db->insertToTable('assessment_systems', 'SystemName',
            "'$data->SystemName'");
        echoSuccess('وارد شد');
    }
});

$app->post('/Assessment/addOrUpdateAssessmentJob', function () use ($app) {

    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session,['Base','AssessmentManager']);//Base admin
    if (isset($data->ID)) {
        //update
        $app->db->updateRecord('assessment_job', "NameFA='$data->NameFA'"
            , "ID='$data->ID'");
        echoSuccess('آپدیت شد');
    } else {
        //insert
        $app->db->insertToTable('assessment_job', 'NameFA',
            "'$data->NameFA'");
        echoSuccess('وارد شد');
    }
});

$app->post('/Assessment/deleteAssessmentEducation', function () use ($app) {
    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session,['Base','AssessmentManager']);//Base admin
    $app->db->deleteFromTable('assessment_education', 'ID=' . $data->ID);
    echoSuccess('پاک شد');
});

$app->post('/Assessment/deleteAssessmentEducationLevel', function () use ($app) {
    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session,['Base','AssessmentManager']);//Base admin
    $app->db->deleteFromTable('assessment_education_level', 'ID=' . $data->ID);
    echoSuccess('پاک شد');
});

$app->post('/Assessment/deleteAssessmentSystem', function () use ($app) {
    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session,['Base','AssessmentManager']);//Base admin
    $app->db->deleteFromTable('assessment_systems', 'ID=' . $data->ID);
    echoSuccess('پاک شد');
});

$app->post('/Assessment/getAllOrgans', function () use ($app) {
    checkPermission($app->db, $app->session,['Base','AssessmentManager']);//Base admin
    $res = $app->db->getRecords("SELECT so.* from organ_position so");
    echoSuccess($res);
});

$app->post('/Assessment/addOrUpdateDepo', function () use ($app) {

    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session);//Base admin
    if (isset($data->ID)) {
        //update
        $app->db->updateRecord('depo', "OrganPositionID='$data->OrganPositionID',Name='$data->Name'"
            , "ID='$data->ID'");
        echoSuccess('آپدیت شد');
    } else {
        //insert
        $app->db->insertToTable('depo', 'OrganPositionID,Name',
            "'$data->OrganPositionID','$data->Name'");
        echoSuccess('وارد شد');
    }
});

$app->post('/Assessment/deleteDepo', function () use ($app) {
    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session);//Base admin
    $app->db->deleteFromTable('depo', 'ID=' . $data->ID);
    echoSuccess('پاک شد');
});

$app->post('/Assessment/deleteJob', function () use ($app) {
    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session,['Base','AssessmentManager']);//Base admin
    $app->db->deleteFromTable('assessment_job', 'ID=' . $data->ID);
    echoSuccess('پاک شد');
});
$app->post('/Assessment/getAllJobs', function () use ($app) {
    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session,['Base','AssessmentManager']);//Base admin

    $pr = new Pagination($data);
    $resq = $pr->getPage($app->db, "SELECT *  FROM `assessment_job` ORDER BY assessment_job.ID DESC ");
    if ($resq)
        echoResponse(200, $resq);
    else
        echoError('nothing');
    echoSuccess();
});

$app->post('/Assessment/getAllEducationLevels', function () use ($app) {
    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session,['Base','AssessmentManager']);//Base admin

    $pr = new Pagination($data);
    $resq = $pr->getPage($app->db, "SELECT *  FROM `assessment_education_level` ORDER BY assessment_education_level.ID DESC ");
    if ($resq)
        echoResponse(200, $resq);
    else
        echoError('nothing');
    echoSuccess();
});


$app->post('/Assessment/getAllEducations', function () use ($app) {
    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session,['Base','AssessmentManager']);//Base admin

    $pr = new Pagination($data);
    $resq = $pr->getPage($app->db, "SELECT *  FROM `assessment_education` ORDER BY assessment_education.ID DESC ");
    if ($resq)
        echoResponse(200, $resq);
    else
        echoError('nothing');
    echoSuccess();
});

$app->post('/Assessment/getAllSystems', function () use ($app) {
    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session,['Base','AssessmentManager']);//Base admin

    $pr = new Pagination($data);
    $resq = $pr->getPage($app->db, "SELECT *  FROM `assessment_systems` ORDER BY assessment_systems.ID DESC ");
    if ($resq)
        echoResponse(200, $resq);
    else
        echoError('nothing');
    echoSuccess();
});

$app->post('/Assessment/getAllDepos', function () use ($app) {
    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session);//Base admin

    $pr = new Pagination($data);
    $resq = $pr->getPage($app->db, "SELECT depo.* ,st.OrganizationName  FROM `depo`
 INNER JOIN organ_position st on st.ID = depo.OrganPositionID ORDER BY depo.ID DESC ");
    if ($resq)
        echoResponse(200, $resq);
    else
        echoError('nothing');
    echoSuccess();
});

$app->post('/Assessment/getAllUsers', function () use ($app) {
    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session,['Base','AssessmentManager']);//Base admin
    $pr = new Pagination($data);
    $sess = new Session();

    $where = "WHERE 1=1 ";
    if (isset($data->searchValue) && strlen($data->searchValue) > 0) {
        $s = mb_convert_encoding($data->searchValue, "UTF-8", "auto");
        $where .= " AND (Username LIKE '%" . $s . "%' OR FullName LIKE '%" . $s . "%' OR Email LIKE '%" . $s . "%')";
    }
    if (isset($data->PositionID)) {
        $where .= " AND (orp.ID ='$data->PositionID' )";
    }
//
//    if ($sess->AdminPermissionLevel == "OrganAdmin") {
//        $admin = $app->db->makeQuery("select user.OrganizationID FROM user WHERE user.ID = '$sess->UserID'");
//        $admin = $admin->fetch_assoc();
//        $where .= "AND user.OrganizationID = " . $admin["OrganizationID"];
//    }

    $pageRes = $pr->getPage($app->db, "SELECT 
user.`ID`, `FullName`, `Email`, `Username` , orp.OrganizationName ,
`PhoneNumber`, `Tel`, `SignupDate`, `Gender`, user.`Description`, `SessionID`,
`MailAccepted`, `ValidSessionID`, `UserAccepted`,  admin.ID as
AdminID , user.LastActiveTime, user.SignupDate , user.score , file_storage.FullPath
FROM user LEFT JOIN admin on admin.UserID = user.ID
INNER JOIN assessment ass on ass.UserID = user.ID
inner JOIN organ_position orp on orp.ID = ass.CurrentPositionID
LEFT JOIN file_storage on file_storage.ID = user.AvatarID " . $where . " ORDER BY user.ID desc");

    foreach ($pageRes['Items'] as &$item) {
        $item['AssessmentJobInfo'] = $app->db->getRecords("SELECT aj.*, orp.OrganizationName FROM `assessment_job_record_info` aj
LEFT JOIN organ_position orp on orp.ID = aj.OrganPositionID
INNER JOIN assessment as a on a.ID = aj.AssessmentID
 WHERE a.UserID = '" . $item['ID'] . "'");
        $item["SystemExperience"] = $app->db->getRecords("SELECT ae.* FROM `assessment_system_expertise` as ae
INNER JOIN assessment as a on a.ID = ae.AssessmentID
 WHERE a.UserID = '" . $item['ID'] . "' AND ae.SystemID = '0'");

        $item["SystemExperienceDef"] = $app->db->getRecords("SELECT ass.*, ae.SystemID, ae.SystemType, ae.TrainingTime, ae.Description, ae.Score, ae.SelfScore
 FROM `assessment_systems` as ass INNER JOIN assessment_system_expertise ae on ae.SystemID = ass.ID");

        $item["Assessment"] = $app->db->getOneRecord("SELECT ass.* ,dp1.Name as Depo1Name, orp.OrganizationName,ael.NameFA,ajr.NameFA as JobRecord FROM user u INNER JOIN assessment ass on ass.UserID = u.ID 
LEFT JOIN assessment_education_level ael on ael.ID = ass.AssessmentEducationLevelID
LEFT JOIN assessment_job_record ajr on ajr.ID = ass.JobRecordID
LEFT JOIN organ_position orp on orp.ID = ass.CurrentPositionID
LEFT JOIN depo dp1 on dp1.ID = ass.DepoID
 WHERE u.ID = " . $item['ID']);
        if ($item["Assessment"])
            $item["AssessmentPositions"] = $app->db->getRecords("SELECT * FROM `assessment_positions` 
INNER JOIN organ_position op on op.ID = assessment_positions.OrganPositionID
WHERE assessment_positions.AssessmentID=" . $item["Assessment"]["ID"]);
    }
    echoResponse(200, $pageRes);
});
