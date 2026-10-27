<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | Barangay San Isidro</title>
    <meta name="description" content="Set a new password for your Barangay San Isidro account.">
    <meta property="og:title" content="Reset Password | Barangay San Isidro">
    <meta property="og:description" content="Securely reset your barangay account password.">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600&family=Manrope:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<main class="reset-page">
    <section class="reset-card">
        <span class="ico">
            <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M2.59 17.41A2 2 0 0 0 2 18.83V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.17a2 2 0 0 0 1.42-.59l.81-.81a6.5 6.5 0 1 0-4-4z"/>
                <circle cx="16.5" cy="7.5" r=".5"/>
            </svg>
        </span>
        <h1>Choose a new password</h1>
        <p>Use at least 8 characters that you don’t use elsewhere.</p>
        <form id="r-form" class="stack hidden" style="margin-top:1.75rem" novalidate>
            <label class="field"><span class="label">New password</span><input class="input" id="r-pass" type="password" minlength="8"></label>
            <label class="field"><span class="label">Confirm password</span><input class="input" id="r-conf" type="password" minlength="8"></label>
            <p class="err hidden" id="r-err"></p>
            <button class="btn btn-primary btn-full" style="height:2.5rem">Update password</button>
        </form>
        <p class="notice" id="r-msg">Checking your recovery link…</p>
    </section>
</main>
<div id="toasts" aria-live="polite"></div>
<script src="assets/app.js"></script>
</body>
</html>
