<?php
$pageTitle = 'Help';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div style="background:var(--warm);padding:88px 5% 72px;text-align:center;">
  <div class="hero-eyebrow" style="margin:0 auto 24px;">💬 Support</div>
  <h1 class="section-title" style="font-size:clamp(2.4rem,5vw,4rem);margin-bottom:20px;">How can we <em>help</em>?</h1>
  <p style="color:var(--brown-md);font-size:1.02rem;max-width:760px;margin:0 auto 28px;line-height:1.8;">Find order support, shipping details, returns guidance, and the answers to common MeowMart questions.</p>
  <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
    <a href="#track-order" class="btn-primary" style="text-decoration:none;">Track My Order</a>
    <a href="#faq" class="btn-outline" style="text-decoration:none;">View FAQ</a>
  </div>
</div>

<section style="padding:28px 5% 0;background:var(--cream);">
  <div style="max-width:1120px;margin:0 auto;display:flex;gap:10px;flex-wrap:wrap;justify-content:center;">
    <a href="#track-order" class="pill active" style="text-decoration:none;">Track My Order</a>
    <a href="#returns" class="pill" style="text-decoration:none;">Returns & Refunds</a>
    <a href="#shipping" class="pill" style="text-decoration:none;">Shipping Info</a>
    <a href="#faq" class="pill" style="text-decoration:none;">FAQ</a>
  </div>
</section>

<section id="track-order" style="padding:56px 5% 28px;background:var(--cream);scroll-margin-top:120px;">
  <div style="max-width:1120px;margin:0 auto;background:var(--white);border-radius:28px;padding:38px 32px;display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:28px;align-items:center;">
    <div>
      <div class="section-tag" style="display:inline-flex;margin-bottom:16px;">📦 Orders</div>
      <h2 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;color:var(--brown);margin-bottom:12px;">Track my order</h2>
      <p style="color:var(--brown-md);line-height:1.8;margin-bottom:18px;">Check your latest order status, order total, and item list in one place.</p>
      <?php if (is_logged_in()): ?>
        <a href="<?= h(base_url('shop/orders.php')) ?>" class="btn-primary" style="text-decoration:none;display:inline-block;">Open My Orders</a>
      <?php else: ?>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
          <a href="<?= h(base_url('account/login.php')) ?>" class="btn-primary" style="text-decoration:none;display:inline-block;">Log In to Track Orders</a>
          <a href="<?= h(base_url('account/register.php')) ?>" class="btn-outline" style="text-decoration:none;display:inline-block;">Create Account</a>
        </div>
      <?php endif; ?>
    </div>
    <div style="background:var(--warm);border-radius:24px;padding:24px;">
      <ul style="list-style:none;padding:0;margin:0;display:grid;gap:12px;">
        <li style="background:#fff;border-radius:18px;padding:14px 16px;">Confirmed orders appear in your account after checkout.</li>
        <li style="background:#fff;border-radius:18px;padding:14px 16px;">Shipping and delivery updates are shown on your order history page.</li>
        <li style="background:#fff;border-radius:18px;padding:14px 16px;">Need help with a missing order? Use our contact form for support.</li>
      </ul>
    </div>
  </div>
</section>

<section id="returns" style="padding:28px 5%;background:var(--cream);scroll-margin-top:120px;">
  <div style="max-width:1120px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:18px;">
    <div style="background:var(--white);border-radius:24px;padding:28px;">
      <h3 style="font-family:'Playfair Display',serif;font-size:1.45rem;color:var(--brown);margin-bottom:12px;">Returns & refunds</h3>
      <p style="color:var(--brown-md);line-height:1.8;margin:0;">Unused items in original condition can be returned within 30 days. Once approved, refunds go back to the original payment method.</p>
    </div>
    <div style="background:var(--white);border-radius:24px;padding:28px;">
      <h3 style="font-family:'Playfair Display',serif;font-size:1.45rem;color:var(--brown);margin-bottom:12px;">What to prepare</h3>
      <p style="color:var(--brown-md);line-height:1.8;margin:0;">Have your order number, item name, and a short description of the issue ready. Photos help speed things up for damaged or incorrect items.</p>
    </div>
    <div style="background:var(--white);border-radius:24px;padding:28px;">
      <h3 style="font-family:'Playfair Display',serif;font-size:1.45rem;color:var(--brown);margin-bottom:12px;">Start a request</h3>
      <p style="color:var(--brown-md);line-height:1.8;margin-bottom:16px;">Use our contact page and include your order details so the support team can guide the next steps.</p>
      <a href="<?= h(base_url('content/contact.php')) ?>" class="btn-primary" style="text-decoration:none;display:inline-block;">Contact Support</a>
    </div>
  </div>
</section>

<section id="shipping" style="padding:28px 5%;background:var(--warm);scroll-margin-top:120px;">
  <div style="max-width:1120px;margin:0 auto;background:var(--brown);border-radius:28px;padding:38px 32px;color:var(--cream);">
    <div class="section-tag" style="background:rgba(255,255,255,.08);color:var(--orange-lt);display:inline-flex;margin-bottom:16px;">🚚 Delivery</div>
    <h2 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;color:var(--cream);margin-bottom:12px;">Shipping info</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-top:22px;">
      <div style="background:rgba(255,255,255,.08);border-radius:22px;padding:18px;">Free shipping on orders above <strong>$60</strong>.</div>
      <div style="background:rgba(255,255,255,.08);border-radius:22px;padding:18px;">Most local orders arrive in <strong>1 to 3 working days</strong>.</div>
      <div style="background:rgba(255,255,255,.08);border-radius:22px;padding:18px;">Tracking status is best viewed from your order history page after login.</div>
    </div>
  </div>
</section>

<section id="faq" style="padding:56px 5% 100px;background:var(--cream);scroll-margin-top:120px;">
  <div class="section-header">
    <div class="section-tag">❓ Common Questions</div>
    <h2 class="section-title">Frequently asked <em>questions</em></h2>
  </div>
  <div style="max-width:980px;margin:0 auto;display:grid;gap:14px;">
    <?php
    $faqs = [
      ['Do I need an account to shop?', 'No. You can browse products freely, but an account makes it easier to track orders and access MeowClub perks.'],
      ['Where can I see my past orders?', 'Once logged in, open My Orders to review your order history, statuses, and totals.'],
      ['How do I contact support?', 'Use the Contact Us page and include your order number if your question is about a purchase.'],
      ['Do vouchers work at checkout?', 'Yes. Enter your voucher code during checkout and the order summary will update before you place the order.'],
    ];
    foreach ($faqs as [$q, $a]):
    ?>
      <details style="background:var(--white);border-radius:20px;padding:18px 22px;">
        <summary style="cursor:pointer;font-weight:700;color:var(--brown);"><?= h($q) ?></summary>
        <p style="margin:12px 0 0;color:var(--brown-md);line-height:1.75;"><?= h($a) ?></p>
      </details>
    <?php endforeach; ?>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
