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

  const revealEls = $$("[data-reveal]");
  if (revealEls.length) {
    document.documentElement.classList.add("reveal-ready");
    if ("IntersectionObserver" in window) {
      const io = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.classList.add("reveal-in");
              io.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.16, rootMargin: "0px 0px -8% 0px" },
      );
      revealEls.forEach((el) => io.observe(el));
    } else {
      revealEls.forEach((el) => el.classList.add("reveal-in"));
    }
  }

  const dr = $("#open-drawer");
  if (dr) {
    const b = document.body,
      close = () => b.classList.remove("drawer-open");
    dr.onclick = () => b.classList.add("drawer-open");
    const closeButton = $("#close-drawer"),
      backdrop = $("#drawer-bg");
    if (closeButton) closeButton.onclick = close;
    if (backdrop) backdrop.onclick = close;
    $$(".drawer nav a").forEach((a) => (a.onclick = close));
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") close();
    });
  }

  const A = $("#auth");
  if (A) {
    const form = $("#a-form"),
      succ = $("#a-success"),
      err = $("#a-err"),
      submit = $("#a-submit");
    let mode = A.dataset.initial,
      verifiedEmail = "",
      emailProof = "",
      emailVerificationId = null,
      lastMsg = "",
      lastKind = "forgot";
    const T = {
      signin: [
        "Welcome back",
        "Sign in to access barangay services and updates.",
        "Sign in",
        "New to the barangay portal?",
        "/signup",
        "Create an account",
      ],
      signup: [
        "Create your account",
        "Register as a resident to access barangay services.",
        null,
        "Already registered?",
        "/login",
        "Sign in",
      ],
      forgot: [
        "Reset your password",
        "We'll send a secure reset link to your email.",
        "Send reset link",
        "Remembered your password?",
        null,
        null,
      ],
      success: ["Check your inbox", "", null, "", null, null],
    };
    function render() {
      const t = T[mode];
      $("#a-title").textContent =
        mode === "success" && lastKind === "signup"
          ? "Account created"
          : t[0];
      $("#a-sub").textContent = mode === "success" ? lastMsg : t[1];
      succ.classList.toggle("hidden", mode !== "success");
      form.classList.toggle("hidden", mode === "success");
      $("#a-ok").textContent = lastMsg;
      $$("[data-modes]", form).forEach((el) =>
        el.classList.toggle(
          "hidden",
          !el.dataset.modes.split(" ").includes(mode),
        ),
      );
      submit.textContent = mode === "signup" ? "Create resident account" : t[2];
      $("#a-foot").textContent = t[3];
      const l = $("#a-link"),
        b = $("#a-back");
      l.classList.toggle("hidden", !t[4]);
      b.classList.toggle("hidden", !!t[4]);
      if (t[4]) {
        l.href = BASE + t[4];
        l.textContent = t[5];
      }
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
    const f = (n) => (form.elements[n]?.value || "").trim();

    const email = form.elements.email,
      evb = $("#email-verify-btn");
    const EM = $("#email-modal"),
      emBoxes = $$("#email-otp input"),
      emBtn = $("#em-verify"),
      emRs = $("#em-resend"),
      emErr = $("#em-err");
    let emCd = 0,
      emTimer = null;
    const emCode = () => emBoxes.map((b) => b.value).join("");
    const syncEmailVerify = () => {
      const v = email.value.trim(),
        ok = !!verifiedEmail && verifiedEmail === v;
      if (!ok) emailProof = "";
      evb.textContent = ok ? "Verified" : "Verify";
      evb.classList.toggle("btn-secondary", ok);
      evb.classList.toggle("btn-outline", !ok);
      evb.disabled = !emailOk(v);
    };
    email.oninput = syncEmailVerify;
    function emTick() {
      clearInterval(emTimer);
      const u = () => {
        emRs.disabled = emCd > 0;
        emRs.textContent = emCd > 0 ? "Resend in " + emCd + "s" : "Resend code";
      };
      u();
      emTimer = setInterval(() => {
        emCd--;
        u();
        if (emCd <= 0) clearInterval(emTimer);
      }, 1000);
    }
    async function sendEmailCode() {
      const value = email.value.trim();
      if (!emailOk(value)) {
        toast("Enter a valid email address first.", "bad");
        return;
      }
      $("#em-email").textContent = value;
      EM.classList.add("open");
      emBoxes.forEach((b) => (b.value = ""));
      emBtn.disabled = true;
      emErr.classList.add("hidden");
      emRs.disabled = true;
      emRs.textContent = "Sending...";
      try {
        const data = await api("/email-send", {
          method: "POST",
          body: { email: value },
        });
        emailVerificationId = data.verification_id;
        if (data.debug_code) {
          toast(
            "Verification code for " + value + ": " + data.debug_code,
            "",
            15000,
          );
        } else {
          toast("Verification code sent to " + value + ".", "ok", 5000);
        }
        emCd = 30;
        emTick();
        emBoxes[0].focus();
      } catch (e) {
        if (e.data && e.data.retry_after) {
          emCd = e.data.retry_after;
          emTick();
        } else {
          emRs.disabled = false;
          emRs.textContent = "Resend code";
        }
        emErr.textContent = e.message;
        emErr.classList.remove("hidden");
      }
    }
    function openEmail() {
      if (!emailOk(email.value.trim())) return;
      sendEmailCode();
    }
    const closeEmail = () => EM.classList.remove("open");
    evb.onclick = openEmail;
    $("#em-close").onclick = closeEmail;
    EM.onclick = (e) => {
      if (e.target === EM) closeEmail();
    };
    emRs.onclick = sendEmailCode;
    emBoxes.forEach((b, i) => {
      b.oninput = () => {
        b.value = b.value.replace(/\D/g, "").slice(-1);
        if (b.value && emBoxes[i + 1]) emBoxes[i + 1].focus();
        emBtn.disabled = emCode().length !== 6;
        emErr.classList.add("hidden");
      };
      b.onkeydown = (e) => {
        if (e.key === "Backspace" && !b.value && emBoxes[i - 1])
          emBoxes[i - 1].focus();
      };
      b.onpaste = (e) => {
        const d = (e.clipboardData.getData("text") || "")
          .replace(/\D/g, "")
          .slice(0, 6);
        if (!d) return;
        e.preventDefault();
        emBoxes.forEach((x, j) => (x.value = d[j] || ""));
        emBtn.disabled = d.length !== 6;
      };
    });
    emBtn.onclick = async () => {
      const entered = emCode();
      if (entered.length !== 6 || !emailVerificationId) return;
      emBtn.disabled = true;
      emErr.classList.add("hidden");
      try {
        const data = await api("/email-verify", {
          method: "POST",
          body: { verification_id: emailVerificationId, code: entered },
        });
        verifiedEmail = email.value.trim();
        emailProof = data.email_proof;
        emailVerificationId = null;
        syncEmailVerify();
        closeEmail();
        err.classList.add("hidden");
        toast("Email verified. You can now create your account.", "ok");
      } catch (e) {
        emErr.textContent = e.message;
        emErr.classList.remove("hidden");
        emBtn.disabled = false;
      }
    };

    form.onsubmit = async (ev) => {
      ev.preventDefault();
      err.classList.add("hidden");
      if (mode === "signin") {
        if (!emailOk(f("email")) || form.elements.password.value.length < 8) {
          return fail(
            "Enter a valid email and a password with at least 8 characters.",
          );
        }
        submit.disabled = true;
        try {
          const data = await api("/login", {
            method: "POST",
            body: {
              email: f("email"),
              password: form.elements.password.value,
            },
          });
          location.href = data.redirect || BASE + "/dashboard";
        } catch (e) {
          fail(e.message);
          submit.disabled = false;
        }
        return;
      }
      if (mode === "forgot") {
        if (!emailOk(f("email"))) return fail("Enter a valid email address.");
        submit.disabled = true;
        try {
          const data = await api("/forgot-password", {
            method: "POST",
            body: { email: f("email") },
          });
          lastKind = "forgot";
          lastMsg = data.message || "If an account exists, a reset link has been sent.";
          if (data.debug_reset_url) {
            toast(data.debug_reset_url, "", 20000);
          }
          setMode("success");
        } catch (e) {
          fail(e.message);
        } finally {
          submit.disabled = false;
        }
        return;
      }
      const full = f("fullName"),
        em = f("email"),
        ph = f("phone"),
        ad = f("address"),
        pw = form.elements.password.value;
      if (
        full.length < 2 ||
        !emailOk(em) ||
        ph.length < 7 ||
        ad.length < 5 ||
        pw.length < 8
      ) {
        return fail(
          "Please complete all required fields with valid information.",
        );
      }
      if (verifiedEmail !== em || !emailProof) {
        openEmail();
        return fail("Please verify your email address first.");
      }
      submit.disabled = true;
      try {
        const data = await api("/signup", {
          method: "POST",
          body: {
            fullName: full,
            email: em,
            phone: ph,
            address: ad,
            householdNumber: f("householdNumber"),
            password: pw,
            emailProof,
          },
        });
        lastKind = "signup";
        lastMsg = data.message || "Account created. You can now sign in.";
        toast(lastMsg, "ok");
        location.href =
          BASE + "/login?registered=1&email=" + encodeURIComponent(em);
      } catch (e) {
        fail(e.message);
        submit.disabled = false;
      }
    };
    render();
    syncEmailVerify();

    const params = new URLSearchParams(location.search);
    const prefillEmail = (params.get("email") || "").trim();
    if (prefillEmail && email) {
      email.value = prefillEmail;
      syncEmailVerify();
      if (params.get("registered") === "1") $("#password")?.focus();
    }
    if (params.get("registered") === "1") {
      toast("Account created. Sign in to continue.", "ok", 6000);
    }
  }

  const R = $("#r-form");
  if (R) {
    const msg = $("#r-msg"),
      query = new URLSearchParams(location.search),
      hash = new URLSearchParams(location.hash.slice(1));
    const token = query.get("token") || hash.get("token") || "";
    const valid = token.length >= 32;
    R.classList.toggle("hidden", !valid);
    msg.classList.toggle("hidden", valid);
    if (!valid) msg.textContent = "This recovery link is invalid or has expired.";
    R.onsubmit = async (e) => {
      e.preventDefault();
      const p = $("#r-pass").value,
        c = $("#r-conf").value,
        er = $("#r-err"),
        button = $("#r-submit");
      if (p.length < 8 || p !== c) {
        er.textContent = "Passwords must match and contain at least 8 characters.";
        er.classList.remove("hidden");
        return;
      }
      er.classList.add("hidden");
      if (button) button.disabled = true;
      try {
        await api("/reset-password", {
          method: "POST",
          body: { token, password: p, confirmPassword: c },
        });
        toast("Password updated. You can now sign in.", "ok");
        setTimeout(() => (location.href = BASE + "/login"), 800);
      } catch (err) {
        er.textContent = err.message;
        er.classList.remove("hidden");
        if (button) button.disabled = false;
      }
    };
  }

  (async () => {
    try {
      const data = await api("/session");
      if (data.csrf) csrf = data.csrf;
      const signedIn = !!data.authenticated;
      $$('[data-auth="out"]').forEach((e) =>
        e.classList.toggle("hidden", signedIn),
      );
      const role = (data.user && data.user.role_code) || "resident";
      const dashboard = ["admin", "captain", "super_admin"].includes(role)
        ? BASE + "/admin/dashboard"
        : ["official", "staff", "kagawad", "secretary", "treasurer"].includes(
              role,
            )
          ? BASE + "/official/dashboard"
          : BASE + "/dashboard";
      $$('[data-auth="in"]').forEach((e) => {
        e.classList.toggle("hidden", !signedIn);
        e.setAttribute("href", dashboard);
      });
      const destinations = {
        documents: BASE + "/documents",
        assistance: BASE + "/dashboard",
        concern: BASE + "/blotter",
      };
      $$("[data-service]").forEach((a) => {
        a.setAttribute(
          "href",
          signedIn
            ? destinations[a.dataset.service] || BASE + "/dashboard"
            : BASE + "/login",
        );
      });
    } catch (e) {}
  })();
})();
