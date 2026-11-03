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

// Resident onboarding: first-login profile interview
(function () {
  var endpoint = '../../backend/api/resident-profile.php';

  function escapeHtml(value) {
    return String(value == null ? '' : value).replace(/[&<>"']/g, function (char) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char];
    });
  }

  function selected(value, option) {
    return String(value) === option ? ' selected' : '';
  }

  function buildModal(profile) {
    var sex = String(profile.sex || '');
    var civilStatus = String(profile.civil_status || '');

    var overlay = document.createElement('div');
    overlay.className = 'onb-overlay';
    overlay.id = 'onb-overlay';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.setAttribute('aria-labelledby', 'onb-title');

    overlay.innerHTML =
      '<div class="onb-modal">' +
        '<span class="onb-badge">Profile setup</span>' +
        '<h2 id="onb-title">Complete your resident profile</h2>' +
        '<p class="onb-desc">Welcome! Before you continue, please answer a few quick questions so the barangay can keep accurate records of your household.</p>' +
        '<form id="onb-form" novalidate>' +
          '<div class="onb-grid">' +
            '<label class="field"><span>First name</span><input type="text" name="first_name" maxlength="80" value="' + escapeHtml(profile.first_name) + '" required></label>' +
            '<label class="field"><span>Middle name</span><input type="text" name="middle_name" maxlength="80" value="' + escapeHtml(profile.middle_name) + '" placeholder="Optional"></label>' +
            '<label class="field"><span>Last name</span><input type="text" name="last_name" maxlength="80" value="' + escapeHtml(profile.last_name) + '" required></label>' +
            '<label class="field"><span>Suffix</span><input type="text" name="suffix" maxlength="20" value="' + escapeHtml(profile.suffix) + '" placeholder="e.g. Jr., III"></label>' +
            '<label class="field"><span>Birth date</span><input type="date" name="birth_date" value="' + escapeHtml(profile.birth_date) + '" required></label>' +
            '<label class="field"><span>Sex</span><select name="sex" required>' +
              '<option value=""' + selected(sex, '') + '>Select</option>' +
              '<option value="male"' + selected(sex, 'male') + '>Male</option>' +
              '<option value="female"' + selected(sex, 'female') + '>Female</option>' +
              '<option value="other"' + selected(sex, 'other') + '>Other</option>' +
            '</select></label>' +
            '<label class="field wide"><span>Civil status</span><select name="civil_status" required>' +
              '<option value=""' + selected(civilStatus, '') + '>Select</option>' +
              '<option value="single"' + selected(civilStatus, 'single') + '>Single</option>' +
              '<option value="married"' + selected(civilStatus, 'married') + '>Married</option>' +
              '<option value="widowed"' + selected(civilStatus, 'widowed') + '>Widowed</option>' +
              '<option value="separated"' + selected(civilStatus, 'separated') + '>Separated</option>' +
              '<option value="annulled"' + selected(civilStatus, 'annulled') + '>Annulled</option>' +
            '</select></label>' +
          '</div>' +
          '<p class="onb-error" id="onb-error" role="alert" hidden></p>' +
          '<div class="onb-foot">' +
            '<button type="submit" class="btn-primary" id="onb-submit">Save my profile</button>' +
          '</div>' +
        '</form>' +
      '</div>';

    document.body.appendChild(overlay);
    document.body.style.overflow = 'hidden';
    return overlay;
  }

  async function fetchCsrf() {
    var response = await fetch('../../backend/api/session.php', {
      headers: { Accept: 'application/json' },
      credentials: 'same-origin',
    });
    var session = await response.json();
    return session.csrf || '';
  }

  async function start() {
    if (document.getElementById('onb-overlay')) return;

    var payload;
    try {
      var response = await fetch(endpoint, {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
      });
      payload = await response.json();
    } catch (error) {
      return;
    }

    if (!payload || payload.ok !== true || payload.needs_onboarding !== true) return;

    var overlay = buildModal(payload.profile || {});
    var form = overlay.querySelector('#onb-form');
    var errorEl = overlay.querySelector('#onb-error');
    var submitBtn = overlay.querySelector('#onb-submit');

    var firstInput = form.querySelector('input[name="first_name"]');
    if (firstInput && !firstInput.value) firstInput.focus();

    form.addEventListener('submit', async function (event) {
      event.preventDefault();
      errorEl.hidden = true;

      var data = {
        first_name: form.first_name.value.trim(),
        middle_name: form.middle_name.value.trim(),
        last_name: form.last_name.value.trim(),
        suffix: form.suffix.value.trim(),
        birth_date: form.birth_date.value,
        sex: form.sex.value,
        civil_status: form.civil_status.value,
      };

      if (!data.first_name || !data.last_name) {
        errorEl.textContent = 'First name and last name are required.';
        errorEl.hidden = false;
        return;
      }
      if (!data.birth_date) {
        errorEl.textContent = 'Please enter your birth date.';
        errorEl.hidden = false;
        return;
      }
      if (!data.sex || !data.civil_status) {
        errorEl.textContent = 'Please select your sex and civil status.';
        errorEl.hidden = false;
        return;
      }

      submitBtn.disabled = true;
      submitBtn.textContent = 'Saving...';

      try {
        var csrf = await fetchCsrf();
        var response = await fetch(endpoint, {
          method: 'POST',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrf,
          },
          credentials: 'same-origin',
          body: JSON.stringify(data),
        });
        var result = await response.json();

        if (result && result.ok === true) {
          overlay.remove();
          document.body.style.overflow = '';
          var greeting = document.querySelector('.greeting h1');
          if (greeting) {
            greeting.textContent = 'Good day, ' + data.first_name;
          }
          return;
        }

        errorEl.textContent = (result && result.error) || 'We could not save your profile. Please try again.';
        errorEl.hidden = false;
      } catch (error) {
        errorEl.textContent = 'We could not save your profile. Please try again.';
        errorEl.hidden = false;
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Save my profile';
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }
})();
