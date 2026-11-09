// Main JS file for global functionality
console.log("Barangay Dashboard Loaded");

document.addEventListener("DOMContentLoaded", () => {
    const signOutButton = document.getElementById("admin-signout");
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
                location.href = result.redirect || "../login_page/login.php";
            } catch (error) {
                location.href = "../login_page/login.php";
            } finally {
                signOutButton.disabled = false;
            }
        });
    }

    const totalResidents = document.getElementById("kpi-total-residents");
    if (!totalResidents) {
        return;
    }

    const numberFormat = new Intl.NumberFormat("en-US");
    const setText = (id, value) => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    };
    const setWidth = (id, value) => {
        const element = document.getElementById(id);
        if (element) {
            element.style.width = `${value}%`;
        }
    };

    const refreshResidentStats = async () => {
        try {
            const response = await fetch("../../backend/api/residents.php?action=stats", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            if (!response.ok) {
                return;
            }

            const result = await response.json();
            const stats = result && result.ok ? result.stats : null;
            if (!stats) {
                return;
            }

            setText("kpi-total-residents", numberFormat.format(stats.total));
            setText("kpi-voters", numberFormat.format(stats.voters));
            setText("kpi-voter-pct", `${Number(stats.voter_pct).toFixed(1)}% of residents (18+)`);
            setText("kpi-new-month", numberFormat.format(stats.new_this_month));

            setText("gender-male-pct", stats.male_pct);
            setText("gender-female-pct", stats.female_pct);
            setWidth("gender-male-bar", stats.male_pct);
            setWidth("gender-female-bar", stats.female_pct);

            setText("age-0-17", numberFormat.format(stats.age_0_17));
            setText("age-18-34", numberFormat.format(stats.age_18_34));
            setText("age-35-59", numberFormat.format(stats.age_35_59));
            setText("age-60-plus", numberFormat.format(stats.age_60_plus));
        } catch (error) {
            // Keep the last known values when the request fails.
        }
    };

    refreshResidentStats();
    window.setInterval(refreshResidentStats, 15000);
});
