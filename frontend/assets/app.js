/* e-Barangay San Isidro — static front-end demo.
   No server: the "session" and "accounts" live in this browser's localStorage only.
   Passwords are never stored. */
(function () {
  const $ = (s, r = document) => r.querySelector(s),
    $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const store = {
    get(k) {
      try {
        return JSON.parse(localStorage.getItem(k));
      } catch (e) {
        return null;
      }
    },
    set(k, v) {
      try {
        localStorage.setItem(k, JSON.stringify(v));
      } catch (e) {}
    },
    del(k) {
      try {
        localStorage.removeItem(k);
      } catch (e) {}
    },
  };
  const session = () => store.get("bsi-session"),
    accounts = () => store.get("bsi-accounts") || {};
  const emailOk = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
  function toast(msg, type, ms) {
    const t = document.createElement("div");
    t.className = "toast " + (type || "");
    t.textContent = msg;
    $("#toasts").appendChild(t);
    setTimeout(() => t.remove(), ms || 5000);
  }
  const iconUse = (id) => $("#" + id).innerHTML;

  // ---- home: signed-in state, drawer, service links
  const inn = session();
  $$('[data-auth="in"]').forEach((e) => e.classList.toggle("hidden", !inn));
  $$('[data-auth="out"]').forEach((e) => e.classList.toggle("hidden", !!inn));
  $$("[data-service]").forEach((a) =>
    a.setAttribute("href", inn ? "account.php" : "login.php"),
  );
  const dr = $("#open-drawer");
  if (dr) {
    const b = document.body,
      close = () => b.classList.remove("drawer-open");
    dr.onclick = () => b.classList.add("drawer-open");
    $("#close-drawer").onclick = close;
    $("#drawer-bg").onclick = close;
    $$(".drawer nav a").forEach((a) => (a.onclick = close));
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") close();
    });
  }

  // ---- auth form
  const A = $("#auth");
  if (A) {
    const form = $("#a-form"),
      succ = $("#a-success"),
      err = $("#a-err"),
      submit = $("#a-submit");
    let mode = A.dataset.initial,
      type = "resident",
      verified = "",
      lastMsg = "";
    const T = {
      signin: [
        "Welcome back",
        "Sign in to access barangay services and updates.",
        "Sign in",
        "New to the barangay portal?",
        "signup.php",
        "Create an account",
      ],
      signup: [
        "Create your account",
        "Register as a resident or submit an official access request.",
        null,
        "Already registered?",
        "login.php",
        "Sign in",
      ],
      forgot: [
        "Reset your password",
        "We’ll send a secure reset link to your email.",
        "Send reset link",
        "Remembered your password?",
        null,
        null,
      ],
      success: ["Check your inbox", "", null, "", null, null],
    };
    function render() {
      const t = T[mode];
      $("#a-title").textContent = t[0];
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
      $("#official-fields").classList.toggle(
        "hidden",
        !(mode === "signup" && type === "official"),
      );
      submit.textContent =
        mode === "signup"
          ? type === "official"
            ? "Submit official request"
            : "Create resident account"
          : t[2];
      $("#a-foot").textContent = t[3];
      const l = $("#a-link"),
        b = $("#a-back");
      l.classList.toggle("hidden", !t[4]);
      b.classList.toggle("hidden", !!t[4]);
      if (t[4]) {
        l.href = t[4];
        l.textContent = t[5];
      }
      err.classList.add("hidden");
    }
    const setMode = (m) => {
      mode = m;
      render();
    };
    $$("[data-mode]", A).forEach(
      (b) => (b.onclick = () => setMode(b.dataset.mode)),
    );
    $$("[data-type]", A).forEach(
      (b) =>
        (b.onclick = () => {
          type = b.dataset.type;
          $$("[data-type]", A).forEach((x) =>
            x.classList.toggle("on", x === b),
          );
          render();
        }),
    );
    $("#pw-toggle").onclick = () => {
      const p = $("#password"),
        show = p.type === "password";
      p.type = show ? "text" : "password";
      $(".eye", A).classList.toggle("hidden", show);
      $(".eye-off", A).classList.toggle("hidden", !show);
    };
    const phone = form.elements.phone,
      vb = $("#verify-btn");
    const syncVerify = () => {
      const v = phone.value.trim(),
        ok = verified && verified === v;
      vb.textContent = ok ? "Verified" : "Verify";
      vb.className = "btn " + (ok ? "btn-secondary" : "btn-outline");
      vb.disabled = v.length < 7;
    };
    phone.oninput = syncVerify;
    const fail = (m) => {
      err.textContent = m;
      err.classList.remove("hidden");
    };
    const f = (n) => (form.elements[n]?.value || "").trim();

    form.onsubmit = (ev) => {
      ev.preventDefault();
      err.classList.add("hidden");
      if (mode === "signin") {
        if (!emailOk(f("email")) || form.elements.password.value.length < 8)
          return fail(
            "Enter a valid email and a password with at least 8 characters.",
          );
        const email = f("email").toLowerCase(),
          acc = accounts()[email] || {
            email,
            profile: {
              full_name: "Barangay Resident",
              phone: "Not provided",
              address: "To be completed",
              account_type: "resident",
            },
            application: null,
          };
        store.set("bsi-session", acc);
        location.href = "../account.php";
        return;
      }
      if (mode === "forgot") {
        if (!emailOk(f("email"))) return fail("Enter a valid email address.");
        lastMsg =
          "We sent a secure password reset link to your email. (Demo: open reset-password.php#type=recovery to try the next step.)";
        return setMode("success");
      }
      // signup
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
      )
        return fail(
          "Please complete all required fields with valid information.",
        );
      if (verified !== ph) {
        openPhone();
        return fail("Please verify your phone number first.");
      }
      if (type === "official" && (!f("position") || !f("employeeNumber")))
        return fail("Position and employee number are required for officials.");
      const official = type === "official",
        acc = {
          email: em.toLowerCase(),
          profile: {
            full_name: full,
            phone: ph,
            address: ad,
            household_number: f("householdNumber") || null,
            account_type: official ? "official_applicant" : "resident",
          },
          application: official
            ? { position: f("position"), status: "pending" }
            : null,
        };
      const all = accounts();
      all[acc.email] = acc;
      store.set("bsi-accounts", all);
      lastMsg = official
        ? "Account created (demo). Your official access request is pending review. Sign in to continue."
        : "Account created (demo). Sign in to continue.";
      setMode("success");
    };
    $("#google").onclick = () =>
      toast("Google sign-in is not available in the static demo.", "bad");

    // phone verification (demo: code shown on screen)
    const M = $("#phone-modal"),
      boxes = $$("#otp input"),
      vBtn = $("#pm-verify"),
      rs = $("#pm-resend"),
      pe = $("#pm-err");
    let sent = "",
      cd = 0,
      timer = null;
    const code = () => boxes.map((b) => b.value).join("");
    function send() {
      sent = String(Math.floor(100000 + Math.random() * 900000));
      boxes.forEach((b) => (b.value = ""));
      pe.classList.add("hidden");
      vBtn.disabled = true;
      toast(
        "Verification code for " + phone.value.trim() + ": " + sent,
        "",
        15000,
      );
      cd = 30;
      tick();
      boxes[0].focus();
    }
    function tick() {
      clearInterval(timer);
      const u = () => {
        rs.disabled = cd > 0;
        rs.textContent = cd > 0 ? "Resend in " + cd + "s" : "Resend code";
      };
      u();
      timer = setInterval(() => {
        cd--;
        u();
        if (cd <= 0) clearInterval(timer);
      }, 1000);
    }
    function openPhone() {
      if (phone.value.trim().length < 7) return;
      $("#pm-phone").textContent = phone.value.trim();
      M.classList.add("open");
      send();
    }
    const closePhone = () => M.classList.remove("open");
    vb.onclick = openPhone;
    $("#pm-close").onclick = closePhone;
    M.onclick = (e) => {
      if (e.target === M) closePhone();
    };
    rs.onclick = send;
    boxes.forEach((b, i) => {
      b.oninput = () => {
        b.value = b.value.replace(/\D/g, "").slice(-1);
        if (b.value && boxes[i + 1]) boxes[i + 1].focus();
        vBtn.disabled = code().length !== 6;
        pe.classList.add("hidden");
      };
      b.onkeydown = (e) => {
        if (e.key === "Backspace" && !b.value && boxes[i - 1])
          boxes[i - 1].focus();
      };
      b.onpaste = (e) => {
        const d = (e.clipboardData.getData("text") || "")
          .replace(/\D/g, "")
          .slice(0, 6);
        if (!d) return;
        e.preventDefault();
        boxes.forEach((x, j) => (x.value = d[j] || ""));
        vBtn.disabled = d.length !== 6;
      };
    });
    vBtn.onclick = () => {
      vBtn.disabled = true;
      setTimeout(() => {
        if (code() !== sent) {
          pe.textContent = "That code is incorrect. Please try again.";
          pe.classList.remove("hidden");
          vBtn.disabled = false;
          return;
        }
        verified = phone.value.trim();
        syncVerify();
        closePhone();
        err.classList.add("hidden");
        toast("Phone number verified. You can now create your account.", "ok");
      }, 400);
    };
    render();
    syncVerify();
  }

  // ---- reset password
  const R = $("#r-form");
  if (R) {
    const msg = $("#r-msg"),
      h = new URLSearchParams(location.hash.slice(1));
    const valid =
      h.get("type") === "recovery" || !!h.get("access_token") || !!session();
    R.classList.toggle("hidden", !valid);
    msg.classList.toggle("hidden", valid);
    if (!valid)
      msg.textContent = "This recovery link is invalid or has expired.";
    R.onsubmit = (e) => {
      e.preventDefault();
      const p = $("#r-pass").value,
        c = $("#r-conf").value,
        er = $("#r-err");
      if (p.length < 8 || p !== c) {
        er.textContent =
          "Passwords must match and contain at least 8 characters.";
        er.classList.remove("hidden");
        return;
      }
      if (!session())
        store.set("bsi-session", {
          email: "resident@example.com",
          profile: {
            full_name: "Barangay Resident",
            phone: "Not provided",
            address: "To be completed",
            account_type: "resident",
          },
          application: null,
        });
      location.href = "../account.php";
    };
  }

  // ---- account
  const C = $("#acct");
  if (C) {
    const s = session();
    if (!s) {
      location.replace("login.php");
      return;
    }
    const p = s.profile,
      a = s.application,
      set = (id, v) => ($("#" + id).textContent = v);
    C.hidden = false;
    set("u-first", (p.full_name || "Barangay resident").split(" ")[0]);
    set("u-email", s.email || "Not available");
    set("u-phone", p.phone);
    set("u-address", p.address);
    set(
      "u-type",
      p.account_type === "official_applicant"
        ? "Official applicant"
        : "Resident",
    );
    set(
      "s-title",
      a
        ? a.status === "pending"
          ? "Under review"
          : a.status
        : "Resident access",
    );
    set(
      "s-text",
      a
        ? a.position +
            " application. Barangay administrators will review your submitted credentials."
        : "Your resident account is active and ready for available online services.",
    );
    $("#s-ico").innerHTML = iconUse(
      a && a.status === "pending" ? "ico-clock" : "ico-ok",
    );
    $("#s-ico").style.display = "inline-flex";
    $("#signout").onclick = () => {
      store.del("bsi-session");
      location.replace("index.php");
    };
  }
})();
