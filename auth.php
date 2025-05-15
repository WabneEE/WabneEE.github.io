<?php
session_start();
require "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
$pdo = new PDO("mysql:host=localhost;dbname=login_db", "root", "b#P3L8jQoR*5uVp");
$error = "";
$success = "";
function sendOTP($email, $name)
{
	$otp = rand(100000, 999999);
	$_SESSION["verify_otp"] = $otp;
	$_SESSION["verify_email"] = $email;
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
	$mail->Subject = "Your OTP Code - TernCoders";
	$mail->Body = <<<HTML
		<html>
			<head>
				<meta charset="UTF-8" />
				<title>OTP Verification</title>
			</head>
			<body style="font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4">
				<div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 5px">
					<h2 style="color: #333">Hello $name,</h2>
					<p style="font-size: 16px">Your One-Time Password (OTP) for verifying your email address is:</p>
					<h1 style="text-align: center; color: #000">$otp</h1>
					<p style="font-size: 14px; color: #555">
						Please enter this code to complete your registration. This OTP is valid for a short time only.
					</p>
					<p style="font-size: 14px; color: #555">If you did not request this, please ignore this email.</p>
					<br />
					<p style="font-size: 14px">Thanks,<br /><b>TernCoders Team</b></p>
				</div>
			</body>
		</html>
	HTML;
	$mail->send();
}
if (isset($_POST["register"])) {
	$name = $_POST["name"] ?? "";
	$email = $_POST["email"] ?? "";
	$password = $_POST["password"] ?? "";
	if ($name && $email && $password) {
		$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
		$stmt->execute([$email]);
		if ($stmt->rowCount() == 0) {
			$password_hash = password_hash($password, PASSWORD_DEFAULT);
			$stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, email_verified) VALUES (?, ?, ?, 0)");
			$stmt->execute([$name, $email, $password_hash]);
			sendOTP($email, $name);
			$_SESSION["pending_verification"] = $email;
			header("Location: verify.php");
			exit();
		} else {
			$error = "Email already registered.";
		}
	} else {
		$error = "All fields are required.";
	}
}
if (isset($_POST["login"])) {
	$email = $_POST["email"] ?? "";
	$password = $_POST["password"] ?? "";
	if ($email && $password) {
		$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND email_verified = 1");
		$stmt->execute([$email]);
		$user = $stmt->fetch();
		if ($user && password_verify($password, $user["password_hash"])) {
			$_SESSION["user"] = $user["id"];
			header("Location: dashboard.php");
			exit();
		} else {
			$error = "Invalid credentials or email not verified.";
		}
	} else {
		$error = "Email and password are required.";
	}
}
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="source" content="dynamic">
		<title>TernCoders - Auth</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="style.css" />
		<script src="script.js" defer></script>
		<link rel="icon" href="favicon.gif" type="image/gif" />
		<script>
			function togglePasswordVisibility(id, checkbox) {
				var x = document.getElementById(id);
				if (checkbox.checked) {
					x.type = "text";
				} else {
					x.type = "password";
				}
			}
		</script>
	</head>
	<body>
		<nav>
			<a href="index.html"><img src="favicon.gif" alt="Logo" class="logo" /></a>
			<ul class="nav-menu">
				<li><a href="index.html#home" class="nav-link">Home</a></li>
				<li><a href="index.html#courses" class="nav-link">Courses</a></li>
				<li><a href="index.html#about-us" class="nav-link">About Us</a></li>
				<li><a href="index.html#contact-us" class="nav-link">Contact Us</a></li>
				<li>
					<button class="nav-btn nav-btn-link"><a href="auth.php">Log In</a></button>
				</li>
			</ul>
			<div class="hamburger"><span class="bar"></span><span class="bar"></span><span class="bar"></span></div>
		</nav>
		<section class="log auth">
			<form method="POST">
				<h2>Login</h2>
				<input type="email" name="email" placeholder="Email" required />
				<input type="password" id="password" name="password" placeholder="Password" required />
				<button type="submit" name="login">Login</button>
				<label><input type="checkbox" onclick="togglePasswordVisibility('password', this)" /> Show Password</label>
			</form>
			<form method="POST">
				<h2>Register</h2>
				<input type="text" name="name" placeholder="Full Name" required />
				<input type="email" name="email" placeholder="Email" required />
				<input type="password" id="password" name="password" placeholder="Password" required />
				<button type="submit" name="register">Register</button>
				<label><input type="checkbox" onclick="togglePasswordVisibility('password', this)" /> Show Password</label>
			</form>
			<p class="error"><?php echo htmlspecialchars($error); ?></p>
			<p class="success"><?php echo htmlspecialchars($success); ?></p>
		</section>
	</body>
</html>