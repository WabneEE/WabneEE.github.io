<?php
session_start();
require "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function sanitize_input($data)
{
	return htmlspecialchars(trim($data), ENT_QUOTES, "UTF-8");
}
function send_mail($to, $toName, $subject, $body, $imageCid = null)
{
	$mail = new PHPMailer(true);
	$mail->isSMTP();
	$mail->Host = "smtp.gmail.com";
	$mail->SMTPAuth = true;
	$mail->Username = "arczardrom@gmail.com";
	$mail->Password = "zufafvpiqtxzmljx";
	$mail->SMTPSecure = "tls";
	$mail->Port = 587;
	$mail->setFrom("arczardrom@gmail.com", "WabneEE");
	$mail->addAddress($to, $toName);
	$mail->isHTML(true);
	$mail->Subject = $subject;
	if ($imageCid) {
		$mail->addEmbeddedImage(__DIR__ . "/mailimgae.jpg", $imageCid);
	}
	$mail->Body = $body;
	$mail->send();
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$recaptcha_secret = "6LfC6lgrAAAAAHFZl4qPgXw7K4wdObXD8KStmw0p";
	$recaptcha_response = $_POST["g-recaptcha-response"] ?? "";
	$recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
	$recaptcha = file_get_contents($recaptcha_url . "?secret=" . $recaptcha_secret . "&response=" . $recaptcha_response . "&remoteip=" . $_SERVER["REMOTE_ADDR"]);
	$recaptcha = json_decode($recaptcha, true);
	if (!$recaptcha["success"]) {
		echo "<script>alert('Captcha verification failed. Please try again.'); window.location.href='index.html';</script>";
		exit();
	}
	$name = sanitize_input($_POST["name"] ?? "");
	$email = sanitize_input($_POST["email"] ?? "");
	$phone = sanitize_input($_POST["phone"] ?? "");
	$subject = sanitize_input($_POST["subject"] ?? "");
	$message = nl2br(sanitize_input($_POST["message"] ?? ""));
	$errors = [];
	if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors[] = "Invalid email.";
	}
	if ($phone && !preg_match('/^[6-9]\d{9}$/', $phone)) {
		$errors[] = "Invalid phone number.";
	}
	if (!$subject) {
		$errors[] = "Subject is required.";
	}
	$name = $name ?: "(Name Not Given)";
	$phone = $phone ?: "Not Given";
	$message = $message ?: "(Message Not Given)";
	if ($errors) {
		echo "<script>alert('" . implode("\\n", $errors) . "'); window.location.href='index.html';</script>";
		exit();
	}
	$conn = new mysqli("localhost", "root", "b#P3L8jQoR*5uVp", "contact_form");
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$stmt = $conn->prepare("INSERT INTO messages (name,email,phone,subject,message) VALUES (?,?,?,?,?)");
	$stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
	if (!$stmt->execute()) {
		echo "<script>alert('Database error. Please try again later.'); window.location.href='index.html';</script>";
		exit();
	}
	$stmt->close();
	$conn->close();
	send_mail("arczardrom@gmail.com", "Admin", $subject, "<b>Name:</b> $name<br><b>Email:</b> $email<br><b>Phone:</b> $phone<br><b>Message:</b><br>$message");
	if (isset($_POST["remember"])) {
		setcookie("contact_name", $name, time() + 2592000, "/");
		setcookie("contact_email", $email, time() + 2592000, "/");
		setcookie("contact_phone", $phone, time() + 2592000, "/");
	}
	$confirmationBody = <<<HTML
	<html
		><head
			><style>
				@import url("https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2&display=swap");
				body {
					font-family: "Baloo Bhaijaan 2", cursive;
					background: #fdf6f0;
					color: #4b3832;
					margin: 0;
					padding: 0;
				}
				.card {
					max-width: 600px;
					margin: 30px auto;
					border-radius: 10px;
					box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
				}
				#imgil {
					width: 100%;
					border-radius: 10px 10px 0 0;
				}
				.content {
					padding: 20px;
					background: #fff;
					border-radius: 0 0 10px 10px;
				}
				h2 {
					text-align: center;
					color: #6d4c41;
				}
				ul {
					padding: 0;
					list-style: none;
				}
				ul li {
					margin-bottom: 10px;
				}
			</style></head
		><body
			><div class="card"
				><img src="cid:headerImage" alt="Header" id="imgil" /><div class="content"
					><p>Dear <strong>$name</strong>,</p><p>Thanks for reaching out. Here's a summary:</p
					><ul
						><li><strong>Name:</strong> $name</li
						><li><strong>Email:</strong> $email</li
						><li><strong>Phone:</strong> $phone</li
						><li><strong>Subject:</strong> $subject</li
						><li><strong>Message:</strong><br />$message</li></ul
					><p style="text-align: center">We'll get back to you soon ✨</p><p>Warm Regards,<br />WabneEE (arczardrom@gmail.com)</p></div
				></div
			></body
		></html
	>
	HTML;
	send_mail($email, $name, "Confirmation for $name's Submission", $confirmationBody, "headerImage");
	echo "<script>alert('Message sent successfully! A confirmation has been sent to your email.'); window.location.href='index.html';</script>";
	exit();
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
				font-size: 3vw;
				margin-bottom: 1vw;
				color: rgba(109, 76, 65, 0.85);
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
				padding: 1vw;
				border-radius: 1vw;
				box-shadow: 0 0.5vw 1vw rgba(0, 0, 0, 0.1);
				z-index: 2;
				width: 25vw;
			}
			#contact-us input,
			#contact-us textarea {
				display: block;
				width: 100%;
				font-size: 1vw;
				padding: 0.5vw;
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
				right: -0.75vw;
				transform: translateY(-35%);
				font-size: 1vw;
				pointer-events: none;
			}
			#contact-us button {
				background-color: #007bff;
				color: white;
				font-size: 1vw;
				padding: 0.8vw 1.6vw;
				border: none;
				border-radius: 0.5vw;
				cursor: pointer;
				transition: background-color 0.3s ease;
			}
			#contact-us button:hover {
				background-color: #0056b3;
			}
			.recaptcha-wrapper {
				transform: scale(0.85);
				transform-origin: center;
				width: fit-content;
				height: fit-content;
				overflow: hidden;
				align-self: center;
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
				<label><input type="text" name="name" placeholder="Your Name" /></label>
				<label><input type="email" name="email" placeholder="Your Email" required /></label>
				<label><input type="number" name="phone" placeholder="Phone" /></label>
				<label><input type="text" name="subject" placeholder="Subject" required /></label>
				<label><textarea name="message" placeholder="Your Message"></textarea></label>
				<div class="recaptcha-wrapper"><div class="g-recaptcha" data-sitekey="6LfC6lgrAAAAALejNNVx0wuEhSYbcekBo19ED-QM"></div></div>
				<button type="submit" name="submit">Send</button>
			</form>
		</section>
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	</body>
</html>