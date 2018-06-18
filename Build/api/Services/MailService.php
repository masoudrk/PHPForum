<?php
/**
 * Created by PhpStorm.
 * User: milad
 * Date: 6/18/18
 * Time: 10:25 PM
 */

define('ADMIN_EMAIL', 'sepanta-domainadmin@sepantarai.com');
define('X_MAILER', 'sepantarai.com');

function sendEmail(string $to, string $subject, string $message)
{

    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From: ' . ADMIN_EMAIL . "\r\n" .
        'Reply-To: ' . ADMIN_EMAIL . "\r\n" .
        'X-Mailer: ' . X_MAILER;

    return mail($to, $subject, $message, $headers);
}

function sendActivatedAccountEmail($to, $fullName)
{
    $subject = 'account activated - Sepantarai.com';
    $message = '
                <html>
                <head>
                  <title></title>
                </head>
                <body>
<p style="direction: rtl">سلام ' . $fullName . '</p><br>
                <p style="direction: rtl">
اکانت شما تا??د شده . شما م? توان?د در انجمن شروع به فعال?ت کن?د
<br>
' . $to . '
                </p>
                </body>
                </html>
';

    sendEmail($to, $subject, $message);
}

function sendEmailMessage($to, $title, $message)
{

    $subject = $title . ' - Sepantarai.com';
    $message = '
                        <html>
                        <head>
                          <title></title>
                        </head>
                        <body>
                        <p style="direction: rtl">' . $title . '
                        </p>
                        <br>
                        <br>
                        <p style="direction: rtl">' . $message . '
                        </p>
                        </body>
                        </html>
        ';

    sendEmail($to, $subject, $message);
}

function sendForgetPasswordEmail($to, $newPassword, $fullName)
{

    $subject = 'password changed - Sepantarai.com';
    $message = '
                <html>
                <head>
                  <title></title>
                </head>
                <body>
<p style="direction: rtl">سلام ' . $fullName . '</p><br>
                <p style="direction: rtl">پسورد جدید شما :
<br>' . $newPassword . '
<br> ایمیل شما:
' . $to . '
                </p>
                </body>
                </html>
';
    sendEmail($to, $subject, $message);
}

function sendConfirmEmail($to, $fullName, $linkID)
{

    $subject = 'confirm your email - Sepantarai.com';
    $message = '
                <html>
                <head>
                  <title></title>
                </head>
                <body>
                <p style="direction: rtl">سلام ' . $fullName . '! لطفا برای فعال سازی حساب کاربری خود روی لینک زیر کلیک
                 کنید:
<br>
                 ----> <a href="http://www.sepantarai.com/verify.php?link=' . $linkID . '"> Verify account link </a> 
                 <----
                </p>
<p style="direction: rtl">
بعد از تایید مدیر، شما می توانید وارد انجمن شوید
                </p>
                </body>
                </html>
';

    sendEmail($to, $subject, $message);
}