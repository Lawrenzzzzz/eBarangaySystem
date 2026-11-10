document.addEventListener("DOMContentLoaded", () => {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfMeta ? csrfMeta.getAttribute("content") || "" : "";
    const endpoint = "../../backend/api/settings.php";

    const showAlert = (id, type, message) => {
        const element = document.getElementById(id);
        if (!element) {
            return;
        }
        element.className = `alert alert-${type} py-2`;
        element.textContent = message;
        element.classList.remove("d-none");
    };

    const hideAlert = (id) => {
        const element = document.getElementById(id);
        if (element) {
            element.classList.add("d-none");
        }
    };

    const send = async (action, payload) => {
        const response = await fetch(endpoint, {
            method: "POST",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-Token": csrf,
            },
            credentials: "same-origin",
            body: JSON.stringify({ action, ...payload }),
        });

        let data = {};
        try {
            data = await response.json();
        } catch (error) {
            data = {};
        }

        if (!response.ok || data.ok !== true) {
            throw new Error(data.error || "Unable to complete the request. Please try again.");
        }

        return data;
    };

    const withButtonState = async (button, busyLabel, task) => {
        const originalMarkup = button.innerHTML;
        button.disabled = true;
        button.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>${busyLabel}`;
        try {
            return await task();
        } finally {
            button.disabled = false;
            button.innerHTML = originalMarkup;
        }
    };

    const profileForm = document.getElementById("profile-form");
    if (profileForm) {
        profileForm.addEventListener("submit", async (event) => {
            event.preventDefault();
            hideAlert("profile-alert");

            const payload = {
                barangay_name: profileForm.barangay_name.value.trim(),
                municipality: profileForm.municipality.value.trim(),
                province: profileForm.province.value.trim(),
                chairman: profileForm.chairman.value.trim(),
                term: profileForm.term.value.trim(),
            };

            if (payload.barangay_name === "") {
                showAlert("profile-alert", "danger", "Enter the barangay name.");
                return;
            }

            const button = profileForm.querySelector('button[type="submit"]');
            try {
                const data = await withButtonState(button, "Saving\u2026", () => send("profile", payload));
                if (data.profile) {
                    const headerName = document.querySelector(".sidebar-header h6");
                    if (headerName) {
                        headerName.textContent = data.profile.barangay_name;
                    }
                    const cardName = document.querySelector(".user-card strong");
                    if (cardName) {
                        cardName.textContent = data.profile.chairman;
                    }
                    const cardTerm = document.querySelector(".user-card small:last-of-type");
                    if (cardTerm) {
                        cardTerm.textContent = `Chairman - Term ${data.profile.term}`;
                    }
                    document.title = `Barangay Profile - ${data.profile.barangay_name}`;
                }
                showAlert("profile-alert", "success", data.message || "Barangay profile saved.");
            } catch (error) {
                showAlert("profile-alert", "danger", error.message);
            }
        });
    }

    const officialForm = document.getElementById("official-form");
    if (officialForm) {
        officialForm.addEventListener("submit", async (event) => {
            event.preventDefault();
            hideAlert("official-alert");

            const payload = {
                full_name: officialForm.full_name.value.trim(),
                email: officialForm.email.value.trim(),
                phone: officialForm.phone.value.trim(),
                position: officialForm.position.value,
                password: officialForm.password.value,
                confirm_password: officialForm.confirm_password.value,
            };

            if (payload.full_name === "") {
                showAlert("official-alert", "danger", "Enter the official's full name.");
                return;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(payload.email)) {
                showAlert("official-alert", "danger", "Enter a valid email address.");
                return;
            }
            if (payload.position === "") {
                showAlert("official-alert", "danger", "Select a position.");
                return;
            }
            if (payload.password.length < 8) {
                showAlert("official-alert", "danger", "Password must be at least 8 characters long.");
                return;
            }
            if (payload.password !== payload.confirm_password) {
                showAlert("official-alert", "danger", "The password confirmation does not match.");
                return;
            }

            const button = officialForm.querySelector('button[type="submit"]');
            try {
                const data = await withButtonState(button, "Creating\u2026", () => send("official", payload));
                officialForm.reset();
                showAlert("official-alert", "success", data.message || "Official account created.");
            } catch (error) {
                showAlert("official-alert", "danger", error.message);
            }
        });
    }

    const passwordForm = document.getElementById("password-form");
    if (passwordForm) {
        passwordForm.addEventListener("submit", async (event) => {
            event.preventDefault();
            hideAlert("password-alert");

            const payload = {
                current_password: passwordForm.current_password.value,
                new_password: passwordForm.new_password.value,
                confirm_password: passwordForm.confirm_password.value,
            };

            if (payload.current_password === "") {
                showAlert("password-alert", "danger", "Enter your current password.");
                return;
            }
            if (payload.new_password.length < 8) {
                showAlert("password-alert", "danger", "Password must be at least 8 characters long.");
                return;
            }
            if (payload.new_password !== payload.confirm_password) {
                showAlert("password-alert", "danger", "The password confirmation does not match.");
                return;
            }

            const button = passwordForm.querySelector('button[type="submit"]');
            try {
                const data = await withButtonState(button, "Updating\u2026", () => send("password", payload));
                passwordForm.reset();
                showAlert("password-alert", "success", data.message || "Your password has been updated.");
            } catch (error) {
                showAlert("password-alert", "danger", error.message);
            }
        });
    }
});
