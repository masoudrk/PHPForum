<?php
/**
 * Created by PhpStorm.
 * User: -MR-
 * Date: 10/05/2016
 * Time: 12:06 PM
 */

function getAllForumSubjects($db){

    $resQ = $db->makeQuery("SELECT forum_subject.* FROM forum_subject");

    $arr = [];
    while($r = $resQ->fetch_assoc())
        $arr[] = $r;

    return $arr;
}

function getQuestionSubject($db ,$qID){

    $resQ = $db->makeQuery("select forum_subject.* from forum_question left join forum_subject on forum_subject
    .ID=forum_question.SubjectID
where
forum_question.ID='$qID'");

    return $resQ->fetch_assoc();
}

?>