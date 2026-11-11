document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("document-fees-form");
  if (!form) {
    return;
  }

  const alertEl = document.getElementById("document-fees-alert");
  const submitBtn = form.querySelector('button[type="submit"]');
  const inputs = Array.from(form.querySelectorAll("[data-fee-code]"));
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

    const fees = {};
    for (const input of inputs) {
      const code = input.dataset.feeCode;
      const raw = input.value.trim();
      const value = Number(raw);

      if (raw === "" || !Number.isFinite(value) || value < 0) {
        showAlert("Enter a valid fee amount for every document.", "danger");
        input.focus();
        return;
      }

      fees[code] = Math.round(value * 100) / 100;
    }

    const originalMarkup = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving\u2026';

    try {
      const response = await fetch((window.EB_BASE || "") + "/api/document-fees", {
        method: "POST",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          "X-CSRF-Token": csrf,
        },
        credentials: "same-origin",
        body: JSON.stringify({ fees }),
      });
      const data = await response.json();

      if (data && data.ok === true) {
        if (Array.isArray(data.fees)) {
          data.fees.forEach((record) => {
            const input = form.querySelector('[data-fee-code="' + record.code + '"]');
            if (!input) return;
            const amount = Number(record.fee || 0);
            input.value = amount.toFixed(2);
            input.defaultValue = input.value;
          });
        }
        showAlert(data.message || "Document fees saved.", "success");
      } else {
        showAlert((data && data.error) || "Unable to save the document fees.", "danger");
      }
    } catch (error) {
      showAlert("Unable to reach the server. Please try again.", "danger");
    }

    submitBtn.disabled = false;
    submitBtn.innerHTML = originalMarkup;
  });
});
