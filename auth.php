<?php
require "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
$pdo = new PDO("mysql:host=localhost;dbname=login_db", "root", "b#P3L8jQoR*5uVp");
$success = "";
$error = "";
function sendVerificationEmail($email, $otp) {
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
	$mail->Subject = "Account Verification OTP";
	$mail->Body = "Your OTP to verify your email is <b>$otp</b>";
	$mail->send();
}
if (isset($_POST["register"])) {
	$email = $_POST["email"] ?? "";
	$password = $_POST["password"] ?? "";
	$username = $_POST["username"] ?? "";
	if ($email && $password && $username) {
		$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
		$stmt->execute([$email]);
		if ($stmt->rowCount() == 0) {
			$otp = rand(100000, 999999);
			$password_hash = password_hash($password, PASSWORD_DEFAULT);
			$insert = $pdo->prepare("INSERT INTO users (email, password_hash, otp, username, email_verified) VALUES (?, ?, ?, ?, 0)");
			$insert->execute([$email, $password_hash, $otp, $username]);
			sendVerificationEmail($email, $otp);
			$success = "Account created! Check your email for the OTP.";
		} else {
			$error = "Email is already taken.";
		}
	} else {
		$error = "All fields are required.";
	}
}
if (isset($_POST["login"])) {
	$email = $_POST["email"] ?? "";
	$password = $_POST["password"] ?? "";
	if ($email && $password) {
		$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
		$stmt->execute([$email]);
		$user = $stmt->fetch();
		if ($user && password_verify($password, $user["password_hash"])) {
			if ($user["email_verified"] == 1) {
				session_start();
				$_SESSION["user"] = $user["username"];
				header("Location: dashboard.php");
				exit();
			} else {
				$error = "Please verify your email first.";
			}
		} else {
			$error = "Invalid email or password.";
		}
	} else {
		$error = "Both fields are required.";
	}
}
?>
<!doctype html>
<html>
	<head>
		<title>TernCoders - Auth</title>
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
		<section class="auth log">
			<form method="POST">
				<h2>Login</h2>
				<input type="email" name="email" placeholder="Email" required />
				<input type="password" name="password" placeholder="Password" required />
				<button type="submit" name="login">Login</button>
			</form>
			<form method="POST">
				<h2>Register</h2>
				<input type="text" name="username" placeholder="Username" required />
				<input type="email" name="email" placeholder="Email" required />
				<input type="password" name="password" placeholder="Password" required />
				<button type="submit" name="register">Register</button>
			</form>
		</section>
	</body>
</html>