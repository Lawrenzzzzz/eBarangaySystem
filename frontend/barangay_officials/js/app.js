document.addEventListener("DOMContentLoaded", () => {
  const searchBox = document.querySelector(".search-box");
  const searchInput = searchBox ? searchBox.querySelector('input[name="q"]') : null;
  const residentsTable = document.querySelector(".residents-table");

  if (searchBox && searchInput && residentsTable) {
    const tbody = residentsTable.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr[data-search]"));
    const emptyRow = tbody.querySelector("tr[data-resident-empty]");
    const emptyCell = emptyRow ? emptyRow.querySelector("td") : null;

    const applyFilter = () => {
      const term = searchInput.value.trim().toLowerCase();
      let visibleCount = 0;

      rows.forEach((row) => {
        const matches = term === "" || (row.dataset.search || "").includes(term);
        row.style.display = matches ? "" : "none";
        if (matches) {
          visibleCount += 1;
        }
      });

      if (emptyRow && emptyCell) {
        if (term !== "") {
          emptyCell.textContent = "No residents match your search.";
          emptyRow.style.display = visibleCount === 0 ? "" : "none";
        } else {
          emptyCell.textContent = "No residents found.";
          emptyRow.style.display = rows.length === 0 ? "" : "none";
        }
      }
    };

    searchBox.addEventListener("submit", (event) => {
      event.preventDefault();
      applyFilter();
    });

    searchInput.addEventListener("input", applyFilter);
    searchInput.addEventListener("search", applyFilter);
    searchInput.addEventListener("keydown", (event) => {
      if (event.key === "Enter") {
        event.preventDefault();
        applyFilter();
      } else if (event.key === "Escape") {
        searchInput.value = "";
        applyFilter();
      }
    });

    applyFilter();
  }

  const addResidentModal = document.getElementById("addResidentModal");
  if (addResidentModal) {
    const addForm = document.getElementById("add-resident-form");
    const codeEl = document.getElementById("add-resident-code");
    const errorEl = document.getElementById("add-resident-error");
    const successEl = document.getElementById("add-resident-success");
    const submitBtn = document.getElementById("add-resident-submit");
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfMeta ? csrfMeta.getAttribute("content") || "" : "";
    const parseBirthDate = (value) => {
      const match = String(value || "").trim().match(/^(\d{1,2})[/\-.](\d{1,2})[/\-.](\d{4})$/);
      if (!match) return "";
      const month = parseInt(match[1], 10);
      const day = parseInt(match[2], 10);
      const year = parseInt(match[3], 10);
      const date = new Date(Date.UTC(year, month - 1, day));
      if (
        date.getUTCFullYear() !== year ||
        date.getUTCMonth() !== month - 1 ||
        date.getUTCDate() !== day ||
        year < 1900 ||
        date.getTime() > Date.now()
      ) {
        return "";
      }
      return (
        year +
        "-" +
        String(month).padStart(2, "0") +
        "-" +
        String(day).padStart(2, "0")
      );
    };

    const clearAlerts = () => {
      errorEl.classList.add("d-none");
      successEl.classList.add("d-none");
    };
    const showError = (message) => {
      successEl.classList.add("d-none");
      errorEl.textContent = message;
      errorEl.classList.remove("d-none");
    };

    async function loadNextCode() {
      codeEl.textContent = "Loading\u2026";
      try {
        const response = await fetch(
          "../../backend/api/residents.php?action=next-code",
          { headers: { Accept: "application/json" }, credentials: "same-origin" },
        );
        const data = await response.json();
        codeEl.textContent = data && data.ok ? data.resident_code : "Unavailable";
      } catch (error) {
        codeEl.textContent = "Unavailable";
      }
    }

    const openBtn = document.querySelector('[data-bs-target="#addResidentModal"]');
    let backdrop = null;

    function openModal() {
      clearAlerts();
      addForm.reset();
      addResidentModal.style.display = "block";
      addResidentModal.classList.add("show");
      addResidentModal.removeAttribute("aria-hidden");
      addResidentModal.setAttribute("aria-modal", "true");
      document.body.classList.add("modal-open");
      document.body.style.overflow = "hidden";
      if (!backdrop) {
        backdrop = document.createElement("div");
        backdrop.className = "modal-backdrop fade show";
        document.body.appendChild(backdrop);
      }
      loadNextCode();
      const first = addForm.querySelector('input[name="first_name"]');
      if (first) first.focus();
    }

    function closeModal() {
      addResidentModal.style.display = "none";
      addResidentModal.classList.remove("show");
      addResidentModal.setAttribute("aria-hidden", "true");
      addResidentModal.removeAttribute("aria-modal");
      document.body.classList.remove("modal-open");
      document.body.style.overflow = "";
      if (backdrop) {
        backdrop.remove();
        backdrop = null;
      }
      addForm.reset();
      clearAlerts();
      codeEl.textContent = "Loading\u2026";
    }

    if (openBtn) {
      openBtn.addEventListener("click", openModal);
    }
    addResidentModal
      .querySelectorAll('[data-bs-dismiss="modal"]')
      .forEach((button) => button.addEventListener("click", closeModal));
    addResidentModal.addEventListener("click", (event) => {
      if (event.target === addResidentModal) closeModal();
    });
    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && addResidentModal.classList.contains("show")) {
        closeModal();
      }
    });

    addForm.addEventListener("submit", async (event) => {
      event.preventDefault();
      clearAlerts();

      const payload = {
        first_name: addForm.first_name.value.trim(),
        middle_name: addForm.middle_name.value.trim(),
        last_name: addForm.last_name.value.trim(),
        suffix: addForm.suffix.value.trim(),
        birth_date: parseBirthDate(addForm.birth_date.value),
        sex: addForm.sex.value,
        civil_status: addForm.civil_status.value,
        email: addForm.email.value.trim(),
        phone: addForm.phone.value.trim(),
        household_number: addForm.household_number.value.trim(),
        address_line: addForm.address_line.value.trim(),
      };

      if (!payload.first_name || !payload.last_name) {
        showError("Enter the resident's first and last name.");
        return;
      }
      if (!payload.birth_date) {
        showError("Enter the resident's date of birth as MM/DD/YYYY.");
        return;
      }
      if (!payload.sex || !payload.civil_status) {
        showError("Select the resident's sex and civil status.");
        return;
      }
      if (!payload.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(payload.email)) {
        showError("Enter a valid email address.");
        return;
      }
      if (payload.address_line.length < 5) {
        showError("Enter the resident's complete address.");
        return;
      }

      const originalMarkup = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving\u2026';

      try {
        const response = await fetch("../../backend/api/residents.php", {
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
          if (data.resident_code) codeEl.textContent = data.resident_code;
          successEl.textContent =
            (data.message || "Resident added successfully.") +
            " Refreshing the list\u2026";
          successEl.classList.remove("d-none");
          addForm.reset();
          setTimeout(() => location.reload(), 1400);
          return;
        }

        showError((data && data.error) || "Unable to add the resident.");
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalMarkup;
      } catch (error) {
        showError("Unable to reach the server. Please try again.");
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalMarkup;
      }
    });
  }

  const newAnnouncementModal = document.getElementById("newAnnouncementModal");
  if (newAnnouncementModal) {
    const announcementForm = document.getElementById("new-announcement-form");
    const errorEl = document.getElementById("new-announcement-error");
    const submitBtn = announcementForm.querySelector('button[type="submit"]');
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfMeta ? csrfMeta.getAttribute("content") || "" : "";

    const openBtn = document.querySelector('[data-bs-target="#newAnnouncementModal"]');
    let backdrop = null;

    const showError = (message) => {
      errorEl.textContent = message;
      errorEl.classList.remove("d-none");
    };

    function openModal() {
      errorEl.classList.add("d-none");
      announcementForm.reset();
      newAnnouncementModal.style.display = "block";
      newAnnouncementModal.classList.add("show");
      newAnnouncementModal.removeAttribute("aria-hidden");
      newAnnouncementModal.setAttribute("aria-modal", "true");
      document.body.classList.add("modal-open");
      document.body.style.overflow = "hidden";
      if (!backdrop) {
        backdrop = document.createElement("div");
        backdrop.className = "modal-backdrop fade show";
        document.body.appendChild(backdrop);
      }
      const typeField = announcementForm.querySelector("select[name='type']");
      if (typeField) typeField.focus();
    }

    function closeModal() {
      newAnnouncementModal.style.display = "none";
      newAnnouncementModal.classList.remove("show");
      newAnnouncementModal.setAttribute("aria-hidden", "true");
      newAnnouncementModal.removeAttribute("aria-modal");
      document.body.classList.remove("modal-open");
      document.body.style.overflow = "";
      if (backdrop) {
        backdrop.remove();
        backdrop = null;
      }
      announcementForm.reset();
      errorEl.classList.add("d-none");
    }

    announcementForm.addEventListener("submit", async (event) => {
      event.preventDefault();
      errorEl.classList.add("d-none");

      const type = announcementForm.type.value;
      const title = announcementForm.title.value.trim();
      const content = announcementForm.content.value.trim();

      if (!type) {
        showError("Select the announcement type.");
        return;
      }
      if (!title) {
        showError("Enter the announcement title.");
        return;
      }
      if (!content) {
        showError("Enter the announcement content.");
        return;
      }

      const originalMarkup = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Posting\u2026';

      try {
        const response = await fetch("../../backend/api/announcements.php", {
          method: "POST",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-CSRF-Token": csrf,
          },
          credentials: "same-origin",
          body: JSON.stringify({ type, title, content }),
        });
        const data = await response.json();

        if (data && data.ok === true) {
          location.reload();
          return;
        }

        showError((data && data.error) || "Unable to post the announcement.");
      } catch (error) {
        showError("Unable to reach the server. Please try again.");
      }

      submitBtn.disabled = false;
      submitBtn.innerHTML = originalMarkup;
    });

    if (openBtn) {
      openBtn.addEventListener("click", openModal);
    }
    newAnnouncementModal
      .querySelectorAll('[data-bs-dismiss="modal"]')
      .forEach((button) => button.addEventListener("click", closeModal));
    newAnnouncementModal.addEventListener("click", (event) => {
      if (event.target === newAnnouncementModal) closeModal();
    });
    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && newAnnouncementModal.classList.contains("show")) {
        closeModal();
      }
    });
  }

  const signOutButton = document.getElementById("official-signout");
  if (signOutButton) {
    signOutButton.addEventListener("click", async () => {
      signOutButton.disabled = true;
      try {
        const sessionResponse = await fetch("../../backend/api/session.php", {
          headers: { Accept: "application/json" },
          credentials: "same-origin",
        });
        const session = await sessionResponse.json();
        const logoutResponse = await fetch("../../backend/api/logout.php", {
          method: "POST",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-CSRF-Token": session.csrf || "",
          },
          credentials: "same-origin",
        });
        const result = await logoutResponse.json();
        location.href = result.redirect || "../login_page/index.php";
      } catch (error) {
        location.href = "../login_page/index.php";
      } finally {
        signOutButton.disabled = false;
      }
    });
  }
});
