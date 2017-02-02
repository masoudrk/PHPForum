<?php
/**
 * Created by PhpStorm.
 * User: -MR-
 * Date: 10/05/2016
 * Time: 12:06 PM
 */

function getQuestionAttachments($db ,$qID){

    $resQ = $db->makeQuery("select fs.*,ft.GeneralType from question_attachment as qt
inner join file_storage as fs on fs.ID = qt.FileID
left join file_type as ft on ft.ID=fs.FileTypeID
where qt.QuestionID='$qID'");
    $arr = [];
    while($r = $resQ->fetch_assoc())
        $arr[] = $r;

    return $arr;
}

?>