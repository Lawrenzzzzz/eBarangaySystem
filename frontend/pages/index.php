<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Barangay San Isidro | Serbisyong Malapit sa Iyo</title>
    <meta name="description" content="Access resident services, barangay announcements, emergency contacts, and your secure Barangay San Isidro account.">
    <meta property="og:title" content="Barangay San Isidro | Serbisyong Malapit sa Iyo">
    <meta property="og:description" content="A connected, caring, and responsive barangay for every resident.">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600&family=Manrope:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body id="top">
    <header class="site-header">
        <div class="container header-in">
            <a href="#top" class="logo">
                <span class="logo-mark">
                    <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </span>
                <span>
                    <strong>Barangay San Isidro</strong>
                    <small>Lungsod ng Maynila</small>
                </span>
            </a>
            <nav class="nav-main" aria-label="Main navigation">
                <a href="#services">Services</a>
                <a href="#announcements">Announcements</a>
                <a href="#about">Our barangay</a>
                <a href="#contact">Contact</a>
            </nav>
            <div class="header-actions">
                <span data-auth="out" class="inl">
                    <a class="btn btn-ghost" href="login.php">
                        <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <path d="m10 17 5-5-5-5M15 12H3"/>
                        </svg>
                        Sign in
                    </a>
                    <a class="btn btn-primary btn-pill" href="signup.php">
                        <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M19 8v6M22 11h-6"/>
                        </svg>
                        Register
                    </a>
                </span>
                <a data-auth="in" class="btn btn-primary hidden" href="../account.php">
                    My account
                    <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 12h14"/>
                        <path d="m12 5 7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <button class="btn btn-outline btn-icon menu-btn" id="open-drawer" aria-label="Open navigation">
                <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 12h16M4 6h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </header>

    <div class="drawer-bg" id="drawer-bg"></div>
    <aside class="drawer" aria-label="Navigation">
        <button class="btn btn-ghost btn-icon drawer-close" id="close-drawer" aria-label="Close">
            <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M18 6 6 18M6 6l12 12"/>
            </svg>
        </button>
        <h2>Barangay San Isidro</h2>
        <nav>
            <a href="#services">Services</a>
            <a href="#announcements">Announcements</a>
            <a href="#about">Our barangay</a>
            <a href="#contact">Contact</a>
        </nav>
        <div class="actions">
            <span data-auth="out" style="display:grid;gap:.5rem">
                <a class="btn btn-outline" href="../login.php">Sign in</a>
                <a class="btn btn-primary" href="../signup.php">Register</a>
            </span>
            <a data-auth="in" class="btn btn-primary hidden" href="../account.php">My account</a>
        </div>
    </aside>

    <main>
        <section class="hero">
            <img src="../assets/barangay-hall.jpg" alt="Barangay hall and community plaza" width="1600" height="1000">
            <div class="hero-overlay"></div>
            <div class="container hero-in">
                <div class="hero-copy">
                    <p class="hero-kicker"><span></span>Malasakit. Aksyon. Pag-unlad.</p>
                    <h1>Serbisyong barangay, mas malapit sa iyo.</h1>
                    <p>Access essential services, receive community updates, and connect with your barangay�wherever you are.</p>
                    <div class="hero-btns">
                        <a class="btn btn-lg btn-gold" href="signup.php">
                            Create resident account
                            <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M5 12h14"/>
                                <path d="m12 5 7 7-7 7"/>
                            </svg>
                        </a>
                        <a class="btn btn-lg btn-hero-outline" href="login.php">Sign in to portal</a>
                    </div>
                </div>
            </div>
            <div class="hero-stats">
                <div class="container">
                    <div class="stat"><strong>18,420</strong><span>Residents</span></div>
                    <div class="stat"><strong>4,380</strong><span>Households</span></div>
                    <div class="stat"><strong>24/7</strong><span>Response line</span></div>
                </div>
            </div>
        </section>

        <section id="services" class="section">
            <div class="container">
                <div class="sec-head">
                    <div>
                        <p class="kicker">Resident services</p>
                        <h2 class="title">What can we help you with?</h2>
                    </div>
                    <p class="muted">Start common barangay requests online, then track updates from your secure account.</p>
                </div>
                <div class="cards">
                    <article class="card-svc">
                        <span class="svc-ico">
                            <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 22h14a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v4"/>
                                <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                                <path d="m3 15 2 2 4-4"/>
                            </svg>
                        </span>
                        <h3>Request documents</h3>
                        <p>Barangay clearance, residency, indigency, and other certificates.</p>
                        <a class="btn btn-link" data-service href="../login.php">Open service
                            <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </a>
                    </article>
                    <article class="card-svc">
                        <span class="svc-ico">
                            <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                                <path d="M12 5 9.04 7.96a2.17 2.17 0 0 0 0 3.08c.82.82 2.13.85 3 .07l2.07-1.9a2.82 2.82 0 0 1 3.79 0l2.96 2.66"/>
                            </svg>
                        </span>
                        <h3>Community assistance</h3>
                        <p>Ask about health, senior, livelihood, and social support programs.</p>
                        <a class="btn btn-link" data-service href="../login.php">Open service
                            <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </a>
                    </article>
                    <article class="card-svc">
                        <span class="svc-ico">
                            <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M10.27 21a2 2 0 0 0 3.46 0"/>
                                <path d="M3.26 15.33A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.67C19.41 13.96 18 12.5 18 8A6 6 0 0 0 6 8c0 4.5-1.41 5.96-2.74 7.33"/>
                            </svg>
                        </span>
                        <h3>Report a concern</h3>
                        <p>Send a local concern to the barangay office for proper action.</p>
                        <a class="btn btn-link" data-service href="../login.php">Open service
                            <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </a>
                    </article>
                </div>
            </div>
        </section>

        <section id="announcements" class="section soft">
            <div class="container ann-grid">
                <div>
                    <p class="kicker">Community board</p>
                    <h2 class="title">Latest from the barangay</h2>
                    <p class="muted">Stay informed about programs, schedules, and activities happening in our community.</p>
                </div>
                <div class="ann-list">
                    <article class="ann">
                        <time>15 SEP</time>
                        <div>
                            <h3>Free medical and dental mission</h3>
                            <p>Barangay covered court � 8:00 AM to 3:00 PM</p>
                        </div>
                    </article>
                    <article class="ann">
                        <time>18 SEP</time>
                        <div>
                            <h3>Community clean-up drive</h3>
                            <p>Assembly at the barangay hall � 6:30 AM</p>
                        </div>
                    </article>
                    <article class="ann">
                        <time>22 SEP</time>
                        <div>
                            <h3>Senior citizen assistance registration</h3>
                            <p>Barangay session hall � 9:00 AM to 4:00 PM</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section id="about" class="section">
            <div class="container about">
                <div>
                    <p class="kicker">Our commitment</p>
                    <h2 class="title">A barangay that listens and acts.</h2>
                    <p class="muted">We work alongside residents to build a safer, healthier, and more inclusive community. Transparent service and responsive local leadership guide every program we deliver.</p>
                    <div class="nums">
                        <div><strong>12</strong><p>Active community programs</p></div>
                        <div><strong>7</strong><p>Puroks served</p></div>
                    </div>
                </div>
                <div class="quote">
                    <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M18 21a8 8 0 0 0-16 0"/>
                        <circle cx="10" cy="8" r="5"/>
                        <path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3"/>
                    </svg>
                    <blockquote>"Maunlad na pamayanan, nagsisimula sa nagkakaisang mamamayan."</blockquote>
                    <p>Office of the Punong Barangay</p>
                </div>
            </div>
        </section>

        <section id="contact" class="contact">
            <div class="container">
                <div class="ct">
                    <span>
                        <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M13 2a9 9 0 0 1 9 9M13 6a5 5 0 0 1 5 5"/>
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </span>
                    <div><small>Barangay office</small><b>(02) 8123 4567</b></div>
                </div>
                <div class="ct">
                    <span>
                        <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M7 18v-6a5 5 0 1 1 10 0v6"/>
                            <path d="M5 21a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2z"/>
                            <path d="M21 12h1M2 12h1M12 2v1M4.93 4.93l.7.7M19.07 4.93l-.7.7"/>
                        </svg>
                    </span>
                    <div><small>Emergency response</small><b>0917 123 4567</b></div>
                </div>
                <div class="ct">
                    <span>
                        <svg class="i" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M8 2v4M16 2v4"/>
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <path d="M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/>
                        </svg>
                    </span>
                    <div><small>Office hours</small><b>Mon�Fri � 8:00 AM�5:00 PM</b></div>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>� 2026 Barangay San Isidro. Serbisyo para sa lahat.</p>
            <p>Privacy � Accessibility � Citizen�s Charter</p>
        </div>
    </footer>

    <div id="toasts" aria-live="polite"></div>
    <script src="../assets/app.js"></script>
</body>
</html>
