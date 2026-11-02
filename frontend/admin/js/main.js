// Main JS file for global functionality
console.log("Barangay Dashboard Loaded");

document.addEventListener("DOMContentLoaded", () => {
    const signOutButton = document.getElementById("admin-signout");
    if (!signOutButton) {
        return;
    }

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
});
