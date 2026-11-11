(function () {
  const $ = (s, r = document) => r.querySelector(s),
    $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const BASE = window.EB_BASE || "";
  const API = BASE + "/api";
  let csrf =
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") ||
    "";
  const emailOk = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);

  function toast(msg, type, ms) {
    const holder = $("#toasts");
    if (!holder) return;
    const t = document.createElement("div");
    t.className = "toast " + (type || "");
    t.textContent = msg;
    holder.appendChild(t);
    setTimeout(() => t.remove(), ms || 5000);
  }

  async function api(path, options = {}) {
    const method = options.method || "GET";
    const headers = { Accept: "application/json" };
    if (options.body !== undefined) headers["Content-Type"] = "application/json";
    if (csrf) headers["X-CSRF-Token"] = csrf;
    let response;
    try {
      response = await fetch(API + path, {
        method,
        headers,
        credentials: "same-origin",
        body:
          options.body !== undefined ? JSON.stringify(options.body) : undefined,
      });
    } catch (e) {
      const error = new Error(
        "Unable to reach the barangay server. Check your connection and try again.",
      );
      error.status = 0;
      throw error;
    }
    let data = null;
    try {
      data = await response.json();
    } catch (e) {}
    if (!response.ok || !data || data.ok === false) {
      const error = new Error(
        (data && data.error) || "Something went wrong. Please try again.",
      );
      error.status = response.status;
      error.data = data;
      throw error;
    }
    return data;
  }

  const signout = $("#att-signout");
  if (signout) {
    signout.onclick = async () => {
      signout.disabled = true;
      try {
        await api("/logout", { method: "POST", body: {} });
        location.href = BASE + "/attendance/login";
      } catch (e) {
        toast(e.message, "bad");
        signout.disabled = false;
      }
    };
  }

  const A = $("#auth");
  if (A) {
    const form = $("#a-form"),
      succ = $("#a-success"),
      err = $("#a-err"),
      submit = $("#a-submit");
    let mode = A.dataset.initial,
      lastMsg = "";
    const T = {
      signin: [
        "Welcome back",
        "Sign in with your admin account to record attendance.",
        "Sign in",
      ],
      forgot: [
        "Reset your password",
        "We'll send a secure reset link to your email.",
        "Send reset link",
      ],
    };

    function render() {
      if (mode === "success") {
        $("#a-title").textContent = "Check your inbox";
        $("#a-sub").textContent = lastMsg;
        succ.classList.remove("hidden");
        form.classList.add("hidden");
        $("#a-ok").textContent = lastMsg;
        err.classList.add("hidden");
        return;
      }
      const t = T[mode];
      $("#a-title").textContent = t[0];
      $("#a-sub").textContent = t[1];
      succ.classList.add("hidden");
      form.classList.remove("hidden");
      $$("[data-modes]", form).forEach((el) =>
        el.classList.toggle(
          "hidden",
          !el.dataset.modes.split(" ").includes(mode),
        ),
      );
      submit.textContent = t[2];
      err.classList.add("hidden");
    }

    const setMode = (m) => {
      mode = m;
      submit.disabled = false;
      render();
    };

    $$("[data-mode]", A).forEach(
      (b) => (b.onclick = () => setMode(b.dataset.mode)),
    );

    $("#pw-toggle").onclick = () => {
      const p = $("#password"),
        show = p.type === "password";
      p.type = show ? "text" : "password";
      $(".eye", A).classList.toggle("hidden", show);
      $(".eye-off", A).classList.toggle("hidden", !show);
    };

    const fail = (m) => {
      err.textContent = m;
      err.classList.remove("hidden");
    };

    form.onsubmit = async (ev) => {
      ev.preventDefault();
      err.classList.add("hidden");
      const email = (form.elements.email.value || "").trim();
      if (mode === "signin") {
        const password = form.elements.password.value;
        if (!emailOk(email) || password.length < 8) {
          return fail(
            "Enter a valid email and a password with at least 8 characters.",
          );
        }
        submit.disabled = true;
        try {
          await api("/attendance-login", {
            method: "POST",
            body: { email, password },
          });
          location.href = BASE + "/attendance";
        } catch (e) {
          fail(e.message);
          submit.disabled = false;
        }
        return;
      }
      if (mode === "forgot") {
        if (!emailOk(email)) return fail("Enter a valid email address.");
        submit.disabled = true;
        try {
          const data = await api("/forgot-password", {
            method: "POST",
            body: { email },
          });
          lastMsg =
            data.message || "If an account exists, a reset link has been sent.";
          if (data.debug_reset_url) toast(data.debug_reset_url, "", 20000);
          setMode("success");
        } catch (e) {
          fail(e.message);
        } finally {
          submit.disabled = false;
        }
      }
    };

    render();

    const params = new URLSearchParams(location.search);
    const prefillEmail = (params.get("email") || "").trim();
    if (prefillEmail && form.elements.email) {
      form.elements.email.value = prefillEmail;
    }
  }

  /* ------------------------------------------------------------------ */
  /* Attendance scanner dashboard                                        */
  /* ------------------------------------------------------------------ */

  const reader = $("#att-reader");
  if (reader && typeof Html5Qrcode !== "undefined") {
    const camHint = $("#att-cam-hint"),
      camError = $("#att-cam-error"),
      startBtn = $("#att-cam-start"),
      stopBtn = $("#att-cam-stop"),
      result = $("#att-result"),
      resultBadge = $("#att-result-badge"),
      resultName = $("#att-result-name"),
      resultTime = $("#att-result-time"),
      logRows = $("#att-log-rows"),
      modal = $("#att-modal"),
      modalBadge = $("#att-modal-badge"),
      modalName = $("#att-modal-name"),
      modalSub = $("#att-modal-sub"),
      modalIco = $("#att-modal-ico"),
      modalOk = $("#att-modal-ok"),
      modalClose = $("#att-modal-close");

    let scanner = null;
    let cameraOn = false;
    let busy = false;
    let hideTimer = 0;
    let modalTimer = 0;
    let activeText = "";
    let lastHandled = { text: "", at: 0 };

    function setCameraState(on) {
      cameraOn = on;
      reader.classList.toggle("hidden", !on);
      camHint.classList.toggle("hidden", on);
      startBtn.classList.toggle("hidden", on);
      stopBtn.classList.toggle("hidden", !on);
    }

    function showCameraError(message) {
      camError.textContent = message;
      camError.classList.remove("hidden");
    }

    function showResult(data, message) {
      result.classList.remove("hidden", "in", "out", "err");
      if (data === null) {
        result.classList.add("err");
        resultBadge.textContent = "NOT RECORDED";
        resultName.textContent = "";
        resultTime.textContent = message;
      } else {
        result.classList.add(data.action);
        resultBadge.textContent = data.action === "in" ? "TIME IN" : "TIME OUT";
        resultName.textContent = data.name;
        resultTime.textContent = data.message;
      }
      clearTimeout(hideTimer);
      hideTimer = setTimeout(() => result.classList.add("hidden"), 8000);
    }

    function closeModal() {
      clearTimeout(modalTimer);
      if (modal) modal.classList.remove("open");
    }

    function showModal(data) {
      if (!modal) return;
      modalBadge.textContent = data.action === "in" ? "TIME IN" : "TIME OUT";
      modalBadge.classList.toggle("out", data.action === "out");
      modalIco.classList.toggle("out", data.action === "out");
      modalName.textContent = data.name;
      modalSub.textContent = data.message;
      modal.classList.add("open");
      clearTimeout(modalTimer);
      modalTimer = setTimeout(closeModal, 6000);
    }

    if (modal) {
      modalOk.onclick = closeModal;
      modalClose.onclick = closeModal;
      modal.addEventListener("click", (ev) => {
        if (ev.target === modal) closeModal();
      });
      document.addEventListener("keydown", (ev) => {
        if (ev.key === "Escape") closeModal();
      });
    }

    function renderLog(records) {
      if (!logRows) return;
      logRows.textContent = "";
      if (!records.length) {
        const tr = document.createElement("tr");
        tr.className = "att-empty";
        const td = document.createElement("td");
        td.colSpan = 5;
        td.textContent = "No one has timed in yet today.";
        tr.appendChild(td);
        logRows.appendChild(tr);
        return;
      }
      for (const record of records) {
        const tr = document.createElement("tr");
        const cells = [
          { text: record.name },
          { text: record.position },
          { text: record.time_in, pill: "att-pill-in" },
          {
            text: record.time_out || "—",
            pill: record.time_out ? "att-pill-out" : null,
            muted: !record.time_out,
          },
          { text: record.hours || "—" },
        ];
        for (const cell of cells) {
          const td = document.createElement("td");
          const span = document.createElement("span");
          if (cell.pill) span.className = "att-pill " + cell.pill;
          else if (cell.muted) span.className = "att-muted";
          span.textContent = cell.text;
          td.appendChild(span);
          tr.appendChild(td);
        }
        logRows.appendChild(tr);
      }
    }

    async function refreshLog() {
      try {
        const data = await api("/attendance-scan");
        renderLog(data.records || []);
      } catch (e) {}
    }

    async function record(body) {
      if (busy) return;
      busy = true;
      try {
        const data = await api("/attendance-scan", {
          method: "POST",
          body,
        });
        showResult(data, null);
        showModal(data);
        toast(data.message, "ok");
        await refreshLog();
      } catch (e) {
        showResult(null, e.message);
        toast(e.message, "bad");
      } finally {
        busy = false;
      }
    }

    function onDecoded(text) {
      if (text === activeText) return;
      activeText = text;
      const now = Date.now();
      if (text === lastHandled.text && now - lastHandled.at < 3000) return;
      lastHandled = { text, at: now };
      record({ payload: text });
    }

    function onDecodeFailure() {
      activeText = "";
    }

    async function startCamera() {
      if (cameraOn) return;
      camError.classList.add("hidden");
      try {
        scanner = scanner || new Html5Qrcode("att-reader");
        await scanner.start(
          { facingMode: "environment" },
          { fps: 10, qrbox: { width: 250, height: 250 } },
          onDecoded,
          onDecodeFailure
        );
        setCameraState(true);
      } catch (e) {
        showCameraError(
          "Unable to start the camera. Allow camera access for this page (cameras work on localhost or HTTPS), then try again."
        );
      }
    }

    async function stopCamera() {
      if (!scanner || !cameraOn) return;
      try {
        await scanner.stop();
        scanner.clear();
      } catch (e) {}
      setCameraState(false);
    }

    startBtn.onclick = startCamera;
    stopBtn.onclick = stopCamera;

    startCamera();
  }
})();
