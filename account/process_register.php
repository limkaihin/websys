<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

function saveMemberToDB() {
    global $fname, $lname, $email, $cat_name, $pwd_hashed, $errorMsg, $success;

    /*$config = parse_ini_file('C:/Web system/Project/websys/php/php.ini', true);
    if (!$config) { $errorMsg = "Failed to read database config file."; $success = false; return; }

    $conn = new mysqli(
        $config['database']['servername'],
        $config['database']['username'],
        $config['database']['password'],
        $config['database']['dbname']
    );*/

    //Database Credentials
    $servername = "localhost";
    $username = "root";
    $password = "12345"; 
    $dbname = "Meowmart";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) { $errorMsg = "Connection failed: ".$conn->connect_error; $success = false; return; }

    $chk = $conn->prepare("SELECT id FROM Meowmart_members WHERE email = ?");
    $chk->bind_param("s", $email); $chk->execute(); $chk->store_result();
    if ($chk->num_rows > 0) { $errorMsg = "This email is already registered."; $success = false; $chk->close(); $conn->close(); return; }
    $chk->close();

    $stmt = $conn->prepare("INSERT INTO Meowmart_members (fname,lname,email,cat_name,password) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss", $fname, $lname, $email, $cat_name, $pwd_hashed);
    if (!$stmt->execute()) { $errorMsg = "Registration failed. Please try again."; $success = false; }
    $stmt->close(); $conn->close();
}

function si($d) { return htmlspecialchars(stripslashes(trim($d))); }

$fname=$lname=$email=$cat_name=$errorMsg=""; $pwd=$pwd_confirm=""; $success=true;

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    if (empty($_POST["fname"])) { $errorMsg.="First name is required.<br>"; $success=false; } else { $fname=si($_POST["fname"]); }
    if (empty($_POST["lname"])) { $errorMsg.="Last name is required.<br>";  $success=false; } else { $lname=si($_POST["lname"]); }
    if (empty($_POST["email"])) { $errorMsg.="Email is required.<br>";      $success=false; }
    else { $email=si($_POST["email"]); if (!filter_var($email,FILTER_VALIDATE_EMAIL)) { $errorMsg.="Invalid email format.<br>"; $success=false; } }
    $cat_name = si($_POST["cat_name"] ?? "");
    if (empty($_POST["pwd"])) { $errorMsg.="Password is required.<br>"; $success=false; }
    else { $pwd=$_POST["pwd"]; if (strlen($pwd)<6) { $errorMsg.="Password must be at least 6 characters.<br>"; $success=false; } }
    if (empty($_POST["pwd_confirm"])) { $errorMsg.="Please confirm your password.<br>"; $success=false; }
    else { $pwd_confirm=$_POST["pwd_confirm"]; }
    if ($pwd!==$pwd_confirm) { $errorMsg.="Passwords do not match.<br>"; $success=false; }
    if (empty($_POST["agree"])) { $errorMsg.="You must agree to the terms.<br>"; $success=false; }
    if ($success) { $pwd_hashed=password_hash($pwd,PASSWORD_DEFAULT); saveMemberToDB(); }
} else { header("Location: register.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head><?php include "../inc/head.inc.php"; ?><title>Register – MeowMart</title></head>
<body>

<?php include "../inc/nav.inc.php"; ?>

<section style="padding:80px 5%;min-height:60vh;display:flex;align-items:center;justify-content:center;">
  <div style="max-width:500px;width:100%;text-align:center;">
    <?php if ($success): ?>
      <div style="background:var(--warm);border-radius:28px;padding:48px 40px;">
        <div style="font-size:4rem;margin-bottom:16px;">🎉</div>
        <h2 style="font-family:'Playfair Display',serif;font-size:1.8rem;margin-bottom:12px;color:var(--brown);">Welcome to MeowClub!</h2>
        <p style="color:var(--brown-md);margin-bottom:8px;">Thank you, <strong><?= htmlspecialchars($fname)." ".htmlspecialchars($lname) ?></strong>!</p>
        <?php if ($cat_name): ?><p style="color:var(--orange);margin-bottom:28px;">🐱 Say hi to <?= htmlspecialchars($cat_name) ?> for us!</p><?php else: ?><p style="margin-bottom:28px;"></p><?php endif; ?>
        <a href="login.php" class="btn-primary" style="text-decoration:none;display:inline-block;">Log In Now →</a>
      </div>
    <?php else: ?>
      <div style="background:var(--brown);border-radius:28px;padding:48px 40px;">
        <div style="font-size:4rem;margin-bottom:16px;">😿</div>
        <h2 style="font-family:'Playfair Display',serif;font-size:1.8rem;margin-bottom:12px;color:var(--cream);">Oops! Please fix these:</h2>
        <p style="color:var(--blush);margin-bottom:28px;text-align:left;line-height:1.8;"><?= $errorMsg ?></p>
        <a href="register.php" class="btn-join" style="text-decoration:none;display:inline-block;">Go Back →</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include "../inc/footer.inc.php"; ?>
</body>
</html>
