<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function sanitize_input($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}
$name = sanitize_input($_POST['name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$phone = sanitize_input($_POST['phone'] ?? '');
$subject = sanitize_input($_POST['subject'] ?? '');
$message = nl2br(sanitize_input($_POST['message'] ?? ''));
$errors = [];
if (empty($name)) {
    $name = '(Name Not Given)';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address.';
}
if (empty($phone)) {
    $phone = 'Not Given';
} elseif (!preg_match('/^[6-9]\d{9}$/', $phone)) {
    $errors[] = 'Phone number must be a valid 10-digit Indian mobile number starting with 6-9.';
}
if (empty($subject)) {
    $errors[] = 'Subject is required.';
}
if (empty($message)) {
    $message = '(Message Not Given)';
}
if (!empty($errors)) {
    $query = http_build_query(['errors' => $errors]);
    header("Location: contact.php?$query");
    exit();
}
try {
    $pdo = new PDO('mysql:host=localhost;dbname=contact_form;charset=utf8', 'root', 'b#P3L8jQoR*5uVp');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare('INSERT INTO messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$name, $email, $phone, $subject, strip_tags($message)]);
} catch (PDOException $e) {
    die('PDO Error: ' . $e->getMessage());
}
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
    exit();
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
    $userMail->addAddress($email);
    $userMail->isHTML(true);
    $userMail->Subject = "Confirmation Email for $name's Submission";
    $userMail->AddEmbeddedImage('mailimgae.jpg', 'headerImage', 'Header Image');
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
    exit();
}
echo "<script>alert('Message sent successfully! A confirmation has been sent to your email.'); window.location.href='index.html';</script>";
?>
