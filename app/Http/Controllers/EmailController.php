<?php


namespace App\Http\Controllers;

ini_set('max_execution_time', 300);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailController extends Controller
{
    public function sendMail($email, $subject, $html)
    {
        $email_settings = $this->emailRepo->getEmailSettings();
        if (!empty($email_settings)) {
            $email_settings = $email_settings[0];
            if ($email_settings['hr_emails']) {
                $from_address = '';
                $to_address = [];
                $cc_address = [];

                $hr_mails = explode(',', $email_settings['hr_emails']);
                $from_address = $hr_mails[0];

                $to_address = explode(',', $email_settings['hr_emails']);
                $to_address[] = $email;

                if ($email_settings['admin_emails']) {
                    $cc_address = explode(',', $email_settings['admin_emails']);
                }

                if ($email_settings['cto_emails']) {
                    $cto_emails = explode(',', $email_settings['cto_emails']);
                    foreach ($cto_emails as $mail) {
                        $cc_address[] = $mail;
                    }
                }

                $mail = null;
                $mail = new PHPMailer(true); // notice the \  you have to use root namespace here
                try {
                    $mail->isSMTP(); // tell to use smtp
                    //            $mail->isSendMail();
                    $mail->CharSet = "utf-8"; // set charset to utf8
                    //            $mail->SMTPDebug = 2;
                    $mail->SMTPAuth = true;  // use smpt auth
                    //            $mail->SMTPSecure = config('MAIL_ENCRYPTION'); // or ssl
                    $mail->Host = 'smtp.sendgrid.net';
                    $mail->Port = 587; // most likely something different for you. This is the mailtrap.io port i use for testing.
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );
                    $mail->Username = 'dhruvin';
                    $mail->Password = 'dhruvin@123';


                    $mail->setFrom("hr@esparkinfo.com", "eSparkBiz HR");

                    // $mail->setFrom($from_address, "eSparkBiz");

                    $mail->Subject = $subject;
                    $mail->MsgHTML($html);
                    //                    $mail->addAddress($email);

                    foreach ($to_address as $item) {
                        $mail->addAddress($item);
                    }

                    //                    $mail->addCC('info@sacramento4kids.com');
                    foreach ($cc_address as $cc) {
                        $mail->addCC($cc);
                    }

                    //                    $mail->addBCC('hr@esparkinfo.com');
                    //                    $mail->addBCC('webdeveloper1011@gmail.com');
                    $mail->send();
                } catch (phpmailerException $e) {
                    return 0;
                } catch (Exception $e) {
                    return 0;
                }
                return 1;
            }
        }
        return 0;
    }

    public function thresholdSendMail($email, $subject, $html)
    {


        $from_address = '';
        $to_address = [];
        $cc_address = [];

        $mail = null;
        $mail = new PHPMailer(true); // notice the \  you have to use root namespace here
        try {
            $mail->isSMTP(); // tell to use smtp
            //            $mail->isSendMail();
            $mail->CharSet = "utf-8"; // set charset to utf8
            //            $mail->SMTPDebug = 2;
            $mail->SMTPAuth = true;  // use smpt auth
            //            $mail->SMTPSecure = config('MAIL_ENCRYPTION'); // or ssl
            $mail->Host = 'smtp.sendgrid.net';
            $mail->Port = 587; // most likely something different for you. This is the mailtrap.io port i use for testing.
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->Username = 'dhruvin';
            $mail->Password = 'dhruvin@123';


            $mail->setFrom("hr@esparkinfo.com", "eSparkBiz HR");

            //                    $mail->setFrom($from_address, "eSparkBiz");

            $mail->Subject = $subject;
            $mail->MsgHTML($html);
            //                $mail->addAddress($email);

            foreach ($email as $item) {
                $mail->addAddress($item);
            }

            //                    $mail->addCC('info@sacramento4kids.com');
            foreach ($cc_address as $cc) {
                $mail->addCC($cc);
            }

            //                    $mail->addBCC('hr@esparkinfo.com');
            //                    $mail->addBCC('webdeveloper1011@gmail.com');
            $mail->send();
        } catch (phpmailerException $e) {
            return 0;
        } catch (Exception $e) {
            return 0;
        }
        return 1;
    }
    public function candidateSendMail($email, $subject, $html)
    {


        $from_address = '';
        $to_address = [];
        $cc_address = [];

        $mail = null;
        $mail = new PHPMailer(true); // notice the \  you have to use root namespace here
        try {
            $mail->isSMTP(); // tell to use smtp
            //            $mail->isSendMail();
            $mail->CharSet = "utf-8"; // set charset to utf8
            //            $mail->SMTPDebug = 2;
            $mail->SMTPAuth = true;  // use smpt auth
            //            $mail->SMTPSecure = config('MAIL_ENCRYPTION'); // or ssl
            $mail->Host = 'smtp.sendgrid.net';
            $mail->Port = 587; // most likely something different for you. This is the mailtrap.io port i use for testing.
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->Username = 'dhruvin';
            $mail->Password = 'dhruvin@123';


            $mail->setFrom("hr@esparkinfo.com", "eSparkBiz HR");

            //                    $mail->setFrom($from_address, "eSparkBiz");

            $mail->Subject = $subject;
            $mail->MsgHTML($html);
            //                $mail->addAddress($email);

//            foreach ($email as $item) {
                $mail->addAddress($email);
//            }

            //                    $mail->addCC('info@sacramento4kids.com');
            foreach ($cc_address as $cc) {
                $mail->addCC($cc);
            }

            //                    $mail->addBCC('hr@esparkinfo.com');
            //                    $mail->addBCC('webdeveloper1011@gmail.com');
            $mail->send();
        } catch (phpmailerException $e) {
            return 0;
        } catch (Exception $e) {
            return 0;
        }
        return 1;
    }



    public function sendTestMail()
    {

        $mail = null;
        $mail = new PHPMailer(true); // notice the \  you have to use root namespace here
        try {
            $mail->isSMTP(); // tell to use smtp
            //            $mail->isSendMail();
            $mail->CharSet = "utf-8"; // set charset to utf8
            //            $mail->SMTPDebug = 2;
            $mail->SMTPAuth = true;  // use smpt auth
            //            $mail->SMTPSecure = config('MAIL_ENCRYPTION'); // or ssl
            $mail->Host = 'smtp.sendgrid.net';
            $mail->Port = 587; // most likely something different for you. This is the mailtrap.io port i use for testing.
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->Username = 'dhruvin';
            $mail->Password = 'dhruvin@123';


            $mail->setFrom("hr@esparkinfo.com", "eSparkBiz HR");

            $mail->Subject = 'Test Email';
            $mail->MsgHTML("<h1>Test check Mail</h1>");

            $mail->addAddress("webdeveloper1011@gmail.com");

            $mail->send();
        } catch (phpmailerException $e) {
            echo $e;
            return 0;
        } catch (Exception $e) {
            echo $e;
            return 0;
        }
        return 1;
    }

    public function sendReport($html)
    {
        $email_settings = $this->emailRepo->getEmailSettings();
        $emails = [];
        if (!empty($email_settings)) {
            $email_settings = $email_settings[0];
            if ($email_settings['admin_emails']) {
                $admin_emails = explode(',', $email_settings['admin_emails']);
                foreach ($admin_emails as $item) {
                    $emails[] = $item;
                }
            }

            if ($email_settings['hr_emails']) {
                $hr_emails = explode(',', $email_settings['hr_emails']);
                foreach ($hr_emails as $item) {
                    $emails[] = $item;
                }
            }

            if ($email_settings['cto_emails']) {
                $cto_emails = explode(',', $email_settings['cto_emails']);
                foreach ($cto_emails as $item) {
                    $emails[] = $item;
                }
            }

            $mail = null;
            $mail = new PHPMailer(true); // notice the \  you have to use root namespace here
            try {
                $mail->isSMTP(); // tell to use smtp
                //            $mail->isSendMail();
                $mail->CharSet = "utf-8"; // set charset to utf8
                //            $mail->SMTPDebug = 2;
                $mail->SMTPAuth = true;  // use smpt auth
                //            $mail->SMTPSecure = config('MAIL_ENCRYPTION'); // or ssl
                $mail->Host = 'smtp.sendgrid.net';
                $mail->Port = 587; // most likely something different for you. This is the mailtrap.io port i use for testing.
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                $mail->Username = 'dhruvin';
                $mail->Password = 'dhruvin@123';


                $mail->setFrom("vaibhav@esparkinfo.com", "eSparkBiz");

                $mail->Subject = 'Daily Attendance Report';
                $mail->MsgHTML($html);


                if (count($emails) > 0) {
                    foreach ($emails as $item) {
                        $mail->addAddress($item);
                    }
                } else {
                    $mail->addAddress("hari.krishna@esparkinfo.com");
                    $mail->addAddress("hr@esparkinfo.com");
                    $mail->addAddress("chintan@esparkinfo.com");
                }
                // $mail->addAddress("hari.krishna@esparkinfo.com");
                // $mail->addAddress("hr@esparkinfo.com");
                // $mail->addAddress("chintan@esparkinfo.com");

                $mail->send();
            } catch (phpmailerException $e) {
                echo $e;
                return 0;
            } catch (Exception $e) {
                echo $e;
                return 0;
            }
            return 1;
        }
    }
    /*
     * $mail = null;
                $mail = new PHPMailer(true); // notice the \  you have to use root namespace here
                try {
                    $mail->isSMTP(); // tell to use smtp
//            $mail->isSendMail();
                    $mail->CharSet = "utf-8"; // set charset to utf8
//            $mail->SMTPDebug = 2;
                    $mail->SMTPAuth = true;  // use smpt auth
//            $mail->SMTPSecure = config('MAIL_ENCRYPTION'); // or ssl
                    $mail->Host = 'mail.eworkdemo.com';
                    $mail->Port = 587; // most likely something different for you. This is the mailtrap.io port i use for testing.
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );
                    $mail->Username = 'noreply@eworkdemo.com';
                    $mail->Password = '1NSuZrEGNvWd';
                    $mail->setFrom("noreply@eworkdemo.com", "eSparkBiz");
                    $mail->Subject = $subject;
                    $mail->MsgHTML($html);
                    $mail->addAddress($email);
                    $mail->addCC('info@sacramento4kids.com');
//            $mail->addBCC('hr@esparkinfo.com');
                    $mail->addBCC('webdeveloper1011@gmail.com');
                    $mail->send();
                } catch (phpmailerException $e) {
                    return 0;
                } catch (Exception $e) {
                    return 0;
                }
                return 1;
     */
}
