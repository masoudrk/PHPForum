<?php
/**
 * Created by PhpStorm.
 * User: -MR-
 * Date: 10/05/2016
 * Time: 12:06 PM
 */

function getPageUserAnswers($db ,$uID,$pin){
    $pr = new Pagination(null);
    $pr->setParams($pin);

    $res= $pr->getPage($db,
"SELECT forum_answer.* , forum_question.Title , user.FullName , file_storage.FullPath as 
QuestionerImage 
FROM forum_answer 
INNER JOIN forum_question on forum_question.ID=forum_answer.QuestionID 
INNER JOIN user on user.ID=forum_question.AuthorID 
LEFT JOIN file_storage on file_storage.ID=user.AvatarID 
WHERE forum_answer.AuthorID = '17' ORDER BY forum_answer.CreationDate ASC");
    return $res;
}
?>