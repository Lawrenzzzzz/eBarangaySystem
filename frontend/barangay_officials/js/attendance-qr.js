(function () {
  const box = document.getElementById("att-qr-code");
  if (!box || typeof qrcode === "undefined") return;
  const payload = box.dataset.payload || "";
  if (!payload) return;

  const qr = qrcode(0, "M");
  qr.addData(payload);
  qr.make();
  box.innerHTML = qr.createSvgTag({ scalable: true });

  function escapeHtml(value) {
    const div = document.createElement("div");
    div.textContent = value;
    return div.innerHTML;
  }

  const printBtn = document.getElementById("att-qr-print");
  if (!printBtn) return;

  printBtn.addEventListener("click", function () {
    const barangay = box.dataset.barangay || "Barangay San Isidro";
    const name = box.dataset.name || "Barangay Official";
    const position = box.dataset.position || "Barangay Official";
    const badgeSvg = qr.createSvgTag({ cellSize: 6, margin: 4 });

    const win = window.open("", "_blank", "width=560,height=720");
    if (!win) return;
    win.document.write(
      '<!doctype html><html lang="en"><head><meta charset="utf-8">' +
        "<title>Attendance Badge</title>" +
        "<style>" +
        "body{margin:0;font-family:'Segoe UI',system-ui,sans-serif;display:grid;place-items:center;min-height:100vh;background:#fff}" +
        ".badge{width:340px;border:2px solid #16412f;border-radius:16px;text-align:center;padding:28px 24px}" +
        ".badge h1{margin:0;font-size:18px;color:#16412f}" +
        ".badge .pos{font-size:11px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:#55645b}" +
        ".badge svg{width:220px;height:220px;margin-top:16px}" +
        ".badge .who{margin-top:16px;font-size:14px;font-weight:600;color:#14211b}" +
        ".badge .hint{margin:18px 0 0;font-size:11px;color:#55645b;line-height:1.6}" +
        "@media print{body{display:block;padding-top:24px}}" +
        "</style></head><body>" +
        '<div class="badge">' +
        "<h1>" + escapeHtml(barangay) + "</h1>" +
        '<div class="pos">Attendance Badge</div>' +
        badgeSvg +
        '<div class="who">' + escapeHtml(name) + " &middot; " + escapeHtml(position) + "</div>" +
        '<p class="hint">Scan this code at the attendance kiosk to record your time in and time out.</p>' +
        "</div></body></html>"
    );
    win.document.close();
    win.focus();
    setTimeout(function () { win.print(); }, 250);
  });
})();
