<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../inc/head.inc.php"; ?>
  <title>Join MeowClub – MeowMart</title>
</head>
<body>

<?php include "../inc/nav.inc.php"; ?>

<div class="membership" id="membership" style="margin:60px 5%;">
  <div class="membership-left">
    <h2>Join the <em>MeowClub</em> & Save Every Day</h2>
    <p>Free membership with exclusive perks, early sale access, birthday treats for your cat, and more — all with no fees, ever.</p>
    <div class="membership-perks">
      <div class="perk">
        <div class="icon">🎁</div>
        <div class="text"><strong>Earn Pawpoints</strong><span>Redeem rewards on every purchase</span></div>
      </div>
      <div class="perk">
        <div class="icon">🚚</div>
        <div class="text"><strong>Free Delivery</strong><span>On all orders for members</span></div>
      </div>
      <div class="perk">
        <div class="icon">🎂</div>
        <div class="text"><strong>Birthday Surprise</strong><span>A free gift for your cat each year</span></div>
      </div>
      <div class="perk">
        <div class="icon">⚡</div>
        <div class="text"><strong>Early Access</strong><span>Shop new arrivals & sales first</span></div>
      </div>
    </div>
  </div>

  <div class="membership-right">
    <h3>Create Your Free Account</h3>
    <form action="process_register.php" method="post">
      <div class="form-field">
        <label>First Name</label>
        <input type="text" name="fname"
               placeholder="e.g. Sarah" required maxlength="45"/>
      </div>
      <div class="form-field">
        <label>Last Name</label>
        <input type="text" name="lname"
               placeholder="e.g. Tan" required maxlength="45"/>
      </div>
      <div class="form-field">
        <label>Email Address</label>
        <input type="email" name="email"
               placeholder="you@example.com" required maxlength="45"/>
      </div>
      <div class="form-field">
        <label>Your Cat's Name 🐱</label>
        <input type="text" name="cat_name" placeholder="e.g. Mochi"/>
      </div>
      <div class="form-field">
        <label>Password</label>
        <input type="password" name="pwd"
               placeholder="At least 6 characters" required/>
      </div>
      <div class="form-field">
        <label>Confirm Password</label>
        <input type="password" name="pwd_confirm"
               placeholder="Repeat your password" required/>
      </div>
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
        <input type="checkbox" name="agree" id="agree"
               style="width:18px;height:18px;accent-color:var(--orange);" required/>
        <label for="agree" style="color:var(--blush);font-size:.82rem;cursor:pointer;">
          I agree to the Terms & Conditions
        </label>
      </div>
      <button class="btn-join" type="submit">Join MeowClub – It's Free!</button>
      <p class="form-note">
        Already a member?
        <a href="login.php" style="color:var(--blush);">Log in →</a>
      </p>
    </form>
  </div>
</div>

<?php include "../inc/footer.inc.php"; ?>
</body>
</html>
