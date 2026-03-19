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



  /* ══ AJAX add-to-cart / wishlist (no reload, no jump) ═════════════════════ */
  function parseHtml(html) {
    return new DOMParser().parseFromString(html, 'text/html');
  }

  function ensureToastContainer() {
    var existing = document.querySelector('[data-flash-toast]');
    if (existing) existing.remove();
  }

  function showToast(message, type) {
    if (!message) return;
    ensureToastContainer();
    var toast = document.createElement('div');
    toast.className = 'flash-toast flash-toast-' + (type === 'error' ? 'error' : 'success');
    toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
    toast.setAttribute('aria-live', 'polite');
    toast.setAttribute('data-flash-toast', '');
    toast.innerHTML = '<div class="flash-toast__text"></div><button type="button" class="flash-toast__close" aria-label="Dismiss message" data-flash-close>&times;</button>';
    toast.querySelector('.flash-toast__text').textContent = message;
    document.body.appendChild(toast);

    var dismissed = false;
    var removeToast = function () {
      if (dismissed) return;
      dismissed = true;
      toast.classList.add('is-hiding');
      window.setTimeout(function () {
        if (toast.parentNode) toast.parentNode.removeChild(toast);
      }, 240);
    };

    toast.querySelector('[data-flash-close]').addEventListener('click', removeToast);
    window.setTimeout(removeToast, 10000);
  }

  function syncSingleNavIcon(currentHrefPart, responseDoc) {
    var current = document.querySelector('.nav-actions a.nav-icon[href*="' + currentHrefPart + '"]');
    var fresh = responseDoc.querySelector('.nav-actions a.nav-icon[href*="' + currentHrefPart + '"]');
    if (!current || !fresh) return;

    current.setAttribute('aria-label', fresh.getAttribute('aria-label') || current.getAttribute('aria-label') || '');

    var currentIcon = current.querySelector('i');
    var freshIcon = fresh.querySelector('i');
    if (currentIcon && freshIcon) {
      currentIcon.className = freshIcon.className;
    }

    var currentBadge = current.querySelector('.badge');
    var freshBadge = fresh.querySelector('.badge');
    if (freshBadge) {
      if (!currentBadge) {
        currentBadge = document.createElement('span');
        currentBadge.className = 'badge';
        currentBadge.setAttribute('aria-hidden', 'true');
        current.appendChild(currentBadge);
      }
      currentBadge.textContent = freshBadge.textContent;
    } else if (currentBadge) {
      currentBadge.remove();
    }
  }

  function syncNavCounts(responseDoc) {
    syncSingleNavIcon('/shop/wishlist.php', responseDoc);
    syncSingleNavIcon('/shop/cart.php', responseDoc);
  }

  function extractToast(responseDoc) {
    var toast = responseDoc.querySelector('[data-flash-toast]');
    if (!toast) return null;
    return {
      type: toast.className.indexOf('flash-toast-error') !== -1 ? 'error' : 'success',
      message: (toast.querySelector('.flash-toast__text') || toast).textContent.trim()
    };
  }

  function syncWishlistButton(currentForm, responseDoc) {
    var productId = currentForm.getAttribute('data-product-id') || '';
    var freshForm = responseDoc.querySelector('form[data-ajax-wishlist][data-product-id="' + productId + '"]');
    var currentButton = currentForm.querySelector('button.wishlist');
    if (!currentButton) return;

    if (!freshForm) {
      return;
    }

    var freshButton = freshForm.querySelector('button.wishlist');
    if (!freshButton) return;

    currentButton.className = freshButton.className;
    currentButton.setAttribute('aria-label', freshButton.getAttribute('aria-label') || 'Toggle wishlist');
    currentButton.innerHTML = freshButton.innerHTML;

    var detailSummary = document.querySelector('.detail-summary');
    if (detailSummary && currentForm.getAttribute('data-ajax-wishlist') === 'detail') {
      var ratingRow = detailSummary.querySelector('.detail-rating-row');
      if (ratingRow) {
        var existing = ratingRow.querySelector('[data-wishlist-indicator]');
        if (existing) existing.remove();
        if (currentButton.classList.contains('active')) {
          var indicator = document.createElement('span');
          indicator.setAttribute('data-wishlist-indicator', '');
          indicator.style.fontSize = '.82rem';
          indicator.style.color = 'var(--orange)';
          indicator.textContent = '❤️ In your wishlist';
          ratingRow.appendChild(indicator);
        }
      }
    }
  }

  function replaceWishlistSection(responseDoc) {
    var currentSection = document.querySelector('section.products');
    var freshSection = responseDoc.querySelector('section.products');
    if (currentSection && freshSection) {
      currentSection.innerHTML = freshSection.innerHTML;
    }
  }

  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!(form instanceof HTMLFormElement)) return;

    var isWishlist = form.hasAttribute('data-ajax-wishlist');
    var isCart = form.hasAttribute('data-ajax-cart');
    if (!isWishlist && !isCart) return;

    e.preventDefault();

    var submitButton = document.activeElement && form.contains(document.activeElement) ? document.activeElement : form.querySelector('button[type="submit"],input[type="submit"]');
    if (submitButton) submitButton.disabled = true;

    var formData = new FormData(form);
    if (submitButton && submitButton.name && !formData.has(submitButton.name)) {
      formData.append(submitButton.name, submitButton.value || '');
    }

    var scrollY = window.scrollY;
    fetch(form.getAttribute('action') || window.location.href, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
      .then(function (res) { return res.text(); })
      .then(function (html) {
        var responseDoc = parseHtml(html);
        syncNavCounts(responseDoc);

        if (form.getAttribute('data-ajax-wishlist') === 'wishlist') {
          replaceWishlistSection(responseDoc);
        } else if (isWishlist) {
          syncWishlistButton(form, responseDoc);
        }

        var toast = extractToast(responseDoc);
        if (toast && toast.message) {
          showToast(toast.message, toast.type);
        }

        window.scrollTo(0, scrollY);
      })
      .catch(function () {
        showToast('Something went wrong. Please try again.', 'error');
      })
      .finally(function () {
        if (submitButton) submitButton.disabled = false;
      });
  });

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
