<?php
/**
 * Created by PhpStorm.
 * User: -MR-
 * Date: 10/05/2016
 * Time: 12:06 PM
 */


function getAuthorQuestions($db ,$uID){

    $resQ = $db->makeQuery("SELECT forum_question.* FROM forum_question WHERE forum_question.AuthorID = '$uID'");

    $arr = [];
    while($r = $resQ->fetch_assoc())
        $arr[] = $r;

    return $arr;
}

function deleteQuestion($db ,$qID,$uid){

    $resQ = $db->deleteFromTable('forum_question',"AuthorID='$uid' AND ID='$qID'");
    return $resQ;
}

function getPageAuthorQuestions($db ,$uID,$pin){
    $pr = new Pagination(null);
    $pr->setParams($pin);

    $res= $pr->getPage($db, "SELECT forum_question.* FROM forum_question WHERE forum_question.AuthorID = '$uID' ORDER BY CreationDate ASC");
    foreach($res['Items'] as &$qs){

        $cq = $db->makeQuery('Select forum_subject.Title from forum_subject where forum_subject.ID='.$qs['SubjectID']);
        $qs['SubjectTitle'] = $cq->fetch_assoc()['Title'];
    }
    return $res;
}
?>