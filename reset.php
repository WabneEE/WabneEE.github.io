<?php
require "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
$pdo = new PDO("mysql:host=localhost;dbname=login_db", "root", "b#P3L8jQoR*5uVp");
$success = "";
$error = "";
function sendResetOTP($email, $otp) {
	$mail = new PHPMailer(true);
	$mail->isSMTP();
	$mail->Host = "smtp.gmail.com";
	$mail->SMTPAuth = true;
	$mail->Username = "terncoders@gmail.com";
	$mail->Password = "tllfxoykrhnsraqk";
	$mail->SMTPSecure = "tls";
	$mail->Port = 587;
	$mail->setFrom("terncoders@gmail.com", "TernCoders");
	$mail->addAddress($email);
	$mail->isHTML(true);
	$mail->Subject = "Reset Password OTP";
	$mail->Body = "Your OTP to reset password is <b>$otp</b>";
	$mail->send();
}
if (isset($_POST["send_otp"])) {
	$email = $_POST["email"] ?? "";
	if ($email) {
		$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND email_verified = 1");
		$stmt->execute([$email]);
		if ($stmt->rowCount() > 0) {
			$otp = rand(100000, 999999);
			$update = $pdo->prepare("UPDATE users SET otp = ? WHERE email = ?");
			$update->execute([$otp, $email]);
			sendResetOTP($email, $otp);
			$success = "OTP sent to your email.";
		} else {
			$error = "Email not found or not verified.";
		}
	} else {
		$error = "Email is required.";
	}
}
if (isset($_POST["reset_password"])) {
	$email = $_POST["email"] ?? "";
	$otp = $_POST["otp"] ?? "";
	$new_password = $_POST["new_password"] ?? "";
	if ($email && $otp && $new_password) {
		$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND otp = ?");
		$stmt->execute([$email, $otp]);
		if ($stmt->rowCount() > 0) {
			$password_hash = password_hash($new_password, PASSWORD_DEFAULT);
			$update = $pdo->prepare("UPDATE users SET password_hash = ?, otp = NULL WHERE email = ?");
			$update->execute([$password_hash, $email]);
			$success = "Password reset successfully. You can now login.";
		} else {
			$error = "Invalid OTP.";
		}
	} else {
		$error = "All fields are required.";
	}
}
?>
<!doctype html>
<html>
	<head>
		<title>TernCoders - Reset Password</title>
		<link rel="stylesheet" href="style.css" />
		<script src="script.js" defer></script>
		<link rel="shortcut icon" href="favicon.gif" type="image/x-icon" />
	</head>
	<body>
		<nav>
			<a href="index.html"> <img src="favicon.gif" alt="Logo" class="logo" /></a>
			<ul class="nav-menu">
				<li><a href="index.html" class="nav-link">Home</a></li>
				<li><a href="#courses" class="nav-link">Courses</a></li>
				<li><a href="#pricing" class="nav-link">Pricing</a></li>
				<li><a href="#about-us" class="nav-link">About Us</a></li>
				<li><a href="#contact-us" class="nav-link">Contact Us</a></li>
				<li>
					<button class="nav-btn nav-btn-link">
						<a href="auth.php">Log In</a>
					</button>
				</li>
			</ul>
			<div class="hamburger">
				<span class="bar"></span>
				<span class="bar"></span>
				<span class="bar"></span>
			</div>
		</nav>
		<section class="reset log">
			<form method="POST">
				<h2>Send OTP</h2>
				<input type="email" name="email" placeholder="Your Verified Email" required />
				<button type="submit" name="send_otp">Send OTP</button>
			</form>
			<form method="POST">
				<h2>Reset Password</h2>
				<input type="email" name="email" placeholder="Your Email" required />
				<input type="number" name="otp" placeholder="Enter OTP" required />
				<input type="password" name="new_password" placeholder="New Password" required />
				<button type="submit" name="reset_password">Reset Password</button>
			</form>
		</section>
	</body>
</html>