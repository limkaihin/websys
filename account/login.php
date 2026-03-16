<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../inc/head.inc.php"; ?>
  <title>Log In – MeowMart</title>
</head>
<body>

<?php include "../inc/nav.inc.php"; ?>

<div class="membership" id="membership" style="margin:60px 5%;">
  <div class="membership-left">
    <h2>Welcome Back to <em>MeowMart</em></h2>
    <p>Log in to access your MeowClub perks, track orders, earn Pawpoints, and shop with your saved details.</p>
    <div class="membership-perks">
      <div class="perk">
        <div class="icon">🎁</div>
        <div class="text"><strong>Your Pawpoints</strong><span>Check your balance & rewards</span></div>
      </div>
      <div class="perk">
        <div class="icon">📦</div>
        <div class="text"><strong>Order History</strong><span>Track and reorder easily</span></div>
      </div>
      <div class="perk">
        <div class="icon">🐱</div>
        <div class="text"><strong>Cat Profile</strong><span>Personalised picks for your cat</span></div>
      </div>
    </div>
  </div>

  <div class="membership-right">
    <h3>Log In to Your Account</h3>
    <form action="process_login.php" method="post">
      <div class="form-field">
        <label>Email Address</label>
        <input type="email" id="email" name="email"
               placeholder="you@example.com" required maxlength="45"/>
      </div>
      <div class="form-field">
        <label>Password</label>
        <input type="password" id="pwd" name="pwd"
               placeholder="Enter your password" required/>
      </div>
      <button class="btn-join" type="submit">Log In →</button>
      <p class="form-note">
        Not a member yet?
        <a href="register.php" style="color:var(--blush);">Join MeowClub free →</a>
      </p>
    </form>
  </div>
</div>

<?php include "../inc/footer.inc.php"; ?>
</body>
</html>
