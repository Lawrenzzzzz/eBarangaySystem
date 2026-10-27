<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | e-Barangay San Isidro</title>
    <meta name="description" content="Sign in to your e-Barangay San Isidro resident or official account.">
    <meta property="og:title" content="Sign in | e-Barangay San Isidro">
    <meta property="og:description" content="Sign in to your e-Barangay San Isidro resident or official account.">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600&family=Manrope:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<main class="auth-page">
    <aside class="auth-aside">
        <img src="../assets/barangay-hall.jpg" alt="Barangay hall">
        <div class="hero-overlay"></div>
        <div class="auth-aside-in">
            <a href="../index.php" class="logo-link">
                <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
                e-Barangay San Isidro
            </a>
            <div style="max-width:28rem">
                <h2>Welcome back, ka-barangay.</h2>
                <p class="lead">Access documents, announcements, and services from anywhere.</p>
                <ul>
                    <li>
                        <span><svg class="i" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg></span>
                        Secure, encrypted resident data
                    </li>
                    <li>
                        <span><svg class="i" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg></span>
                        Verified accounts via phone number
                    </li>
                    <li>
                        <span><svg class="i" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg></span>
                        Real-time barangay announcements
                    </li>
                </ul>
            </div>
        </div>
    </aside>
    <section class="auth-main">
        <a href="index.php" class="back">
            <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                <path d="m12 19-7-7 7-7"/>
                <path d="M19 12H5"/>
            </svg>
            Back to home
        </a>
        <div class="auth-wrap">
            <div class="auth-card" id="auth" data-initial="signin">
                <div class="auth-head">
                    <div class="ico">
                        <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>
                    </div>
                    <h1 id="a-title"></h1>
                    <p id="a-sub"></p>
                </div>
                <div id="a-success" class="auth-body hidden">
                    <div class="okbox" id="a-ok"></div>
                    <button class="btn btn-primary btn-full" type="button" data-mode="signin" style="height:2.5rem">Return to sign in</button>
                </div>
                <form id="a-form" class="auth-body" novalidate>
                    <div class="seg" data-modes="signup">
                        <button type="button" class="btn on" data-type="resident">
                            <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            Resident
                        </button>
                        <button type="button" class="btn" data-type="official">
                            <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                                <path d="m9 12 2 2 4-4"/>
                            </svg>
                            Official
                        </button>
                    </div>
                    <div class="grid2" data-modes="signup">
                        <label class="field"><span class="label">Full name</span><input class="input" name="fullName" placeholder="Juan Dela Cruz" autocomplete="name"></label>
                        <div class="field">
                            <label class="label" for="phone">Phone number</label>
                            <div class="row">
                                <input class="input" id="phone" name="phone" placeholder="09XX XXX XXXX" autocomplete="tel">
                                <button type="button" class="btn btn-outline" id="verify-btn" style="height:2.75rem" disabled>Verify</button>
                            </div>
                        </div>
                    </div>
                    <label class="field"><span class="label">Email address</span><input class="input" type="email" name="email" placeholder="you@example.com" autocomplete="email"></label>
                    <div class="stack" data-modes="signup">
                        <label class="field"><span class="label">Complete address</span><input class="input" name="address" placeholder="House no., street, purok" autocomplete="street-address"></label>
                        <label class="field"><span class="label">Household number (optional)</span><input class="input" name="householdNumber" placeholder="e.g. HH-0241"></label>
                        <div class="grid2 hidden" id="official-fields">
                            <label class="field"><span class="label">Barangay position</span><input class="input" name="position" placeholder="e.g. Kagawad"></label>
                            <label class="field"><span class="label">Employee number</span><input class="input" name="employeeNumber" placeholder="Official ID number"></label>
                        </div>
                    </div>
                    <div class="field" data-modes="signin signup">
                        <label class="label" for="password">Password</label>
                        <div class="pw">
                            <input class="input" id="password" name="password" type="password" maxlength="72" placeholder="At least 8 characters">
                            <button type="button" class="btn btn-ghost btn-icon" id="pw-toggle" aria-label="Show password">
                                <span class="eye">
                                    <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M2.06 12.35a1 1 0 0 1 0-.7 10.75 10.75 0 0 1 19.88 0 1 1 0 0 1 0 .7 10.75 10.75 0 0 1-19.88 0"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </span>
                                <span class="eye-off hidden">
                                    <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M10.73 5.08a10.74 10.74 0 0 1 11.21 6.57 1 1 0 0 1 0 .7 10.75 10.75 0 0 1-1.44 2.49"/>
                                        <path d="M14.08 14.16a3 3 0 0 1-4.24-4.24"/>
                                        <path d="M17.48 17.5a10.75 10.75 0 0 1-15.42-5.15 1 1 0 0 1 0-.7 10.75 10.75 0 0 1 4.45-5.14"/>
                                        <path d="m2 2 20 20"/>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                    <p class="err hidden" role="alert" id="a-err"></p>
                    <div class="right" data-modes="signin">
                        <button type="button" class="btn btn-link small" data-mode="forgot">Forgot password?</button>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full btn-lg-form" id="a-submit"></button>
                    <div data-modes="signin signup" class="stack">
                        <div class="or"><span></span>or continue with<span></span></div>
                        <button type="button" class="btn btn-outline btn-full" id="google" style="height:2.75rem">
                            <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="2" y="4" width="20" height="16" rx="2"/>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                            </svg>
                            Google
                        </button>
                    </div>
                    <p class="center"><span id="a-foot"></span> <a id="a-link" href="#"></a><button type="button" class="btn btn-link hidden" id="a-back" data-mode="signin">Sign in</button></p>
                </form>
            </div>
            <div class="modal-bg" id="phone-modal">
                <div class="modal" role="dialog" aria-modal="true">
                    <button class="btn btn-ghost btn-icon modal-x" type="button" id="pm-close" aria-label="Close">
                        <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18 6 6 18M6 6l12 12"/>
                        </svg>
                    </button>
                    <div class="head">
                        <div class="ico">
                            <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="5" y="2" width="14" height="20" rx="2"/>
                                <path d="M12 18h.01"/>
                            </svg>
                        </div>
                        <h2>Verify your number</h2>
                        <p>Enter the 6-digit code sent to <b id="pm-phone"></b>.</p>
                    </div>
                    <div class="otp" id="otp">
                        <input inputmode="numeric" maxlength="1" aria-label="Digit 1">
                        <input inputmode="numeric" maxlength="1" aria-label="Digit 2">
                        <input inputmode="numeric" maxlength="1" aria-label="Digit 3">
                        <input inputmode="numeric" maxlength="1" aria-label="Digit 4">
                        <input inputmode="numeric" maxlength="1" aria-label="Digit 5">
                        <input inputmode="numeric" maxlength="1" aria-label="Digit 6">
                    </div>
                    <p class="err hidden center" id="pm-err" role="alert" style="color:var(--destructive)"></p>
                    <button class="btn btn-primary btn-full" id="pm-verify" style="height:2.75rem" disabled>Verify number</button>
                    <p class="center">Didn't get it? <button class="btn btn-link" id="pm-resend" type="button">Resend code</button></p>
                </div>
            </div>
        </div>
    </section>
</main>
<div id="toasts" aria-live="polite"></div>
<script src="../assets/app.js"></script>
</body>
</html>
