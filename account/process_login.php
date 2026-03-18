<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

function authenticateUser() {
    global $fname, $lname, $email, $errorMsg, $success;

    $config = parse_ini_file('/var/www/private/db-config.ini', true);
    if (!$config) { $errorMsg = "Failed to read database config file."; $success = false; return; }

    $conn = new mysqli(
        $config['database']['servername'],
        $config['database']['username'],
        $config['database']['password'],
        $config['database']['dbname']
    );
    if ($conn->connect_error) { $errorMsg = "Connection failed: ".$conn->connect_error; $success = false; return; }

    $stmt = $conn->prepare("SELECT * FROM Meowmart_members WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($_POST["pwd"], $row["password"])) {
            $_SESSION["loggedin"] = true;
            $_SESSION["fname"]    = $row["fname"];
            $_SESSION["lname"]    = $row["lname"];
            $_SESSION["email"]    = $row["email"];
            $fname = $row["fname"];
        } else {
            $errorMsg = "Email not found or password doesn't match."; $success = false;
        }
    } else {
        $errorMsg = "Email not found or password doesn't match."; $success = false;
    }
    $stmt->close(); $conn->close();
}

$fname = $email = $errorMsg = "";
$success = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["email"])) { $errorMsg = "Email is required."; $success = false; }
    else { $email = $_POST["email"]; }
    if (empty($_POST["pwd"]))   { $errorMsg = "Password is required."; $success = false; }
    if ($success) { authenticateUser(); }
} else { header("Location: login.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head><?php include "../inc/head.inc.php"; ?><title>Login – MeowMart</title></head>
<body>
<?php include "../inc/nav.inc.php"; ?>
<section style="padding:80px 5%;min-height:60vh;display:flex;align-items:center;justify-content:center;">
  <div style="max-width:480px;width:100%;text-align:center;">
    <?php if ($success): ?>
      <div style="background:var(--warm);border-radius:28px;padding:48px 40px;">
        <div style="font-size:4rem;margin-bottom:16px;">🐾</div>
        <h2 style="font-family:'Playfair Display',serif;font-size:1.8rem;margin-bottom:12px;color:var(--brown);">Welcome back, <?= htmlspecialchars($fname) ?>!</h2>
        <p style="color:var(--brown-md);margin-bottom:28px;">Great to have you back in the MeowClub.</p>
        <a href="/index.php" class="btn-primary" style="text-decoration:none;display:inline-block;">Go to Homepage →</a>
      </div>
    <?php else: ?>
      <div style="background:var(--brown);border-radius:28px;padding:48px 40px;">
        <div style="font-size:4rem;margin-bottom:16px;">😿</div>
        <h2 style="font-family:'Playfair Display',serif;font-size:1.8rem;margin-bottom:12px;color:var(--cream);">Login Failed</h2>
        <p style="color:var(--blush);margin-bottom:28px;"><?= htmlspecialchars($errorMsg) ?></p>
        <a href="login.php" class="btn-join" style="text-decoration:none;display:inline-block;">Try Again →</a>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php include "../inc/footer.inc.php"; ?>
</body>
</html>
