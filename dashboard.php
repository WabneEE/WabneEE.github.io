<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=login_db", "root", "b#P3L8jQoR*5uVp");
$username = $_SESSION["user"] ?? "";
$result = "";
if (!isset($_SESSION["user"])) {
	header("Location: auth.php");
	exit();
}
if (isset($_POST["logout"])) {
	session_unset();
	session_destroy();
	header("Location: auth.php");
	exit();
}
if (isset($_POST["delete"])) {
	$password = $_POST["password"] ?? "";
	if ($username && $password) {
		$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
		$stmt->execute([$username]);
		$user = $stmt->fetch();
		if ($user && password_verify($password, $user["password_hash"])) {
			$stmt = $pdo->prepare("DELETE FROM users WHERE username = ?");
			$stmt->execute([$username]);
			$stmt = $pdo->query("SELECT id FROM users ORDER BY id ASC");
			$ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
			$newId = 1;
			foreach ($ids as $oldId) {
				if ($oldId != $newId) {
					$update = $pdo->prepare("UPDATE users SET id = ? WHERE id = ?");
					$update->execute([$newId, $oldId]);
				}
				$newId++;
			}
			$pdo->exec("ALTER TABLE users AUTO_INCREMENT = $newId");
			session_unset();
			session_destroy();
			header("Location: auth.php");
			exit();
		} else {
			$result = "Invalid credentials. Account not deleted.";
		}
	} else {
		$result = "Password is required.";
	}
}
?>
<!doctype html>
<html>
	<head>
		<title>TernCoders - Dashboard</title>
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
		<section class="dashboard log">
			<p>Welcome, [User]</p>
			<form>
				<button type="submit">Logout</button>
			</form>
			<form>
				<h3>Delete Account</h3>
				<label>Password:</label><br />
				<input type="password" required /><br />
				<button type="submit">Delete Account</button>
			</form>
		</section>
	</body>
</html>