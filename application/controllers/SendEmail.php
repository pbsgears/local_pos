<?php
/*
-- =============================================
-- File Name : SendEmail.php
-- Project Name : SME ERP
-- Module Name : Email
-- Author : Mohamed Mubashir
-- Create date : 20 - December 2016
-- Description : This file contains sending email to reciepient.

-- REVISION HISTORY
-- =============================================*/

class SendEmail extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Email_model');
    }

    function sendEmail()/*send mail*/
    {
        $this->load->library('MY_PHPMailer');
        $mail = new MY_PHPMailer();
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtpout.secureserver.net';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled
        $mail->Username = 'support_admin@xupportcloud.com';                 // SMTP username
        $mail->Password = 'P@ssw0rd240!';                           // SMTP password
        $mail->Port = 465;                                    // TCP port to connect to
        $mail->setFrom('support_admin@xupportcloud.com', 'Info xCloud');
        $mail->addEmbeddedImage('images/Votexcloudsme.png', 'logo_1u');
        $mail->addEmbeddedImage('images/VotexLogo.png', 'logo_2u');
        $mail->isHTML(true);
        $output = $this->Email_model->fetch_email_details();
        if (!empty($output)) {
            foreach ($output as $val) {
                //$mail->SMTPDebug = 3;                               // Enable verbose debug output
                //$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                $mail->addAddress($val["empEmail"], $val["empName"]);     // Add a recipient
                //$mail->addAddress('reyaasr@example.com');               // Name is optional
                //$mail->addReplyTo('info@example.com', 'Information');
                //$mail->addCC('cc@example.com');
                //$mail->addBCC('bcc@example.com');
                //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
                // Set email format to HTML
                $mail->Subject = $val["emailSubject"];
                $msg = "<div style='width: 80%;margin: auto;background-color:#fbfbfb ;padding: 2%;font-family: sans-serif;'><img src='cid:logo_1u' style='width:16%;display: block;'><hr><h2 style='text-align: center;'>".$val["emailSubject"]."</h2> <br><b>Hi " .  $val["empName"] . "</b> <br><br> <p>" . $val["emailBody"] . "</p><br><br><br><p><em>SAVE PAPER - THINK BEFORE YOU PRINT!<br>This is an auto generated email. Please do not reply to this email because we are not monitoring this inbox.</em></p><hr><img src='cid:logo_2u' style='width:16%;display: block;margin: auto;'><br><p style='text-align: center;'></p></div>";
                $mail->Body = $msg;
                $result = $mail->send();
                if ($result) {
                    $update = $this->Email_model->update_email_sent($val["alertID"], $val["empEmail"]);
                } else {
                    echo json_encode(array("error" => 0, "message" => "Mail not sent"));
                }
            }
        }
    }
}