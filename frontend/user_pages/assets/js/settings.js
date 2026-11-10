// Resident settings: profile information and password updates
(function () {
  var csrfMeta = document.querySelector('meta[name="csrf-token"]');
  var csrf = csrfMeta ? csrfMeta.getAttribute('content') || '' : '';
  var endpoint = '../../backend/api/settings.php';

  function showAlert(el, message, type) {
    if (!el) return;
    el.textContent = message;
    el.className = 'alert alert-' + type;
    el.hidden = false;
  }

  function clearAlert(el) {
    if (!el) return;
    el.hidden = true;
    el.textContent = '';
    el.className = 'alert';
  }

  function setLoading(button, loading, label) {
    if (loading) {
      button.dataset.label = button.textContent;
      button.disabled = true;
      button.textContent = 'Saving\u2026';
    } else {
      button.disabled = false;
      button.textContent = button.dataset.label || label;
    }
  }

  // ---------- Profile information ----------
  (function () {
    var form = document.getElementById('profile-form');
    if (!form) return;

    var alertEl = document.getElementById('profile-alert');
    var submitBtn = form.querySelector('button[type="submit"]');
    var photoInput = form.querySelector('input[name="photo"]');
    var preview = document.getElementById('profile-photo-preview');
    var nameInput = form.querySelector('input[name="full_name"]');
    var emailInput = form.querySelector('input[name="email"]');
    var phoneInput = form.querySelector('input[name="phone"]');
    var initialPreview = preview ? preview.innerHTML : '';
    var photoData = '';

    function restorePreview() {
      if (preview) preview.innerHTML = initialPreview;
    }

    if (photoInput) {
      photoInput.addEventListener('change', function () {
        var file = photoInput.files && photoInput.files[0] ? photoInput.files[0] : null;
        photoData = '';

        if (!file) {
          restorePreview();
          return;
        }

        var allowed = ['image/png', 'image/jpeg', 'image/webp'];
        if (allowed.indexOf(file.type) === -1) {
          showAlert(alertEl, 'Upload a JPG, PNG, or WebP image.', 'danger');
          photoInput.value = '';
          restorePreview();
          return;
        }
        if (file.size > 2 * 1024 * 1024) {
          showAlert(alertEl, 'The photo must be 2 MB or smaller.', 'danger');
          photoInput.value = '';
          restorePreview();
          return;
        }

        var reader = new FileReader();
        reader.onload = function () {
          photoData = typeof reader.result === 'string' ? reader.result : '';
          if (preview && photoData) {
            preview.innerHTML = '<img src="' + photoData + '" alt="Profile photo preview">';
          }
        };
        reader.readAsDataURL(file);
      });
    }

    form.addEventListener('reset', function () {
      photoData = '';
      clearAlert(alertEl);
      window.setTimeout(restorePreview, 0);
    });

    form.addEventListener('submit', async function (event) {
      event.preventDefault();
      clearAlert(alertEl);

      var fullName = nameInput.value.trim();
      var email = emailInput.value.trim();
      var phone = phoneInput.value.trim();

      if (fullName.length < 2 || !/^[\p{L}\s.'-]+$/u.test(fullName)) {
        showAlert(alertEl, 'Enter your complete name using letters only.', 'danger');
        nameInput.focus();
        return;
      }
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showAlert(alertEl, 'Enter a valid email address.', 'danger');
        emailInput.focus();
        return;
      }
      if (phone !== '' && !/^(09\d{9}|\+639\d{9})$/.test(phone)) {
        showAlert(alertEl, 'Enter a valid Philippine mobile number (09XXXXXXXXX).', 'danger');
        phoneInput.focus();
        return;
      }

      setLoading(submitBtn, true);

      try {
        var response = await fetch(endpoint, {
          method: 'POST',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrf,
          },
          credentials: 'same-origin',
          body: JSON.stringify({
            action: 'account_profile',
            full_name: fullName,
            email: email,
            phone: phone,
            photo: photoData,
          }),
        });
        var data = await response.json();

        if (data && data.ok === true) {
          showAlert(alertEl, data.message || 'Your profile has been updated.', 'success');
          window.setTimeout(function () { location.reload(); }, 900);
          return;
        }

        showAlert(alertEl, (data && data.error) || 'Unable to update your profile.', 'danger');
      } catch (error) {
        showAlert(alertEl, 'Unable to reach the server. Please try again.', 'danger');
      }

      setLoading(submitBtn, false, 'Save changes');
    });
  })();

  // ---------- Change password ----------
  (function () {
    var form = document.getElementById('password-form');
    if (!form) return;

    var alertEl = document.getElementById('password-alert');
    var submitBtn = form.querySelector('button[type="submit"]');
    var currentInput = form.querySelector('input[name="current_password"]');
    var newInput = form.querySelector('input[name="new_password"]');
    var confirmInput = form.querySelector('input[name="confirm_password"]');

    form.addEventListener('reset', function () {
      clearAlert(alertEl);
    });

    form.addEventListener('submit', async function (event) {
      event.preventDefault();
      clearAlert(alertEl);

      var currentPassword = currentInput.value;
      var newPassword = newInput.value;
      var confirmPassword = confirmInput.value;

      if (currentPassword === '') {
        showAlert(alertEl, 'Enter your current password.', 'danger');
        currentInput.focus();
        return;
      }
      if (newPassword.length < 8) {
        showAlert(alertEl, 'Password must be at least 8 characters long.', 'danger');
        newInput.focus();
        return;
      }
      if (!/[A-Za-z]/.test(newPassword) || !/\d/.test(newPassword)) {
        showAlert(alertEl, 'Password must contain at least one letter and one number.', 'danger');
        newInput.focus();
        return;
      }
      if (newPassword !== confirmPassword) {
        showAlert(alertEl, 'The password confirmation does not match.', 'danger');
        confirmInput.focus();
        return;
      }
      if (newPassword === currentPassword) {
        showAlert(alertEl, 'Choose a password that is different from your current one.', 'danger');
        newInput.focus();
        return;
      }

      setLoading(submitBtn, true);

      try {
        var response = await fetch(endpoint, {
          method: 'POST',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrf,
          },
          credentials: 'same-origin',
          body: JSON.stringify({
            action: 'password',
            current_password: currentPassword,
            new_password: newPassword,
            confirm_password: confirmPassword,
          }),
        });
        var data = await response.json();

        if (data && data.ok === true) {
          form.reset();
          showAlert(alertEl, data.message || 'Your password has been updated.', 'success');
        } else {
          showAlert(alertEl, (data && data.error) || 'Unable to update your password.', 'danger');
        }
      } catch (error) {
        showAlert(alertEl, 'Unable to reach the server. Please try again.', 'danger');
      }

      setLoading(submitBtn, false, 'Update password');
    });
  })();
})();
