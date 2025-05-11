<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=login_db", "root", "b#P3L8jQoR*5uVp");
if (!isset($_SESSION["user"])) {
    header("Location: auth.php");
    exit();
}
$username = $_SESSION["user"];
$result = "";
if (isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    header("Location: auth.php");
    exit();
}
if (isset($_POST["delete"])) {
    $password = $_POST["password"] ?? "";
    if ($password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user["password_hash"])) {
            $delete = $pdo->prepare("DELETE FROM users WHERE username = ?");
            $delete->execute([$username]);
            session_unset();
            session_destroy();
            header("Location: auth.php");
            exit();
        } else {
            $result = "Invalid password. Account not deleted.";
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
            <p>Welcome, <?php echo htmlspecialchars($username); ?></p>
            <form method="POST">
                <button type="submit" name="logout">Logout</button>
            </form>
            <form method="POST">
                <h3>Delete Account</h3>
                <input type="password" name="password" placeholder="Confirm Password" required />
                <button type="submit" name="delete">Delete Account</button>
                <?php if ($result): ?>
                    <p><?php echo htmlspecialchars($result); ?></p>
                <?php endif; ?>
            </form>
        </section>
    </body>
</html>