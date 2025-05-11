<?php
require "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
$pdo = new PDO("mysql:host=localhost;dbname=login_db", "root", "b#P3L8jQoR*5uVp");
$success = "";
$error = "";
if (isset($_POST["verify"])) {
	$email = $_POST["email"] ?? "";
	$otp = $_POST["otp"] ?? "";
	if ($email && $otp) {
		$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND otp = ?");
		$stmt->execute([$email, $otp]);
		if ($stmt->rowCount() > 0) {
			$update = $pdo->prepare("UPDATE users SET email_verified = 1, otp = NULL WHERE email = ?");
			$update->execute([$email]);
			$success = "Email verified successfully! You can now login.";
		} else {
			$error = "Invalid OTP or email.";
		}
	} else {
		$error = "Both fields are required.";
	}
}
?>
<!doctype html>
<html>
	<head>
		<title>TernCoders - Verify OTP</title>
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
		<section class="verify log">
			<form method="POST">
				<h2>Verify OTP</h2>
				<input type="email" name="email" placeholder="Your Email" required />
				<input type="number" name="otp" placeholder="Enter OTP" required />
				<button type="submit" name="verify">Verify</button>
			</form>
		</section>
	</body>
</html>