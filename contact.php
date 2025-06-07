<?php
session_start();
require "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function sanitize_input($data)
{
	return htmlspecialchars(trim($data), ENT_QUOTES, "UTF-8");
}
$confirmationMessage = "";
$name = sanitize_input($_POST["name"] ?? "");
$email = sanitize_input($_POST["email"] ?? "");
$phone = sanitize_input($_POST["phone"] ?? "");
$subject = sanitize_input($_POST["subject"] ?? "");
$message = nl2br(sanitize_input($_POST["message"] ?? ""));
$errors = [];
if (empty($name)) {
	$name = "(Name Not Given)";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$errors[] = "Invalid email address.";
}
if (empty($phone)) {
	$phone = "Not Given";
} elseif (!preg_match('/^[6-9]\d{9}$/', $phone)) {
	$errors[] = "Phone number must be a valid 10-digit Indian mobile number starting with 6-9.";
}
if (empty($subject)) {
	$errors[] = "Subject is required.";
}
if (empty($message)) {
	$message = "(Message Not Given)";
}
if (empty($errors)) {
	$host = "localhost";
	$user = "root";
	$pass = "b#P3L8jQoR*5uVp";
	$db = "contact_form";
	$conn = new mysqli($host, $user, $pass, $db);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$stmt = $conn->prepare("INSERT INTO messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
	$stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
	if (!$stmt->execute()) {
		echo "<script>alert('Database error: {$stmt->error}'); window.location.href='index.html';</script>";
		exit();
	}
	$stmt->close();
	$conn->close();
	$mail = new PHPMailer(true);
	$mail->isSMTP();
	$mail->Host = "smtp.gmail.com";
	$mail->SMTPAuth = true;
	$mail->Username = "terncoders@gmail.com";
	$mail->Password = "tllfxoykrhnsraqk";
	$mail->SMTPSecure = "tls";
	$mail->Port = 587;
	$mail->setFrom("terncoders@gmail.com", "TernCoders");
	$mail->addAddress("terncoders@gmail.com");
	$mail->isHTML(true);
	$mail->Subject = $subject;
	$mail->Body = "<b>Name:</b> $name<br><b>Email:</b> $email<br><b>Phone:</b> $phone<br><b>Message:</b><br>$message";
	$mail->send();
	if (isset($_POST["remember"])) {
		setcookie("contact_name", $name, time() + 30 * 24 * 60 * 60, "/");
		setcookie("contact_email", $email, time() + 30 * 24 * 60 * 60, "/");
		setcookie("contact_phone", $phone, time() + 30 * 24 * 60 * 60, "/");
	}
	try {
		$userMail = new PHPMailer(true);
		$userMail->isSMTP();
		$userMail->Host = "smtp.gmail.com";
		$userMail->SMTPAuth = true;
		$userMail->Username = "terncoders@gmail.com";
		$userMail->Password = "tllfxoykrhnsraqk";
		$userMail->SMTPSecure = "tls";
		$userMail->Port = 587;
		$userMail->setFrom("terncoders@gmail.com", "TernCoders");
		$userMail->addAddress($email);
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
						<p>
							Dear
							<strong>$name</strong>
							,
						</p>
						<p>We have received your message and we truly appreciate your interest. Here is a summary of your submission:</p>
						<ul>
							<li>
								<strong>Name:</strong>
								$name
							</li>
							<li>
								<strong>Email:</strong>
								$email
							</li>
							<li>
								<strong>Phone:</strong>
								$phone
							</li>
							<li>
								<strong>Subject:</strong>
								$subject
							</li>
							<li>
								<strong>Message:</strong>
								<br />
								$message
							</li>
						</ul>
						<p style="text-align: center">✨ We’ll get back to you as soon as possible ✨</p>
						<p>
							Warm Regards,
							<br />
							TernCoders (terncoders@gmail.com)
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
	exit();
} else {
	$confirmationMessage = "<h3 style='color:red;'>" . implode("<br>", $errors) . "</h3>";
}
$name_cookie = $_COOKIE["contact_name"] ?? "";
$email_cookie = $_COOKIE["contact_email"] ?? "";
$phone_cookie = $_COOKIE["contact_phone"] ?? "";
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="source" content="dynamic" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<style>
			@import url("https://fonts.googleapis.com/css2?family=DM+Serif+Text:ital@0;1&display=swap");
			* {
				height: auto;
				width: auto;
				margin: 0;
				padding: 0;
				box-sizing: border-box;
				font-family: "DM Serif Text", serif;
				font-weight: 400;
				font-style: normal;
			}
			section {
				width: 100vw;
				height: 100vh;
				animation: invisible 0.675s linear;
			}
			@keyframes invisible {
				from {
					opacity: 1;
				}
				from {
					opacity: 0;
				}
			}
			#contact-us {
				background: linear-gradient(135deg, #fdf6f0, #ffe4e1);
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				position: relative;
				overflow: hidden;
				z-index: 1;
			}
			#contact-us h1 {
				font-size: 4vw;
				color: rgba(109, 76, 65, 0.85);
				margin-bottom: 2vw;
				z-index: 2;
			}
			.floating-icons2 {
				position: absolute;
				top: 0;
				left: 0;
				width: 100vw;
				height: 100vh;
			}
			.floating-icons2 span {
				position: absolute;
				font-size: 3vw;
				opacity: 0.35;
				z-index: 0;
				animation: rotate 2.5s infinite alternate linear;
			}
			@keyframes rotate {
				from {
					rotate: -15deg;
				}
				to {
					rotate: 15deg;
				}
			}
			.floating-icons2 span:nth-child(1) {
				top: 20%;
				left: 30%;
			}
			.floating-icons2 span:nth-child(2) {
				top: 10%;
				left: 5%;
			}
			.floating-icons2 span:nth-child(3) {
				top: 70%;
				left: 10%;
			}
			.floating-icons2 span:nth-child(4) {
				top: 30%;
				left: 80%;
			}
			.floating-icons2 span:nth-child(5) {
				top: 70%;
				left: 75%;
			}
			.floating-icons2 span:nth-child(6) {
				top: 5%;
				left: 90%;
			}
			.floating-icons2 span:nth-child(7) {
				top: 60%;
				left: 25%;
			}
			.floating-icons2 span:nth-child(8) {
				top: 85%;
				left: 90%;
			}
			#contact-us form {
				display: flex;
				flex-direction: column;
				gap: 1vw;
				background-color: rgba(255, 255, 255, 0.85);
				padding: 2vw;
				border-radius: 1vw;
				box-shadow: 0 0.5vw 1vw rgba(0, 0, 0, 0.1);
				z-index: 2;
				width: 30vw;
			}
			#contact-us input,
			#contact-us textarea {
				display: block;
				width: 100%;
				padding: 0.75vw;
				font-size: 1vw;
				border: 0.1vw solid #ccc;
				border-radius: 0.5vw;
				font-family: inherit;
				transition: all 0.3s ease;
			}
			#contact-us input:focus,
			#contact-us textarea:focus {
				border-color: #ff7f50;
				outline: none;
				background-color: #fffefc;
				box-shadow: 0 0 0.5vw #ffccbc;
			}
			#contact-us textarea {
				height: 4vw;
				resize: none;
			}
			#contact-us label {
				position: relative;
			}
			#contact-us label {
				position: relative;
				display: flex;
				justify-content: flex-start;
			}
			#contact-us label:has(input[required]):not(:has(input[type="number"]))::after,
			#contact-us label:has(textarea[required])::after {
				content: "*";
				color: red;
				font-weight: bold;
				position: absolute;
				right: 0;
				top: 20%;
				right: -1.2vw;
				transform: translateY(-50%);
				font-size: 1.2vw;
				pointer-events: none;
			}
			#contact-us button {
				background-color: #007bff;
				color: white;
				font-size: 1.5vw;
				padding: 0.8vw 1.6vw;
				border: none;
				border-radius: 0.5vw;
				cursor: pointer;
				transition: background-color 0.3s ease;
			}
			#contact-us button:hover {
				background-color: #0056b3;
			}
			@media (orientation: portrait) {
				#contact-us h1 {
					font-size: 9vw;
					margin-bottom: 10vw;
				}
				.floating-icons2 {
					height: calc(100vh - 10vw);
				}
				.floating-icons2 span {
					font-size: 5vw;
				}
				@keyframes rotate {
					from {
						rotate: -15deg;
					}
					to {
						rotate: 15deg;
					}
				}
				.floating-icons2 span:nth-child(1) {
					top: 20%;
					left: 30%;
				}
				.floating-icons2 span:nth-child(2) {
					top: 10%;
					left: 5%;
				}
				.floating-icons2 span:nth-child(3) {
					top: 60%;
					left: 92%;
				}
				.floating-icons2 span:nth-child(4) {
					top: 4%;
					left: 50%;
				}
				.floating-icons2 span:nth-child(5) {
					top: 93.5%;
					left: 35%;
				}
				.floating-icons2 span:nth-child(6) {
					top: 5%;
					left: 90%;
				}
				.floating-icons2 span:nth-child(7) {
					top: 50%;
					left: 3%;
				}
				.floating-icons2 span:nth-child(8) {
					top: 85%;
					left: 90%;
				}
				#contact-us form {
					width: 80vw;
					padding: 6vw;
					gap: 4vw;
					line-height: 5vw;
				}
				#contact-us input,
				#contact-us textarea {
					width: 100%;
					font-size: 4vw;
					line-height: 6vw;
					height: 10vw;
				}
				#contact-us label:has(input[required]):not(:has(input[type="number"]))::after,
				#contact-us label:has(textarea[required])::after {
					right: -4vw;
					font-size: 4vw;
				}
				#contact-us button {
					font-size: 5vw;
					height: 10vw;
				}
			}
		</style>
	</head>
	<body>
		<section id="contact-us">
			<h1>Contact Us</h1>
			<div class="floating-icons2">
				<span>📬</span>
				<span>📞</span>
				<span>📧</span>
				<span>💬</span>
				<span>📱</span>
				<span>📨</span>
				<span>🗨️</span>
				<span>✉️</span>
			</div>
			<form action="contact.php" method="post">
				<label>
					<input type="text" name="name" placeholder="Your Name" />
				</label>
				<label>
					<input type="email" name="email" placeholder="Your Email" required />
				</label>
				<label>
					<input type="number" name="phone" placeholder="Phone" />
				</label>
				<label>
					<input type="text" name="subject" placeholder="Subject" required />
				</label>
				<label>
					<textarea name="message" placeholder="Your Message"></textarea>
				</label>
				<button type="submit" name="submit">Send</button>
			</form>
		</section>
	</body>
</html>