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
      var sessionResponse = await fetch((window.EB_BASE || '') + '/api/session', {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
      });
      var session = await sessionResponse.json();
      var logoutResponse = await fetch((window.EB_BASE || '') + '/api/logout', {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-Token': session.csrf || '',
        },
        credentials: 'same-origin',
      });
      var result = await logoutResponse.json();
      location.href = result.redirect || (window.EB_BASE || '') + '/login';
    } catch (error) {
      location.href = (window.EB_BASE || '') + '/login';
    } finally {
      button.disabled = false;
    }
  });
})();

// Resident onboarding: first-login profile interview
(function () {
  var endpoint = (window.EB_BASE || '') + '/api/resident-profile';

  function escapeHtml(value) {
    return String(value == null ? '' : value).replace(/[&<>"']/g, function (char) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char];
    });
  }

  function selected(value, option) {
    return String(value) === option ? ' selected' : '';
  }

  function pad2(value) {
    return (value < 10 ? '0' : '') + value;
  }

  function formatBirthDate(value) {
    var match = String(value || '').match(/^(\d{4})-(\d{2})-(\d{2})$/);
    return match ? match[2] + '/' + match[3] + '/' + match[1] : String(value || '');
  }

  function parseBirthDate(value) {
    var match = String(value || '').trim().match(/^(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{4})$/);
    if (!match) return '';

    var month = parseInt(match[1], 10);
    var day = parseInt(match[2], 10);
    var year = parseInt(match[3], 10);
    var date = new Date(Date.UTC(year, month - 1, day));

    if (date.getUTCFullYear() !== year || date.getUTCMonth() !== month - 1 || date.getUTCDate() !== day) {
      return '';
    }
    if (year < 1900 || date.getTime() > Date.now()) return '';

    return year + '-' + pad2(month) + '-' + pad2(day);
  }

  function icon(name) {
    var paths = {
      id: '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 10h4"/><path d="M7 14h7"/>',
      mail: '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
      home: '<path d="m3 10 9-7 9 7"/><path d="M5 9v10h14V9"/><path d="M9 19v-5h6v5"/>',
      pin: '<path d="M12 21s7-5.2 7-12a7 7 0 1 0-14 0c0 6.8 7 12 7 12Z"/><circle cx="12" cy="9" r="2.5"/>',
      lock: '<rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
      check: '<path d="m5 12 4 4L19 6"/>',
      clock: '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
      edit: '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>'
    };

    return '<svg class="onb-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + (paths[name] || '') + '</svg>';
  }

  function showToast(message) {
    var existing = document.querySelector('.onb-toast');
    if (existing) existing.remove();

    var toast = document.createElement('div');
    toast.className = 'onb-toast';
    toast.setAttribute('role', 'status');
    toast.innerHTML =
      '<span class="onb-toast-icon">' + icon('check') + '</span>' +
      '<span>' + escapeHtml(message) + '</span>';

    document.body.appendChild(toast);
    window.setTimeout(function () { toast.classList.add('show'); }, 20);
    window.setTimeout(function () {
      toast.classList.remove('show');
      window.setTimeout(function () { toast.remove(); }, 250);
    }, 3600);
  }

  function trapFocus(overlay) {
    overlay.addEventListener('keydown', function (event) {
      if (event.key !== 'Tab') return;

      var focusable = overlay.querySelectorAll('button, input, select, textarea, a[href]');
      var list = Array.prototype.filter.call(focusable, function (element) {
        return !element.disabled && element.offsetParent !== null;
      });
      if (!list.length) return;

      var first = list[0];
      var last = list[list.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    });
  }

  function buildModal(profile) {
    var sex = String(profile.sex || '');
    var civilStatus = String(profile.civil_status || '');
    var firstName = String(profile.first_name || '').trim();

    var trustItems = [];
    if (profile.resident_code) trustItems.push({ icon: 'id', label: 'Resident ID', value: profile.resident_code });
    if (profile.email) trustItems.push({ icon: 'mail', label: 'Email', value: profile.email });
    if (profile.household_number) trustItems.push({ icon: 'home', label: 'Household', value: profile.household_number });
    if (profile.address_line) trustItems.push({ icon: 'pin', label: 'Address', value: profile.address_line, wide: true });

    var trustHtml = '';
    var accountLabel = '';
    if (trustItems.length) {
      accountLabel = '<div class="onb-account-head"><span>Registration details</span><span>From your account</span></div>';
      trustHtml = '<div class="onb-trust" aria-label="Registration details">' +
        trustItems.map(function (item) {
          return '<div class="onb-trust-item' + (item.wide ? ' wide' : '') + '">' +
            '<span class="onb-trust-icon">' + icon(item.icon) + '</span>' +
            '<div><span>' + escapeHtml(item.label) + '</span><strong>' + escapeHtml(item.value) + '</strong></div>' +
          '</div>';
        }).join('') +
      '</div>';
    }

    var overlay = document.createElement('div');
    overlay.className = 'onb-overlay';
    overlay.id = 'onb-overlay';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.setAttribute('aria-labelledby', 'onb-title');

    overlay.innerHTML =
      '<div class="onb-modal">' +
        '<div class="onb-head">' +
          '<div class="onb-head-top">' +
            '<span class="onb-mark" aria-hidden="true">B</span>' +
            '<div class="onb-brand"><strong>Barangay Bucana</strong><span>Resident portal</span></div>' +
            '<span class="onb-badge">Profile setup</span>' +
          '</div>' +
          '<h2 id="onb-title">Welcome' + (firstName ? ', ' + escapeHtml(firstName) : '') + '</h2>' +
          '<p class="onb-desc">Let\'s complete your resident profile so the barangay can keep your records accurate.</p>' +
          '<div class="onb-meta">' +
            '<span>' + icon('clock') + 'About 2 minutes</span>' +
            '<span>' + icon('edit') + 'You can edit this later</span>' +
          '</div>' +
        '</div>' +
        accountLabel +
        trustHtml +
        '<form id="onb-form" novalidate>' +
          '<p class="onb-error" id="onb-error" role="alert" hidden></p>' +
          '<div class="onb-body">' +
          '<div class="onb-section">' +
            '<div class="onb-section-head">' +
              '<span class="onb-section-no" aria-hidden="true">1</span>' +
              '<div class="onb-section-copy">' +
                '<h3>Your name</h3>' +
                '<p>Use the name on your valid ID.</p>' +
              '</div>' +
            '</div>' +
            '<div class="onb-grid">' +
              '<label class="field"><span>First name</span><input type="text" name="first_name" autocomplete="given-name" maxlength="80" value="' + escapeHtml(profile.first_name) + '" required></label>' +
              '<label class="field"><span>Middle name</span><input type="text" name="middle_name" autocomplete="additional-name" maxlength="80" value="' + escapeHtml(profile.middle_name) + '" placeholder="Optional"></label>' +
              '<label class="field"><span>Last name</span><input type="text" name="last_name" autocomplete="family-name" maxlength="80" value="' + escapeHtml(profile.last_name) + '" required></label>' +
              '<label class="field"><span>Suffix</span><input type="text" name="suffix" autocomplete="honorific-suffix" maxlength="20" value="' + escapeHtml(profile.suffix) + '" placeholder="e.g. Jr., III"></label>' +
            '</div>' +
          '</div>' +
          '<div class="onb-section">' +
            '<div class="onb-section-head">' +
              '<span class="onb-section-no" aria-hidden="true">2</span>' +
              '<div class="onb-section-copy">' +
                '<h3>About you</h3>' +
                '<p>These details appear on your barangay records.</p>' +
              '</div>' +
            '</div>' +
            '<div class="onb-grid">' +
              '<label class="field"><span>Date of birth</span><input type="text" name="birth_date" autocomplete="bday" inputmode="numeric" maxlength="10" placeholder="MM/DD/YYYY" value="' + escapeHtml(formatBirthDate(profile.birth_date)) + '" required></label>' +
              '<label class="field select-field"><span>Sex</span><select name="sex" required>' +
                '<option value=""' + selected(sex, '') + '>Select</option>' +
                '<option value="male"' + selected(sex, 'male') + '>Male</option>' +
                '<option value="female"' + selected(sex, 'female') + '>Female</option>' +
                '<option value="other"' + selected(sex, 'other') + '>Other</option>' +
              '</select></label>' +
              '<label class="field wide select-field"><span>Civil status</span><select name="civil_status" required>' +
                '<option value=""' + selected(civilStatus, '') + '>Select</option>' +
                '<option value="single"' + selected(civilStatus, 'single') + '>Single</option>' +
                '<option value="married"' + selected(civilStatus, 'married') + '>Married</option>' +
                '<option value="widowed"' + selected(civilStatus, 'widowed') + '>Widowed</option>' +
                '<option value="separated"' + selected(civilStatus, 'separated') + '>Separated</option>' +
                '<option value="annulled"' + selected(civilStatus, 'annulled') + '>Annulled</option>' +
              '</select></label>' +
            '</div>' +
          '</div>' +
          '</div>' +
          '<div class="onb-foot">' +
            '<p class="onb-privacy">' + icon('lock') + '<span>Your information is private and only visible to authorized barangay staff.</span></p>' +
            '<button type="submit" class="btn-primary" id="onb-submit">Save and continue</button>' +
          '</div>' +
        '</form>' +
      '</div>';

    document.body.appendChild(overlay);
    document.body.style.overflow = 'hidden';
    return overlay;
  }

  async function fetchCsrf() {
    var response = await fetch((window.EB_BASE || '') + '/api/session', {
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

    trapFocus(overlay);

    var firstInput = form.querySelector('input[name="first_name"]');
    if (firstInput) {
      firstInput.focus();
      if (firstInput.value) firstInput.select();
    }

    function setInvalid(field, invalid) {
      var wrapper = field.closest('.field');
      if (wrapper) wrapper.classList.toggle('is-invalid', invalid);
      if (invalid) {
        field.setAttribute('aria-invalid', 'true');
      } else {
        field.removeAttribute('aria-invalid');
      }
    }

    function fail(message, field) {
      errorEl.textContent = message;
      errorEl.hidden = false;
      if (field) {
        setInvalid(field, true);
        field.focus();
      }
    }

    function clearFieldError(event) {
      var field = event.target;
      if (!field || !field.closest('.field')) return;
      setInvalid(field, false);
      if (!form.querySelector('.field.is-invalid')) errorEl.hidden = true;
    }

    form.addEventListener('input', clearFieldError);
    form.addEventListener('change', clearFieldError);

    form.addEventListener('submit', async function (event) {
      event.preventDefault();
      errorEl.hidden = true;

      var birthDate = parseBirthDate(form.birth_date.value);
      var data = {
        first_name: form.first_name.value.trim(),
        middle_name: form.middle_name.value.trim(),
        last_name: form.last_name.value.trim(),
        suffix: form.suffix.value.trim(),
        birth_date: birthDate,
        sex: form.sex.value,
        civil_status: form.civil_status.value,
      };

      if (!data.first_name) {
        fail('Enter your first name.', form.first_name);
        return;
      }
      if (!data.last_name) {
        fail('Enter your last name.', form.last_name);
        return;
      }
      if (!birthDate) {
        fail('Enter your date of birth as MM/DD/YYYY.', form.birth_date);
        return;
      }
      if (!data.sex) {
        fail('Select your sex.', form.sex);
        return;
      }
      if (!data.civil_status) {
        fail('Select your civil status.', form.civil_status);
        return;
      }

      submitBtn.disabled = true;
      submitBtn.classList.add('is-loading');
      submitBtn.setAttribute('aria-busy', 'true');
      submitBtn.innerHTML = '<span class="onb-spinner" aria-hidden="true"></span>Saving profile...';

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
          showToast('Profile saved. Welcome, ' + data.first_name + '.');
          return;
        }

        errorEl.textContent = (result && result.error) || 'We could not save your profile. Please try again.';
        errorEl.hidden = false;
      } catch (error) {
        errorEl.textContent = 'We could not save your profile. Please try again.';
        errorEl.hidden = false;
      } finally {
        submitBtn.disabled = false;
        submitBtn.classList.remove('is-loading');
        submitBtn.removeAttribute('aria-busy');
        submitBtn.textContent = 'Save and continue';
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }
})();
