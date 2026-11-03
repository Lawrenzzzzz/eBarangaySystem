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
