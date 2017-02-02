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

function getAllMainSubjects($db){

    $resQ = $db->makeQuery("SELECT forum_main_subject.* FROM forum_main_subject ");

    $arr = [];
    while($r = $resQ->fetch_assoc())
        $arr[] = $r;

    return $arr;
}

function getAllMainSubjectsWithChilds($db){

    $resQ = $db->makeQuery("SELECT forum_main_subject.Title, forum_main_subject.SubjectID  FROM forum_main_subject ");

    $arr = [];
    while($r = $resQ->fetch_assoc()){
        $childs = getSubjectChilds($db,$r['SubjectID']);
        $r['Childs'] = $childs;
        $arr[] = $r;
    }

    return $arr;
}

function getSubjectChilds($db,$parentID){

    $resQ = $db->makeQuery("SELECT forum_subject.* FROM forum_subject where forum_subject.ParentSubjectID='$parentID'");

    $arr = [];
    while($r = $resQ->fetch_assoc())
        $arr[] = $r;

    return $arr;
}

function getSubjectParent($db,$childID){

    $resQ = $db->makeQuery("SELECT forum_main_subject.Title, forum_main_subject.SubjectID FROM forum_main_subject 
                          LEFT JOIN forum_subject on forum_subject.ParentSubjectID=forum_main_subject.SubjectID where 
                          forum_subject.ID='$childID'");

    return $resQ->fetch_assoc();
}

function getQuestionSubject($db ,$qID){

    $resQ = $db->makeQuery("select forum_subject.* from forum_question left join forum_subject on forum_subject
    .ID=forum_question.SubjectID
where
forum_question.ID='$qID'");

    return $resQ->fetch_assoc();
}

function getAdminPostSubject($db ,$apID){

    $resQ = $db->makeQuery("select forum_subject.* from admin_post 
left join forum_subject on forum_subject.ID=admin_post.SubjectID
where admin_post.ID='$apID'");

    return $resQ->fetch_assoc();
}

?>