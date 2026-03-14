document.addEventListener('DOMContentLoaded', () => {
  window.setFilter = (el) => {
    document.querySelectorAll('.pill').forEach((pill) => pill.classList.remove('active'));
    if (el) el.classList.add('active');
  };

  window.addToCart = () => {
    const badge = document.querySelector('.nav-actions .badge');
    if (badge) {
      const current = Number(badge.textContent || 0);
      badge.textContent = String(current + 1);
    }
    window.alert('Demo item added. Use the Shop page for the full cart flow.');
  };

  const observerTargets = document.querySelectorAll('.product-card, .blog-card, .cat-card');
  if ('IntersectionObserver' in window && observerTargets.length) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, { threshold: 0.1 });

    observerTargets.forEach((el) => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(28px)';
      el.style.transition = 'opacity 0.5s, transform 0.5s';
      observer.observe(el);
    });
  }

  const passwordInput = document.querySelector('input[name="password"]');
  if (passwordInput) {
    passwordInput.setAttribute('minlength', '6');
    if (!passwordInput.getAttribute('autocomplete')) {
      passwordInput.setAttribute('autocomplete', 'new-password');
    }
  }
});

/* ── Mobile nav hamburger ── */
(function () {
  const btn  = document.getElementById('navToggle');
  const menu = document.getElementById('navMenu');
  if (!btn || !menu) return;

  btn.addEventListener('click', function () {
    const expanded = this.getAttribute('aria-expanded') === 'true';
    this.setAttribute('aria-expanded', String(!expanded));
    menu.classList.toggle('open');
  });

  // Close menu when a link is clicked
  menu.querySelectorAll('a').forEach(function (a) {
    a.addEventListener('click', function () {
      btn.setAttribute('aria-expanded', 'false');
      menu.classList.remove('open');
    });
  });
})();
