<?php
/**
 * Created by PhpStorm.
 * User: HI YOU
 * Date: 2/24/2017
 * Time: 11:51 AM
 */

$app->post('/Attachments/getAllAttachments', function () use ($app) {
    $data = json_decode($app->request->getBody());
    checkPermission($app->db, $app->session, ['Base', 'AssessmentManager']);//Base admin

    if (isset($data->QuestionID))
        echoSuccess($app->db->getRecords("SELECT * FROM `file_storage` fs inner join question_attachment qa on qa.FileID = fs.ID
where qa.QuestionID = $data->QuestionID"));
    else if (isset($data->AnswerID))
        echoSuccess($app->db->getRecords("SELECT * FROM `file_storage` fs inner join answer_attachment qa on qa.FileID = fs.ID
where qa.AnswerID = $data->AnswerID"));
    else
        echoError("Invalid Input");
});