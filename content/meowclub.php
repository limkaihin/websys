<?php
$pageTitle = 'MeowClub';
require_once dirname(__DIR__) . '/includes/header.php';

$user = current_user();
$refCode = referral_code_for_user($user);
$refLink = referral_link_for_user($user);
?>

<div style="background:var(--warm);padding:88px 5% 72px;text-align:center;">
  <div class="hero-eyebrow" style="margin:0 auto 24px;">👑 Member Perks</div>
  <h1 class="section-title" style="font-size:clamp(2.4rem,5vw,4rem);margin-bottom:20px;">Welcome to <em>MeowClub</em></h1>
  <p style="color:var(--brown-md);font-size:1.02rem;max-width:760px;margin:0 auto 28px;line-height:1.8;">Join free, earn Pawpoints on every order, unlock member deals, and share your referral code with fellow cat lovers.</p>
  <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
    <a href="#how-it-works" class="btn-primary" style="text-decoration:none;">See How It Works</a>
    <a href="#referrals" class="btn-outline" style="text-decoration:none;">Go to Referrals</a>
  </div>
</div>

<section id="how-it-works" style="padding:72px 5%;background:var(--cream);scroll-margin-top:120px;">
  <div class="section-header">
    <div class="section-tag">✨ Membership Basics</div>
    <h2 class="section-title">How It <em>Works</em></h2>
  </div>
  <div class="values-grid" style="max-width:1180px;margin:0 auto;">
    <?php
    $steps = [
      ['1','Join free with your email and cat profile.'],
      ['2','Earn Pawpoints every time you shop on MeowMart.'],
      ['3','Use your member perks on selected products and seasonal deals.'],
      ['4','Share your referral code to invite friends into the MeowClub.'],
    ];
    foreach ($steps as [$num, $desc]):
    ?>
      <div style="background:var(--white);border-radius:24px;padding:30px 26px;text-align:left;">
        <div style="width:52px;height:52px;border-radius:16px;background:var(--orange);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.1rem;margin-bottom:18px;"><?= h($num) ?></div>
        <p style="margin:0;color:var(--brown);line-height:1.7;font-size:.98rem;"><?= h($desc) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section id="pawpoints" style="padding:72px 5%;background:var(--warm);scroll-margin-top:120px;">
  <div style="max-width:1150px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:28px;align-items:stretch;">
    <div style="background:var(--white);border-radius:28px;padding:40px 34px;">
      <div class="section-tag" style="margin-bottom:16px;display:inline-flex;">🎁 Rewards</div>
      <h2 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;color:var(--brown);margin-bottom:14px;">Pawpoints</h2>
      <p style="color:var(--brown-md);line-height:1.8;margin-bottom:24px;">Pawpoints are your member rewards balance. As a simple guide, members can collect points on purchases and redeem them on future checkouts, surprise bundles, and seasonal offers.</p>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;">
        <div style="background:var(--cream);border-radius:20px;padding:18px;">
          <strong style="display:block;color:var(--brown);margin-bottom:8px;">Earn while you shop</strong>
          <span style="color:var(--brown-md);font-size:.92rem;line-height:1.6;display:block;">Collect points when you place orders through your account.</span>
        </div>
        <div style="background:var(--cream);border-radius:20px;padding:18px;">
          <strong style="display:block;color:var(--brown);margin-bottom:8px;">Redeem on rewards</strong>
          <span style="color:var(--brown-md);font-size:.92rem;line-height:1.6;display:block;">Use points on member savings and selected treats for your cat.</span>
        </div>
      </div>
    </div>
    <div style="background:var(--brown);border-radius:28px;padding:40px 34px;color:var(--cream);">
      <div style="font-size:2.4rem;margin-bottom:14px;">🐾</div>
      <h3 style="font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:900;margin-bottom:16px;color:var(--cream);">Your member snapshot</h3>
      <ul style="list-style:none;padding:0;margin:0;display:grid;gap:14px;">
        <li style="background:rgba(255,255,255,.08);padding:14px 16px;border-radius:18px;">Points are tied to your MeowMart account.</li>
        <li style="background:rgba(255,255,255,.08);padding:14px 16px;border-radius:18px;">Deals are best viewed while logged in.</li>
        <li style="background:rgba(255,255,255,.08);padding:14px 16px;border-radius:18px;">Referral rewards start with your personal code below.</li>
      </ul>
      <div style="margin-top:22px;">
        <?php if (is_logged_in()): ?>
          <a href="<?= h(base_url('account/profile.php')) ?>" class="btn-primary" style="text-decoration:none;display:inline-block;">Open My Account</a>
        <?php else: ?>
          <a href="<?= h(base_url('account/register.php')) ?>" class="btn-primary" style="text-decoration:none;display:inline-block;">Join MeowClub Free</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<section id="referrals" style="padding:72px 5%;background:var(--cream);scroll-margin-top:120px;">
  <div style="max-width:1120px;margin:0 auto;background:var(--white);border-radius:30px;padding:42px 34px;box-shadow:0 10px 30px rgba(61,35,20,.06);">
    <div class="section-header" style="text-align:left;margin-bottom:28px;">
      <div class="section-tag">🔗 Share & Earn</div>
      <h2 class="section-title" style="margin-bottom:0;">Your <em>Referral</em> Page</h2>
    </div>

    <?php if (is_logged_in()): ?>
      <p style="color:var(--brown-md);line-height:1.8;margin-bottom:22px;">Share your referral code or direct signup link with friends from this dedicated referral section.</p>
      <div style="display:grid;grid-template-columns:1fr;gap:16px;">
        <div style="background:var(--cream);border-radius:22px;padding:20px;">
          <div style="font-size:.8rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--brown-md);margin-bottom:10px;">Referral code</div>
          <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <code id="refCodeField" style="font-size:1.05rem;font-weight:700;background:#fff;border:1.5px solid var(--warm);padding:12px 16px;border-radius:16px;color:var(--brown);"><?= h($refCode) ?></code>
            <button type="button" id="copyRefCodeBtn" class="btn-primary" style="border:none;">Copy code</button>
          </div>
        </div>
        <div style="background:var(--cream);border-radius:22px;padding:20px;">
          <div style="font-size:.8rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--brown-md);margin-bottom:10px;">Referral signup link</div>
          <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <input id="refLinkField" type="text" readonly value="<?= h($refLink) ?>" style="flex:1;min-width:260px;background:#fff;border:1.5px solid var(--warm);padding:12px 16px;border-radius:16px;color:var(--brown);font-size:.92rem;">
            <button type="button" id="copyRefLinkBtn" class="btn-primary" style="border:none;">Copy link</button>
          </div>
        </div>
      </div>
      <p id="copyFeedback" style="margin:14px 0 0;color:var(--orange);font-weight:600;min-height:1.4em;"></p>
    <?php else: ?>
      <div style="background:var(--warm);border-radius:24px;padding:28px;">
        <h3 style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900;color:var(--brown);margin-bottom:10px;">Log in to get your referral code</h3>
        <p style="color:var(--brown-md);line-height:1.8;margin-bottom:18px;">Once you sign in, this page will show your own code and a direct signup link that you can share with friends.</p>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
          <a href="<?= h(base_url('account/login.php')) ?>" class="btn-primary" style="text-decoration:none;">Log In</a>
          <a href="<?= h(base_url('account/register.php')) ?>" class="btn-outline" style="text-decoration:none;">Join Free</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<section id="member-deals" style="padding:72px 5% 100px;background:var(--warm);scroll-margin-top:120px;">
  <div class="section-header">
    <div class="section-tag">🔥 Savings</div>
    <h2 class="section-title">Member <em>Deals</em></h2>
  </div>
  <div class="values-grid" style="max-width:1140px;margin:0 auto;">
    <?php
    $deals = [
      ['10% off over $60', 'Use code MEOW10 during checkout for a member-friendly order boost.'],
      ['Free delivery threshold', 'Spend above $60 and enjoy delivery savings on your order.'],
      ['Seasonal surprise drops', 'Keep an eye on featured products and limited-time bundles.'],
    ];
    foreach ($deals as [$title, $desc]):
    ?>
      <div style="background:var(--white);border-radius:24px;padding:30px 28px;">
        <h3 style="font-family:'Playfair Display',serif;font-size:1.3rem;margin-bottom:12px;color:var(--brown);"><?= h($title) ?></h3>
        <p style="margin:0;color:var(--brown-md);line-height:1.75;"><?= h($desc) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
  <div style="text-align:center;margin-top:28px;">
    <a href="<?= h(base_url('shop/products.php')) ?>" class="btn-primary" style="text-decoration:none;display:inline-block;">Shop Member Favourites</a>
  </div>
</section>

<?php if (is_logged_in()): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  function copyFrom(elId, message) {
    var el = document.getElementById(elId);
    var feedback = document.getElementById('copyFeedback');
    if (!el) return;
    var value = el.tagName === 'INPUT' ? el.value : el.textContent;
    navigator.clipboard.writeText(value).then(function () {
      if (feedback) feedback.textContent = message;
    });
  }

  var codeBtn = document.getElementById('copyRefCodeBtn');
  var linkBtn = document.getElementById('copyRefLinkBtn');

  if (codeBtn) codeBtn.addEventListener('click', function () {
    copyFrom('refCodeField', 'Referral code copied.');
  });
  if (linkBtn) linkBtn.addEventListener('click', function () {
    copyFrom('refLinkField', 'Referral link copied.');
  });
});
</script>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
