<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Makati Home Ville | Homeowners Portal</title>
    <meta name="description" content="The community portal of Makati Home Ville in Dayap, Calauan, Laguna. Read HOA announcements, report problems with photos or video, and reserve the Community House.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>

    <header class="site-header">
        <div class="container nav">
            <a class="brand" href="{{ url('/') }}">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/></svg>
                </span>
                Makati Home Ville
            </a>

            <ul class="nav-links" id="nav-links">
                <li><a href="#features">What you can do</a></li>
                <li><a href="#how">How it works</a></li>
                <li><a href="#roles">Who uses it</a></li>
                <li><a href="#alerts">Emergency alerts</a></li>
            </ul>

            <div class="nav-actions">
                <a class="nav-login" href="{{ route('login') }}">Log in</a>
                <a class="btn btn-dark" href="{{ route('register') }}">Register</a>
            </div>

            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav-links" aria-label="Menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>
    </header>

    <main id="main">
        <section class="hero">
            <div class="container hero-grid">
                <div class="hero-copy">
                    <p class="location"><span class="location-dot" aria-hidden="true"></span>Dayap, Calauan, Laguna</p>
                    <h1>One portal for everything at Makati Home Ville.</h1>
                    <p class="lead">Read announcements from the HOA, report problems with a photo or video, and book the Community House. Made for residents, the HOA Admin, and facility staff.</p>
                    <div class="cta-row">
                        <a class="btn btn-accent btn-lg" href="{{ route('register') }}">Create account</a>
                        <a class="text-link" href="{{ route('login') }}">Log in</a>
                    </div>
                    <p class="hero-note">Residents sign up with their name, email, block, lot, and mobile number.</p>
                </div>

                <div class="plan" aria-hidden="true">
                    <svg class="plan-svg" viewBox="0 0 560 540" focusable="false">
                        <defs>
                            <pattern id="lots" width="20" height="28" patternUnits="userSpaceOnUse">
                                <rect x="2" y="2" width="16" height="24" rx="3" fill="#F2F1E6"/>
                            </pattern>
                        </defs>

                        <g class="blk" style="--i:0"><rect x="20" y="28" width="220" height="84" rx="8" fill="rgba(255,255,255,.07)"/><rect x="20" y="28" width="220" height="84" fill="url(#lots)"/></g>
                        <g class="blk" style="--i:1"><rect x="320" y="28" width="220" height="84" rx="8" fill="rgba(255,255,255,.07)"/><rect x="320" y="28" width="220" height="84" fill="url(#lots)"/></g>
                        <g class="blk" style="--i:2"><rect x="20" y="140" width="220" height="84" rx="8" fill="rgba(255,255,255,.07)"/><rect x="20" y="140" width="220" height="84" fill="url(#lots)"/><rect x="102" y="170" width="16" height="24" rx="3" fill="#FF7B3F"/></g>
                        <g class="blk" style="--i:3"><rect x="320" y="140" width="220" height="84" rx="8" fill="rgba(255,255,255,.07)"/><rect x="320" y="140" width="220" height="84" fill="url(#lots)"/></g>

                        <g class="blk" style="--i:4">
                            <rect x="20" y="240" width="520" height="80" rx="12" fill="rgba(255,255,255,.06)"/>
                            <g fill="#3F8C72">
                                <circle cx="52" cy="280" r="9"/><circle cx="92" cy="280" r="9"/><circle cx="132" cy="280" r="9"/><circle cx="172" cy="280" r="9"/>
                                <circle cx="388" cy="280" r="9"/><circle cx="428" cy="280" r="9"/><circle cx="468" cy="280" r="9"/><circle cx="508" cy="280" r="9"/>
                            </g>
                            <polygon points="236,268 280,244 324,268" fill="#FF7B3F"/>
                            <rect x="244" y="268" width="72" height="40" fill="#FF7B3F"/>
                            <rect x="272" y="286" width="16" height="22" rx="2" fill="#0E2F28"/>
                        </g>

                        <g class="blk" style="--i:5"><rect x="20" y="336" width="220" height="84" rx="8" fill="rgba(255,255,255,.07)"/><rect x="20" y="336" width="220" height="84" fill="url(#lots)"/></g>
                        <g class="blk" style="--i:6"><rect x="320" y="336" width="220" height="84" rx="8" fill="rgba(255,255,255,.07)"/><rect x="320" y="336" width="220" height="84" fill="url(#lots)"/></g>
                        <g class="blk" style="--i:7"><rect x="20" y="448" width="220" height="84" rx="8" fill="rgba(255,255,255,.07)"/><rect x="20" y="448" width="220" height="84" fill="url(#lots)"/></g>
                        <g class="blk" style="--i:8"><rect x="320" y="448" width="220" height="84" rx="8" fill="rgba(255,255,255,.07)"/><rect x="320" y="448" width="220" height="84" fill="url(#lots)"/></g>

                        <circle cx="280" cy="176" r="15" fill="rgba(255,123,63,.28)"/>
                        <circle cx="280" cy="176" r="6" fill="#FF7B3F"/>
                    </svg>

                    <div class="chip chip-lot"><strong>Your lot</strong></div>
                    <div class="chip chip-report"><strong>Street light</strong><span class="pill pill-progress">In progress</span></div>
                    <div class="chip chip-house"><strong>Community House</strong><span class="pill pill-ok">Approved</span></div>
                </div>
            </div>
        </section>

        <section class="section" id="features">
            <div class="container split">
                <div class="split-head">
                    <h2>What you can do here</h2>
                    <p>Everyday HOA matters, handled in one place instead of group chats and paper notices.</p>
                </div>

                <ul class="rows">
                    <li class="row">
                        <span class="row-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11v2a1 1 0 0 0 1 1h2l5 4V6L6 10H4a1 1 0 0 0-1 1z"/><path d="M15.5 8.5a5 5 0 0 1 0 7M18 6a8.5 8.5 0 0 1 0 12"/></svg></span>
                        <div>
                            <h3>Read announcements</h3>
                            <p>The HOA posts notices with photos or videos, so you can see what is happening and why.</p>
                        </div>
                    </li>
                    <li class="row">
                        <span class="row-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="1.5"/><path d="M21 16l-5-5-9 9"/></svg></span>
                        <div>
                            <h3>Report a problem with proof</h3>
                            <p>Attach a photo or video so the HOA can see exactly where the problem is. Follow it from Pending to Resolved. Only you and the HOA Admin can see your complaint.</p>
                        </div>
                    </li>
                    <li class="row">
                        <span class="row-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/></svg></span>
                        <div>
                            <h3>Reserve the Community House</h3>
                            <p>Request a date and time. Facility staff approve or decline, and the system blocks a request that overlaps an approved booking.</p>
                        </div>
                    </li>
                    <li class="row">
                        <span class="row-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="M9 12l2 2 4-4"/></svg></span>
                        <div>
                            <h3>See only what is yours</h3>
                            <p>Residents, the HOA Admin, and facility staff each get the tools for their role. Private reports stay private.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </section>

        <section class="section section-sage" id="how">
            <div class="container">
                <div class="section-head">
                    <h2>Up and running in three steps</h2>
                </div>
                <ol class="steps">
                    <li class="step">
                        <span class="step-n" aria-hidden="true">1</span>
                        <h3>Create your account</h3>
                        <p>Register with your name, email, block, lot, and mobile number. New accounts are always residents.</p>
                    </li>
                    <li class="step">
                        <span class="step-n" aria-hidden="true">2</span>
                        <h3>Report or request</h3>
                        <p>File a complaint with photos or video, or ask to reserve a facility for the date you need.</p>
                    </li>
                    <li class="step">
                        <span class="step-n" aria-hidden="true">3</span>
                        <h3>Follow the result</h3>
                        <p>Check the status of your complaint or reservation, and read what the HOA posts.</p>
                    </li>
                </ol>
            </div>
        </section>

        <section class="section" id="roles">
            <div class="container">
                <div class="section-head">
                    <h2>Built for the whole community</h2>
                </div>
                <div class="roles">
                    <div class="role">
                        <h3>Residents</h3>
                        <ul>
                            <li>Read announcements</li>
                            <li>File complaints with photos or video</li>
                            <li>Request facility reservations</li>
                            <li>Track their own requests</li>
                        </ul>
                    </div>
                    <div class="role">
                        <h3>HOA Admin</h3>
                        <ul>
                            <li>Post, edit, and delete announcements</li>
                            <li>See every complaint</li>
                            <li>Update complaint status</li>
                            <li>View all reservations</li>
                        </ul>
                    </div>
                    <div class="role">
                        <h3>Facility Staff</h3>
                        <ul>
                            <li>Review reservation requests</li>
                            <li>Approve or decline them</li>
                            <li>Avoid double bookings</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="section section-dark" id="alerts">
            <div class="container split split-center">
                <div class="split-head">
                    <h2>Emergency alerts are coming</h2>
                    <p>The HOA Admin will be able to send an urgent alert that shows at the top of every screen for everyone who is logged in. SMS alerts will follow.</p>
                </div>
                <div class="alert-preview">
                    <span class="preview-tag">Preview</span>
                    <div class="alert-banner" role="img" aria-label="Preview of an emergency alert banner">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 8 3 8H3s3-1 3-8"/><path d="M10 21a2 2 0 0 0 4 0"/></svg>
                        <div>
                            <strong>Typhoon signal raised</strong>
                            <p>Stay indoors and check this page for updates from the HOA.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section section-cta">
            <div class="container">
                <div class="cta-card">
                    <div>
                        <h2>Join your community portal</h2>
                        <p>Create your resident account in a minute, or log in if you already have one.</p>
                    </div>
                    <div class="cta-row">
                        <a class="btn btn-accent btn-lg" href="{{ route('register') }}">Create account</a>
                        <a class="btn btn-outline btn-lg" href="{{ route('login') }}">Log in</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <p><strong>Makati Home Ville Homeowners Association</strong><br>Dayap, Calauan, Laguna</p>
            <p>Capstone project by Anthony Baldoza, Dexilou Barotia, and Aaron Nidea.</p>
        </div>
    </footer>

    <script>
        (function () {
            var toggle = document.querySelector('.nav-toggle');
            var links = document.getElementById('nav-links');
            toggle.addEventListener('click', function () {
                var open = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', String(!open));
                links.classList.toggle('is-open', !open);
            });
            links.addEventListener('click', function (e) {
                if (e.target.tagName === 'A') {
                    toggle.setAttribute('aria-expanded', 'false');
                    links.classList.remove('is-open');
                }
            });
        })();
    </script>
</body>
</html>