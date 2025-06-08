<?php
$purpose = "";
if (isset($_GET["register"])) {
	$purpose = "Account Creation";
}
if (isset($_GET["reset"])) {
	$purpose = "Password Reset";
}
if (isset($_GET["delete"])) {
	$purpose = "Account Deletion";
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	if ($_POST["otp"] == $_COOKIE["otp"]) {
		$email = $_COOKIE["email"];
		if ($purpose === "Account Creation") {
			echo "<h3>Account successfully created for $email!</h3>";
		} elseif ($purpose === "Password Reset") {
			echo "<h3>Password reset successful for $email!</h3>";
		} elseif ($purpose === "Account Deletion") {
			echo "<h3>Account $email successfully deleted.</h3>";
		}
		setcookie("otp", "", time() - 3600, "/");
		setcookie("email", "", time() - 3600, "/");
		exit();
	} else {
		echo "<h3>Invalid OTP. Please try again.</h3>";
	}
	function sendOTP($email, $name)
	{
		$otp = rand(100000, 999999);
		$_SESSION["verify_otp"] = $otp;
		$_SESSION["verify_email"] = $email;
		$mail = new PHPMailer(true);
		$mail->isSMTP();
		$mail->Host = "smtp.gmail.com";
		$mail->SMTPAuth = true;
		$mail->Username = "arczardrom@gmail.com";
		$mail->Password = "zufafvpiqtxzmljx";
		$mail->SMTPSecure = "tls";
		$mail->Port = 587;
		$mail->setFrom("arczardrom@gmail.com", "WabneEE");
		$mail->addAddress($email);
		$mail->isHTML(true);
		$mail->Subject = "Your OTP Code - WabneEE";
		$mail->Body = <<<HTML
		<!doctype html>
		<html lang="en">
			<head>
				<meta charset="UTF-8" />
				<meta name="viewport" content="width=device-width, initial-scale=1.0" />
				<title>OTP Verification</title>
			</head>
			<body style="font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4">
				<div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 5px">
					<h2 style="color: #333">Hello $name,</h2>
					<p style="font-size: 16px">Your One-Time Password (OTP) for verifying your email address is:</p>
					<h1 style="text-align: center; color: #000">$otp</h1>
					<p style="font-size: 14px; color: #555">Please enter this code to complete your registration. This OTP is valid for a short time only.</p>
					<p style="font-size: 14px; color: #555">If you did not request this, please ignore this email.</p>
					<br />
					<p style="font-size: 14px">
						Thanks,
						<br />
						<b>WabneEE Team</b>
					</p>
				</div>
			</body>
		</html>
		HTML;
		$mail->send();
	}
}
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="source" content="dynamic" />
		<title>WabneEE - Verify</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="style.css" />
		<script src="script.js" defer></script>
		<link rel="icon" href="favicon.gif" type="image/gif" />
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
					<form method="post" style="display: inline">
						<button class="nav-btn nav-btn-link" name="logout">Log Out</button>
						<label>
							<input type="checkbox" onclick="togglePasswordVisibility('password', this)" />
							Show Password
						</label>
						<label>
							<input type="checkbox" name="remember" />
							Remember Me
						</label>
					</form>
				</li>
			</ul>
			<div class="hamburger">
				<span class="bar"></span>
				<span class="bar"></span>
				<span class="bar"></span>
			</div>
		</nav>
		<secton class="log verify">
			<form method="POST">
				<label>Enter OTP sent to your email:</label>
				<input type="text" name="otp" required />
				<button type="submit">Verify</button>
			</form>
		</secton>
	</body>
</html>