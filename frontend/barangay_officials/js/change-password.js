document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("change-password-form");
  if (!form) {
    return;
  }

  const alertEl = document.getElementById("change-password-alert");
  const submitBtn = form.querySelector('button[type="submit"]');
  const currentInput = form.querySelector('input[name="current_password"]');
  const newInput = form.querySelector('input[name="new_password"]');
  const confirmInput = form.querySelector('input[name="confirm_password"]');
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const csrf = csrfMeta ? csrfMeta.getAttribute("content") || "" : "";

  const showAlert = (message, type) => {
    if (!alertEl) return;
    alertEl.textContent = message;
    alertEl.className = "alert py-2 alert-" + type;
    alertEl.classList.remove("d-none");
  };

  const clearAlert = () => {
    if (!alertEl) return;
    alertEl.classList.add("d-none");
    alertEl.textContent = "";
  };

  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    clearAlert();

    const currentPassword = currentInput.value;
    const newPassword = newInput.value;
    const confirmPassword = confirmInput.value;

    if (currentPassword === "") {
      showAlert("Enter your current password.", "danger");
      currentInput.focus();
      return;
    }
    if (newPassword.length < 8) {
      showAlert("Password must be at least 8 characters long.", "danger");
      newInput.focus();
      return;
    }
    if (!/[A-Za-z]/.test(newPassword) || !/\d/.test(newPassword)) {
      showAlert("Password must contain at least one letter and one number.", "danger");
      newInput.focus();
      return;
    }
    if (newPassword !== confirmPassword) {
      showAlert("The password confirmation does not match.", "danger");
      confirmInput.focus();
      return;
    }
    if (newPassword === currentPassword) {
      showAlert("Choose a password that is different from your current one.", "danger");
      newInput.focus();
      return;
    }

    const originalMarkup = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving\u2026';

    try {
      const response = await fetch((window.EB_BASE || "") + "/api/settings", {
        method: "POST",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          "X-CSRF-Token": csrf,
        },
        credentials: "same-origin",
        body: JSON.stringify({
          action: "password",
          current_password: currentPassword,
          new_password: newPassword,
          confirm_password: confirmPassword,
        }),
      });
      const data = await response.json();

      if (data && data.ok === true) {
        form.reset();
        showAlert(data.message || "Your password has been updated.", "success");
      } else {
        showAlert((data && data.error) || "Unable to update your password.", "danger");
      }
    } catch (error) {
      showAlert("Unable to reach the server. Please try again.", "danger");
    }

    submitBtn.disabled = false;
    submitBtn.innerHTML = originalMarkup;
  });
});
