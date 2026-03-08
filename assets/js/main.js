    // Cart toggle
    function openCart() {
      document.getElementById('cartOverlay').classList.add('open');
      document.getElementById('cartDrawer').classList.add('open');
    }
    function closeCart() {
      document.getElementById('cartOverlay').classList.remove('open');
      document.getElementById('cartDrawer').classList.remove('open');
    }
    document.getElementById('cartToggle').addEventListener('click', openCart);

    // Add to cart
    let cartCount = 3;
    function addToCart() {
      cartCount++;
      document.querySelector('.nav-icon .badge').textContent = cartCount;
      // bump total
      const total = (34.70 + 12.90 * (cartCount - 3)).toFixed(2);
      document.getElementById('cartTotal').textContent = `$${total}`;
      openCart();
    }

    // Filter pills
    function setFilter(el) {
      document.querySelectorAll('.pill').forEach(p => p.classList.remove('active'));
      el.classList.add('active');
    }

    // Scroll animation
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.style.opacity = '1';
          e.target.style.transform = 'translateY(0)';
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.product-card, .blog-card, .cat-card').forEach(el => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(28px)';
      el.style.transition = 'opacity 0.5s, transform 0.5s';
      observer.observe(el);
    });
  