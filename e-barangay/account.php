<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Account | Barangay San Isidro</title>
    <meta name="description" content="Manage your Barangay San Isidro resident account and official access request.">
    <meta property="og:title" content="My Account | Barangay San Isidro">
    <meta property="og:description" content="Manage your Barangay San Isidro account.">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600&family=Manrope:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<main class="acct" id="acct" hidden>
    <header class="acct-top">
        <div class="in">
            <a class="btn btn-ghost" href="index.php">
                <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="m12 19-7-7 7-7"/>
                    <path d="M19 12H5"/>
                </svg>
                Home
            </a>
            <button class="btn btn-outline" id="signout">
                <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <path d="m16 17 5-5-5-5M21 12H9"/>
                </svg>
                Sign out
            </button>
        </div>
    </header>
    <div class="acct-body">
        <div class="intro">
            <p class="kick">My barangay account</p>
            <h1>Magandang araw, <span id="u-first"></span>.</h1>
            <p>Your secure home for resident information and barangay service access.</p>
        </div>
        <div class="acct-grid">
            <section class="panel">
                <div class="hd">
                    <span>
                        <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="8" r="5"/>
                            <path d="M20 21a8 8 0 0 0-16 0"/>
                        </svg>
                    </span>
                    <div><h2>Resident profile</h2><p>Verified account information</p></div>
                </div>
                <dl class="info">
                    <div><dt>Email</dt><dd id="u-email"></dd></div>
                    <div><dt>Phone</dt><dd id="u-phone"></dd></div>
                    <div>
                        <dt>Address</dt>
                        <dd>
                            <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 10c0 5-5.54 10.19-7.4 11.8a1 1 0 0 1-1.2 0C9.54 20.19 4 15 4 10a8 8 0 0 1 16 0"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span id="u-address"></span>
                        </dd>
                    </div>
                    <div><dt>Account type</dt><dd id="u-type"></dd></div>
                </dl>
            </section>
            <aside class="status">
                <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
                <p class="s1">Access status</p>
                <h2 id="s-title"></h2>
                <p class="s2" id="s-text"></p>
                <div class="s3"><span id="s-ico"></span> Securely verified</div>
            </aside>
        </div>
    </div>
</main>
<template id="ico-clock">
    <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="12" cy="12" r="10"/>
        <path d="M12 6v6h4"/>
    </svg>
</template>
<template id="ico-ok">
    <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="12" cy="12" r="10"/>
        <path d="m9 12 2 2 4-4"/>
    </svg>
</template>
<div id="toasts" aria-live="polite"></div>
<script src="assets/app.js"></script>
</body>
</html>
