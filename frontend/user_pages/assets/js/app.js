// Mobile navigation toggle
(function () {
  var toggle = document.getElementById('nav-toggle');
  var nav = document.getElementById('main-nav');
  if (!toggle || !nav) return;

  function setOpen(open) {
    nav.classList.toggle('open', open);
    toggle.setAttribute('aria-expanded', String(open));
  }

  toggle.addEventListener('click', function () {
    setOpen(!nav.classList.contains('open'));
  });

  nav.querySelectorAll('a').forEach(function (link) {
    link.addEventListener('click', function () { setOpen(false); });
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') setOpen(false);
  });

  window.addEventListener('resize', function () {
    if (window.innerWidth > 860) setOpen(false);
  });
})();

// Sign out
(function () {
  var button = document.getElementById('signout');
  if (!button) return;

  button.addEventListener('click', async function () {
    button.disabled = true;
    try {
      var sessionResponse = await fetch('../../backend/api/session.php', {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
      });
      var session = await sessionResponse.json();
      var logoutResponse = await fetch('../../backend/api/logout.php', {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-Token': session.csrf || '',
        },
        credentials: 'same-origin',
      });
      var result = await logoutResponse.json();
      location.href = result.redirect || '../login_page/login.php';
    } catch (error) {
      location.href = '../login_page/login.php';
    } finally {
      button.disabled = false;
    }
  });
})();
