document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("profile-form");
  if (!form) {
    return;
  }

  const alertEl = document.getElementById("profile-alert");
  const submitBtn = form.querySelector('button[type="submit"]');
  const photoInput = form.querySelector('input[name="photo"]');
  const preview = document.getElementById("profile-photo-preview");
  const nameInput = form.querySelector('input[name="full_name"]');
  const emailInput = form.querySelector('input[name="email"]');
  const phoneInput = form.querySelector('input[name="phone"]');
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const csrf = csrfMeta ? csrfMeta.getAttribute("content") || "" : "";

  const initialPreview = preview ? preview.getAttribute("src") || "" : "";
  let photoData = "";

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

  if (photoInput) {
    photoInput.addEventListener("change", () => {
      const file = photoInput.files && photoInput.files[0] ? photoInput.files[0] : null;
      photoData = "";

      if (!file) {
        if (preview) preview.src = initialPreview;
        return;
      }

      const allowed = ["image/png", "image/jpeg", "image/webp"];
      if (!allowed.includes(file.type)) {
        showAlert("Upload a JPG, PNG, or WebP image.", "danger");
        photoInput.value = "";
        if (preview) preview.src = initialPreview;
        return;
      }
      if (file.size > 2 * 1024 * 1024) {
        showAlert("The photo must be 2 MB or smaller.", "danger");
        photoInput.value = "";
        if (preview) preview.src = initialPreview;
        return;
      }

      const reader = new FileReader();
      reader.onload = () => {
        photoData = typeof reader.result === "string" ? reader.result : "";
        if (preview && photoData) preview.src = photoData;
      };
      reader.readAsDataURL(file);
    });
  }

  form.addEventListener("reset", () => {
    photoData = "";
    clearAlert();
    window.setTimeout(() => {
      if (preview) preview.src = initialPreview;
    }, 0);
  });

  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    clearAlert();

    const fullName = nameInput.value.trim();
    const email = emailInput.value.trim();
    const phone = phoneInput.value.trim();

    if (fullName.length < 2 || !/^[\p{L}\s.'-]+$/u.test(fullName)) {
      showAlert("Enter your complete name using letters only.", "danger");
      nameInput.focus();
      return;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      showAlert("Enter a valid email address.", "danger");
      emailInput.focus();
      return;
    }
    if (phone !== "" && !/^(09\d{9}|\+639\d{9})$/.test(phone)) {
      showAlert("Enter a valid Philippine mobile number (09XXXXXXXXX).", "danger");
      phoneInput.focus();
      return;
    }

    const payload = {
      action: "account_profile",
      full_name: fullName,
      email,
      phone,
      photo: photoData,
    };

    const originalMarkup = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving\u2026';

    try {
      const response = await fetch("../../backend/api/settings.php", {
        method: "POST",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          "X-CSRF-Token": csrf,
        },
        credentials: "same-origin",
        body: JSON.stringify(payload),
      });
      const data = await response.json();

      if (data && data.ok === true) {
        showAlert(data.message || "Your profile has been updated.", "success");
        window.setTimeout(() => location.reload(), 900);
        return;
      }

      showAlert((data && data.error) || "Unable to update your profile.", "danger");
    } catch (error) {
      showAlert("Unable to reach the server. Please try again.", "danger");
    }

    submitBtn.disabled = false;
    submitBtn.innerHTML = originalMarkup;
  });
});
