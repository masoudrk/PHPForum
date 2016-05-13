<?php
/**
 * Created by PhpStorm.
 * User: -MR-
 * Date: 10/05/2016
 * Time: 12:06 PM
 */

function getAllTags($db){

    $resQ = $db->makeQuery("SELECT tag.* FROM tag");

    $arr = [];
    while($r = $resQ->fetch_assoc())
        $arr[] = $r;

    return $arr;
}

function getQuestionTags($db ,$qID){

    $resQ = $db->makeQuery("SELECT tag.* FROM tag_question 
            LEFT JOIN tag ON tag_question.TagID = tag.ID WHERE tag_question.QuestionID = '$qID'");

    $arr = [];
    while($r = $resQ->fetch_assoc())
        $arr[] = $r;

    return $arr;
}

?>