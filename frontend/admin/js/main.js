// Main JS file for global functionality
console.log("Barangay Dashboard Loaded");

const initAdminDashboard = (root) => {
    const endpoint = root.dataset.dashboardEndpoint || "../../backend/api/admin-dashboard.php";
    const numberFormat = new Intl.NumberFormat("en-PH");
    const currencyFormat = new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
        maximumFractionDigits: 0,
    });

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
            element.setAttribute("aria-valuenow", String(value));
        }
    };
    const money = (value) => currencyFormat.format(Number(value) || 0);

    const applyDashboard = (data) => {
        if (!data) {
            return;
        }

        const budget = data.budget || {};
        const residents = data.residents || {};

        setText("kpi-total-budget", money(budget.total));
        setText("kpi-total-budget-sub", `FY ${budget.year || ""} enacted`);
        setText("kpi-disbursed", money(budget.disbursed));
        setText("kpi-disbursed-sub", `${Number(budget.utilization || 0).toFixed(1)}% utilization`);

        setText("kpi-total-residents", numberFormat.format(residents.total || 0));
        setText("kpi-total-residents-sub", `${numberFormat.format(residents.new_this_month || 0)} new this month`);
        setText("kpi-voters", numberFormat.format(residents.voters || 0));
        setText("kpi-voters-sub", `${Number(residents.voter_pct || 0).toFixed(1)}% of residents (18+)`);

        setText("gender-male-count", numberFormat.format(residents.male || 0));
        setText("gender-male-pct", Math.round(Number(residents.male_pct || 0)));
        setText("gender-female-count", numberFormat.format(residents.female || 0));
        setText("gender-female-pct", Math.round(Number(residents.female_pct || 0)));
        setWidth("gender-male-bar", Number(residents.male_pct || 0));
        setWidth("gender-female-bar", Number(residents.female_pct || 0));

        if (typeof window.renderDashboardCharts === "function") {
            window.renderDashboardCharts(data);
        }
    };

    const refreshDashboard = async () => {
        try {
            const response = await fetch(endpoint, {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
                cache: "no-store",
            });
            if (!response.ok) {
                return;
            }

            const result = await response.json();
            if (result && result.ok && result.dashboard) {
                applyDashboard(result.dashboard);
            }
        } catch (error) {
            // Keep showing the last known values when the request fails.
        }
    };

    applyDashboard(window.DASHBOARD_DATA || null);
    const refreshInterval = window.setInterval(refreshDashboard, 5000);

    document.addEventListener("visibilitychange", () => {
        if (!document.hidden) {
            refreshDashboard();
        }
    });

    window.addEventListener("beforeunload", () => {
        window.clearInterval(refreshInterval);
    });
};

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

    const dashboardRoot = document.querySelector("[data-dashboard-endpoint]");
    if (dashboardRoot) {
        initAdminDashboard(dashboardRoot);
        return;
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
