<?php

$app->post('/logout', function() use ($app)  {
    $sess = $app->session;
    $app->db->deleteFromTable('user_session',"UserID='$sess->UserID' AND SessionID='$sess->SSN'");
    $res = $sess->destroySession();
    echoResponse(200, $res);
});

$app->post('/getSiteInfo', function() use ($app)  {

    $resQ =$app->db->makeQuery("select * from site_config limit 1");
    echoResponse(200, $resQ->fetch_assoc());
});
?>