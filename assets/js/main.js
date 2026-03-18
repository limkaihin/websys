/* ─── MeowMart main.js ──────────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  /* ══ Mobile drawer ════════════════════════════════════════════════ */
  var toggle   = document.getElementById('navToggle');
  var drawer   = document.getElementById('mobileDrawer');
  var overlay  = document.getElementById('mobileOverlay');
  var closeBtn = document.getElementById('drawerClose');

  function openDrawer() {
    drawer.classList.add('open');
    overlay.classList.add('open');
    toggle.setAttribute('aria-expanded', 'true');
    drawer.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    drawer.classList.remove('open');
    overlay.classList.remove('open');
    toggle.setAttribute('aria-expanded', 'false');
    drawer.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  if (toggle && drawer) {
    toggle.addEventListener('click', function () {
      drawer.classList.contains('open') ? closeDrawer() : openDrawer();
    });
  }
  if (overlay)  overlay.addEventListener('click', closeDrawer);
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeDrawer();
  });

  // Close when any link inside drawer is tapped
  if (drawer) {
    drawer.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', closeDrawer);
    });
  }

  /* ══ Desktop user dropdown ════════════════════════════════════════ */
  var userBtn  = document.getElementById('userMenuTrigger');
  var userMenu = document.getElementById('userDropdown');

  function openMenu()  {
    userMenu.classList.add('open');
    userBtn.setAttribute('aria-expanded', 'true');
  }
  function closeMenu() {
    userMenu.classList.remove('open');
    userBtn.setAttribute('aria-expanded', 'false');
  }

  if (userBtn && userMenu) {
    userBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      userMenu.classList.contains('open') ? closeMenu() : openMenu();
    });
    document.addEventListener('click', function (e) {
      if (!userBtn.contains(e.target) && !userMenu.contains(e.target)) closeMenu();
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeMenu();
    });
  }

  /* ══ Card fade-in on scroll ════════════════════════════════════════ */
  var cards = document.querySelectorAll('.product-card, .blog-card, .cat-card');
  if ('IntersectionObserver' in window && cards.length) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) {
          e.target.style.opacity   = '1';
          e.target.style.transform = 'translateY(0)';
        }
      });
    }, { threshold: 0.08 });
    cards.forEach(function (el) {
      el.style.opacity   = '0';
      el.style.transform = 'translateY(20px)';
      el.style.transition = 'opacity .4s ease, transform .4s ease';
      io.observe(el);
    });
  }

});
