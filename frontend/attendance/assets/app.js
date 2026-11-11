(function () {
  const $ = (s, r = document) => r.querySelector(s),
    $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const API = "../../backend/api";
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
        await api("/logout.php", { method: "POST", body: {} });
        location.href = "login.php";
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
          await api("/attendance-login.php", {
            method: "POST",
            body: { email, password },
          });
          location.href = "index.php";
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
          const data = await api("/forgot-password.php", {
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
})();
