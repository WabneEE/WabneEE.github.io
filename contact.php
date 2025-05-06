<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$name = htmlspecialchars($_POST["name"]);
$email = htmlspecialchars($_POST["email"]);
$phone = htmlspecialchars($_POST["phone"]);
$subject = htmlspecialchars($_POST["subject"]);
$message = nl2br(htmlspecialchars($_POST["message"]));
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'terncoders@gmail.com';
    $mail->Password = 'tllfxoykrhnsraqk';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('terncoders@gmail.com', 'TernCoders');
    $mail->addAddress('terncoders@gmail.com');
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = "You have received a message from <b>$name</b> ($email)(Number:$phone):<br><br>$message";
    $mail->send();
} catch (Exception $e) {
    echo "<script>alert('Mailer Error while sending to admin: {$mail->ErrorInfo}'); window.location.href='index.html';</script>";
    exit;
}
try {
    $userMail = new PHPMailer(true);
    $userMail->isSMTP();
    $userMail->Host = 'smtp.gmail.com';
    $userMail->SMTPAuth = true;
    $userMail->Username = 'terncoders@gmail.com';
    $userMail->Password = 'tllfxoykrhnsraqk';
    $userMail->SMTPSecure = 'tls';
    $userMail->Port = 587;
    $userMail->setFrom('terncoders@gmail.com', 'TernCoders');
    $userMail->addAddress('terncoders@gmail.com');
    $userMail->isHTML(true);
    $userMail->Subject = "Confirmation Email for $name's Submission";
    $userMail->AddEmbeddedImage("mailimgae.jpg", "headerImage", "Header Image");
    $userMail->Body = <<<HTML
<html>
  <head>
    <style>
      @import url("https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@400..800&family=Playwrite+IT+Moderna:wght@100..400&display=swap");
      body {
        font-family: "Baloo Bhaijaan 2", cursive;
        background-color: #fdf6f0;
        color: #4b3832;
        margin: 0;
        padding: 0;
        display: grid;
        justify-content: center;
      }
      .card {
        width: 60vw;
        margin: 5vw auto;
        border-radius: 1vw;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
      }
      #imgil {
        width: 100%;
        border-radius: 1vw 1vw 0 0;
      }
      .content {
        padding: 2.5vw;
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 0 0 1vw 1vw;
      }
      h2 {
        font-size: 2.56vw;
        color: #6d4c41;
        text-align: center;
        margin-top: 0;
      }
      p {
        font-size: 16px;
        line-height: 1.6;
      }
      ul {
        list-style: none;
        padding: 0;
      }
      ul li {
        margin-bottom: 10px;
      }
    </style>
  </head>
  <body>
    <div class="card">
      <img src="cid:headerImage" alt="Header Image" id="imgil" />
      <div class="content">
        <p>Dear <strong>$name</strong>,</p>
        <p>
          We have received your message and we truly appreciate your interest.
          Here is a summary of your submission:
        </p>
        <ul>
          <li><strong>Name:</strong> $name</li>
          <li><strong>Email:</strong> $email</li>
          <li><strong>Phone:</strong> $phone</li>
          <li><strong>Subject:</strong> $subject</li>
          <li><strong>Message:</strong><br />$message</li>
        </ul>
        <p style="text-align: center">
          ✨ We’ll get back to you as soon as possible ✨
        </p>
        <p>
          Warm Regards,<br />TernCoders(terncoders@gmail.com)
        </p>
      </div>
    </div>
  </body>
</html>
HTML;
    $userMail->send();
} catch (Exception $e) {
    echo "<script>alert('Mailer Error while sending confirmation: {$userMail->ErrorInfo}'); window.location.href='index.html';</script>";
    exit;
}
echo "<script>alert('Message sent successfully! A confirmation has been sent to your email.'); window.location.href='index.html';</script>";
?>
