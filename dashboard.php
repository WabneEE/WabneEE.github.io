<?php
if (isset($_COOKIE["remembered_email"])) {
	$rememberedEmail = $_COOKIE["remembered_email"];
} else {
	$rememberedEmail = "";
} ?>
<?php
session_start();
$pdo = new PDO(
	"mysql:host=localhost;dbname=login_db",
	"root",
	"b#P3L8jQoR*5uVp"
);
if (!isset($_SESSION["user"])) {
	header("Location: auth.php");
	exit();
}
$result = "";
$name = "";
$stmt = $pdo->prepare(
	"SELECT name, email, password_hash FROM users WHERE id = ?"
);
$stmt->execute([$_SESSION["user"]]);
$user = $stmt->fetch();
if ($user) {
	$name = $user["name"];
}
if (isset($_POST["logout"])) {
	session_unset();
	session_destroy();
	header("Location: auth.php");
	exit();
}
if (isset($_POST["delete"])) {
	$password = $_POST["password"];
	if ($user && password_verify($password, $user["password_hash"])) {
		$token = bin2hex(random_bytes(16));
		$_SESSION["delete_token"] = $token;
		$_SESSION["delete_email"] = $user["email"];
		$email = $user["email"];
		$name = $user["name"];
		$verify_link =
			"http://localhost/verify.php?delete=1&token=$token&email=" .
			urlencode($email);
		$subject = "Confirm Account Deletion";
		$message = "Hi $name,\n\nClick the link below to confirm account deletion:\n$verify_link\n\nIgnore this if not intended.";
		mail($email, $subject, $message);
		$result = "A verification link has been sent to your email.";
	} else {
		$result = "Incorrect password.";
	}
}
?>
<!doctype html>
<html lang="en">
	<head>
  	<meta charset="UTF-8">
		<meta name="source" content="dynamic">
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>TernCoders - Dashboard</title>
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
					<form method="post" style="display: inline">
						<button class="nav-btn nav-btn-link" name="logout">Log Out</button
						><label><input type="checkbox" onclick="togglePasswordVisibility('password', this)" /> Show Password</label
						><label><input type="checkbox" name="remember" /> Remember Me</label>
					</form>
				</li>
			</ul>
			<div class="hamburger"><span class="bar"></span><span class="bar"></span><span class="bar"></span></div>
		</nav>
		<section class="log dashboard">
			<div class="log">
				<h2><?php echo "Welcome, " . htmlspecialchars($name); ?></h2>
			</div>
			<div class="delete-account">
				<h3>Delete Account</h3>
				<form method="post">
					<label for="password">Confirm Password:</label>
					<input type="password" id="password" name="password" id="password" required />
					<button name="delete">Delete Account</button>
					<label><input type="checkbox" onclick="togglePasswordVisibility('password', this)" /> Show Password</label
					><label><input type="checkbox" name="remember" /> Remember Me</label>
				</form>
				<p class="message"><?php echo htmlspecialchars($result); ?></p>
			</div>
		</section>
	</body>
</html>