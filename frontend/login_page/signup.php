<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/auth/page.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
    <title>Create account | e-Barangay Bucana</title>
    <meta name="description" content="Register as a resident of e-Barangay Bucana.">
    <meta property="og:title" content="Create account | e-Barangay Bucana">
    <meta property="og:description" content="Register as a resident of e-Barangay Bucana.">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;1,9..144,400;1,9..144,600&family=Manrope:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= url('assets/auth/styles.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/auth/auth.css') ?>">
</head>
<body>
<main class="auth-shell">
    <aside class="auth-brand" aria-label="About the e-Barangay portal">
        <img class="brand-photo" src="<?= url('assets/auth/barangay-hall.jpg') ?>" alt="" width="1600" height="1000">
        <div class="brand-scrim" aria-hidden="true"></div>
        <div class="brand-content">
            <a class="brand" href="<?= url('') ?>">
                <span class="brand-mark">
                    <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </span>
                <span class="brand-text">
                    <strong>e-Barangay</strong>
                    <small>Bucana · Nasugbu</small>
                </span>
            </a>

            <div class="brand-copy">
                <p class="eyebrow"><span class="rule" aria-hidden="true"></span>Serbisyong malapit sa iyo</p>
                <h2>Join your barangay<br><em>online.</em></h2>
                <p class="brand-lead">Create a verified account to request services and stay informed.</p>
                <ul class="trust">
                    <li>
                        <span class="trust-mark" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        </span>
                        Secure, encrypted resident data
                    </li>
                    <li>
                        <span class="trust-mark" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        </span>
                        Service updates sent to your phone
                    </li>
                    <li>
                        <span class="trust-mark" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        </span>
                        Real-time barangay announcements
                    </li>
                </ul>
            </div>

            <div class="brand-foot">
                <span>Office of the Punong Barangay</span>
                <span>Bayan ng Nasugbu, Batangas</span>
            </div>
        </div>
    </aside>

    <section class="auth-side">
        <div class="auth-top">
            <a class="back-link" href="<?= url('') ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" width="16" height="16">
                    <path d="m12 19-7-7 7-7"/>
                    <path d="M19 12H5"/>
                </svg>
                Back to home
            </a>
            <a class="hotline" href="tel:+63281234567" aria-label="Barangay hotline (02) 8123 4567">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" width="16" height="16">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                <span>Need help? <strong>(02) 8123 4567</strong></span>
            </a>
        </div>

        <div class="auth-center">
            <div class="auth-card" id="auth" data-initial="signup">
                <header class="auth-head">
                    <a class="mobile-brand" href="<?= url('') ?>">
                        <span class="brand-mark">
                            <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                                <path d="m9 12 2 2 4-4"/>
                            </svg>
                        </span>
                        <span>
                            <strong>e-Barangay</strong>
                            <small>Bucana · Nasugbu</small>
                        </span>
                    </a>
                    <p class="eyebrow"><span class="rule" aria-hidden="true"></span>Resident registration</p>
                    <h1 id="a-title"></h1>
                    <p id="a-sub"></p>
                </header>

                <div id="a-success" class="auth-success hidden">
                    <div class="success-mark" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    </div>
                    <div class="okbox" id="a-ok"></div>
                    <button class="btn btn-primary btn-full" type="button" data-mode="signin" style="height:50px">Return to sign in</button>
                </div>

                <form id="a-form" novalidate>
                    <div class="grid2" data-modes="signup">
                        <label class="field"><span class="label">Full name</span><input class="input" name="fullName" placeholder="Juan Dela Cruz" autocomplete="name"></label>
                        <label class="field"><span class="label">Phone number</span><input class="input" name="phone" placeholder="09XX XXX XXXX" autocomplete="tel"></label>
                    </div>

                    <div class="field">
                        <label class="label" for="email">Email address</label>
                        <div class="row">
                            <div class="input-shell">
                                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                                    <path d="m22 7-10 5L2 7"/>
                                </svg>
                                <input class="input" id="email" name="email" type="email" inputmode="email" placeholder="you@example.com" autocomplete="email">
                            </div>
                            <button type="button" class="verify-btn" id="email-verify-btn" data-modes="signup" disabled>Verify</button>
                        </div>
                    </div>

                    <div class="stack" data-modes="signup">
                        <label class="field"><span class="label">Complete address</span><input class="input" name="address" placeholder="House no., street, purok" autocomplete="street-address"></label>
                        <label class="field"><span class="label">Household number (optional)</span><input class="input" name="householdNumber" placeholder="e.g. HH-0241"></label>
                    </div>

                    <div class="field" data-modes="signin signup">
                        <div class="label-row">
                            <label for="password">Password</label>
                            <button type="button" class="link-button" data-mode="forgot" data-modes="signin">Forgot password?</button>
                        </div>
                        <div class="pw">
                            <div class="input-shell has-action">
                                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                <input class="input" id="password" name="password" type="password" maxlength="72" placeholder="At least 8 characters" autocomplete="new-password">
                                <button type="button" class="btn btn-ghost btn-icon" id="pw-toggle" aria-label="Show password">
                                    <span class="eye">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M2.06 12.35a1 1 0 0 1 0-.7 10.75 10.75 0 0 1 19.88 0 1 1 0 0 1 0 .7 10.75 10.75 0 0 1-19.88 0"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </span>
                                    <span class="eye-off hidden">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M10.73 5.08a10.74 10.74 0 0 1 11.21 6.57 1 1 0 0 1 0 .7 10.75 10.75 0 0 1-1.44 2.49"/>
                                            <path d="M14.08 14.16a3 3 0 0 1-4.24-4.24"/>
                                            <path d="M17.48 17.5a10.75 10.75 0 0 1-15.42-5.15 1 1 0 0 1 0-.7 10.75 10.75 0 0 1 4.45-5.14"/>
                                            <path d="m2 2 20 20"/>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <p class="err hidden" role="alert" id="a-err"></p>

                    <button type="submit" class="btn btn-primary btn-full btn-lg-form" id="a-submit"></button>

                    <p class="center"><span id="a-foot"></span> <a id="a-link" href="#"></a><button type="button" class="btn btn-link hidden" id="a-back" data-mode="signin">Sign in</button></p>
                </form>

                <p class="secure-note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Secure, encrypted resident data
                </p>
            </div>

            <div class="modal-bg" id="email-modal">
                <div class="modal" role="dialog" aria-modal="true" aria-labelledby="em-title">
                    <button class="btn btn-ghost btn-icon modal-x" type="button" id="em-close" aria-label="Close">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" width="18" height="18">
                            <path d="M18 6 6 18M6 6l12 12"/>
                        </svg>
                    </button>
                    <div class="head">
                        <div class="ico">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="2" y="4" width="20" height="16" rx="2"/>
                                <path d="m22 7-10 5L2 7"/>
                            </svg>
                        </div>
                        <h2 id="em-title">Verify your email</h2>
                        <p>Enter the 6-digit code sent to <b id="em-email"></b>.</p>
                    </div>
                    <div class="otp" id="email-otp">
                        <input inputmode="numeric" maxlength="1" aria-label="Digit 1">
                        <input inputmode="numeric" maxlength="1" aria-label="Digit 2">
                        <input inputmode="numeric" maxlength="1" aria-label="Digit 3">
                        <input inputmode="numeric" maxlength="1" aria-label="Digit 4">
                        <input inputmode="numeric" maxlength="1" aria-label="Digit 5">
                        <input inputmode="numeric" maxlength="1" aria-label="Digit 6">
                    </div>
                    <p class="err hidden center" id="em-err" role="alert"></p>
                    <button class="btn btn-primary btn-full" id="em-verify" style="height:50px" disabled>Verify email</button>
                    <p class="center">Didn't get it? <button class="btn btn-link" id="em-resend" type="button">Resend code</button></p>
                </div>
            </div>
        </div>

        <footer class="auth-foot">
            <span>&copy; 2026 Barangay Bucana</span>
            <span class="dot" aria-hidden="true"></span>
            <a href="#">Privacy</a>
            <span class="dot" aria-hidden="true"></span>
            <a href="#">Accessibility</a>
        </footer>
    </section>
</main>
<div id="toasts" aria-live="polite"></div>
    <script>window.EB_BASE = <?= json_encode(url('')) ?>;</script>
    <script src="<?= url('assets/auth/app.js') ?>"></script>
</body>
</html>
