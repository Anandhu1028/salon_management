<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description"
        content="SalonPro Management System — the all-in-one platform to manage appointments, staff, customers and grow your salon business.">

    <title>{{ config('app.name', 'SalonPro') }} — Sign In</title>



    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">



    @vite(['resources/css/app.css', 'resources/js/app.js'])



    <style>
        :root {

            --sp-font: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, sans-serif;

            --sp-purple: #7C3AED;

            --sp-violet: #8B5CF6;

            --sp-pink: #D946EF;

            --sp-navy: #09061A;

            --sp-dark: #11142D;

            --sp-white: #FFFFFF;

            --sp-slate: #73758A;

            --sp-mist: #9CA3AF;

            --sp-hairline: #E6E3EF;

            --sp-danger: #DC2626;

            --sp-success-bg: #ECFDF5;

            --sp-success-tx: #065F46;

            --sp-grad: linear-gradient(135deg, #6D28D9 0%, #7C3AED 45%, #D946EF 100%);

            --sp-icon-grad: linear-gradient(135deg, #7C3AED 0%, #9333EA 100%);

        }



        *,
        *::before,
        *::after {

            box-sizing: border-box;

            margin: 0;

            padding: 0;

        }

        html,
        body {

            height: 100%;
            min-height: 100vh;
            overflow-x: hidden;
            font-family: var(--sp-font);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .sp-page {
            position: relative;
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .sp-bg {
            position: fixed;
            inset: 0;
            z-index: 1;
            background-image: url("{{ asset('images/auth/ChatGPT Image Aug 14, 2026, 10_21_27 AM.png') }}");
            background-size: cover;
            background-position: center right;
            background-repeat: no-repeat;
        }

        .sp-bg-overlay {
            position: fixed;
            inset: 0;
            z-index: 2;
            background:
                linear-gradient(105deg,
                    rgba(9, 6, 26, 0.88) 0%,
                    rgba(9, 6, 26, 0.72) 32%,
                    rgba(9, 6, 26, 0.35) 52%,
                    rgba(9, 6, 26, 0.12) 68%,
                    rgba(9, 6, 26, 0.05) 100%),
                radial-gradient(ellipse 70% 80% at 15% 50%, rgba(109, 40, 217, 0.28), transparent 65%),
                radial-gradient(ellipse 50% 50% at 0% 100%, rgba(109, 40, 217, 0.35), transparent 55%),
                linear-gradient(to top, rgba(9, 6, 26, 0.50) 0%, transparent 35%);
        }

        /* Neon arc decoration */
        .sp-neon-arc {
            position: fixed;
            left: -60px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 5;
            width: 180px;
            height: 520px;
            pointer-events: none;
            opacity: 0.85;
        }

        .sp-neon-arc svg {
            width: 100%;
            height: 100%;
        }

        .sp-inner {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1440px;
            margin: 0 auto;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 48px;
            padding: 40px 48px 40px 56px;
        }

        /* ── LEFT ─────────────────────────────────────────── */

        .sp-left {
            flex: 1 1 58%;
            max-width: 620px;
            padding-right: 24px;
        }

        .sp-logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            margin-bottom: 48px;
        }

        .sp-logo-mark {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: var(--sp-grad);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 28px rgba(124, 58, 237, 0.55);
            flex-shrink: 0;
        }

        .sp-logo-text {
            line-height: 1.1;
        }

        .sp-logo-name {

            display: block;

            font-size: 19px;

            font-weight: 800;

            color: #FFFFFF;

            letter-spacing: -0.02em;

        }



        .sp-logo-tag {

            display: block;

            font-size: 9px;

            font-weight: 700;

            color: rgba(255, 255, 255, 0.50);

            letter-spacing: 0.14em;

            text-transform: uppercase;

            margin-top: 3px;

        }



        .sp-hero-h1 {

            font-size: clamp(38px, 4.5vw, 54px);

            font-weight: 800;

            line-height: 1.06;

            letter-spacing: -0.03em;

            color: #FFFFFF;

            margin-bottom: 20px;

        }



        .sp-grad-text {

            background: linear-gradient(135deg, #C4B5FD 0%, #E879F9 100%);

            -webkit-background-clip: text;

            -webkit-text-fill-color: transparent;

            background-clip: text;

        }



        .sp-hero-sub {

            font-size: 15px;

            line-height: 1.75;

            color: rgba(255, 255, 255, 0.68);

            max-width: 440px;

            margin-bottom: 44px;

            font-weight: 400;

        }



        .sp-features {

            display: flex;

            flex-direction: column;

            gap: 16px;

        }



        .sp-feat {

            display: flex;

            align-items: flex-start;

            gap: 16px;

        }



        .sp-feat-icon {

            width: 50px;

            height: 50px;

            border-radius: 14px;

            display: flex;

            align-items: center;

            justify-content: center;

            flex-shrink: 0;

            border: 1px solid rgba(255, 255, 255, 0.12);

        }



        .sp-feat-icon--purple {

            background: rgba(124, 58, 237, 0.40);

            box-shadow: 0 0 24px rgba(124, 58, 237, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.15);

        }



        .sp-feat-icon--pink {

            background: rgba(192, 38, 211, 0.35);

            box-shadow: 0 0 24px rgba(192, 38, 211, 0.30), inset 0 1px 0 rgba(255, 255, 255, 0.12);

        }



        .sp-feat-icon--amber {

            background: rgba(245, 158, 11, 0.32);

            box-shadow: 0 0 24px rgba(245, 158, 11, 0.22), inset 0 1px 0 rgba(255, 255, 255, 0.12);

        }



        .sp-feat-body {
            padding-top: 4px;
        }



        .sp-feat-title {

            font-size: 14.5px;

            font-weight: 700;

            color: #FFFFFF;

            margin-bottom: 4px;

            line-height: 1.2;

        }



        .sp-feat-desc {

            font-size: 12.5px;

            font-weight: 400;

            color: rgba(255, 255, 255, 0.55);

            line-height: 1.45;

        }



        /* ── RIGHT — card column ──────────────────────────── */

        .sp-card-wrap {

            flex: 0 0 auto;

            display: flex;

            flex-direction: column;

            align-items: flex-end;

            gap: 14px;

        }



        .sp-lang {

            display: inline-flex;

            align-items: center;

            gap: 6px;

            background: rgba(255, 255, 255, 0.95);

            backdrop-filter: blur(12px);

            -webkit-backdrop-filter: blur(12px);

            border: 1px solid rgba(255, 255, 255, 0.7);

            border-radius: 999px;

            padding: 8px 16px;

            font-family: var(--sp-font);

            font-size: 13px;

            font-weight: 600;

            color: var(--sp-dark);

            cursor: pointer;

            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);

            transition: box-shadow 0.2s ease, transform 0.2s ease;

        }



        .sp-lang:hover {

            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.18);

            transform: translateY(-1px);

        }



        .sp-card {

            width: 440px;

            background: #FFFFFF;

            border-radius: 32px;

            padding: 38px 38px 0;

            box-shadow:

                0 32px 80px rgba(0, 0, 0, 0.28),

                0 0 0 1px rgba(255, 255, 255, 0.5);

            position: relative;

            overflow: visible;

            display: flex;

            flex-direction: column;

        }



        /* ── Card header ──────────────────────────────────── */

        .sp-card-head {

            text-align: center;

            margin-bottom: 30px;

        }



        .sp-card-title {

            font-size: 28px;

            font-weight: 800;

            color: var(--sp-dark);

            letter-spacing: -0.025em;

            line-height: 1.2;

            margin-bottom: 8px;

        }



        .sp-card-sub {

            font-size: 13.5px;

            color: var(--sp-slate);

            font-weight: 400;

            line-height: 1.5;

        }



        .sp-status {

            margin-bottom: 18px;

            font-size: 13px;

            font-weight: 600;

            border-radius: 12px;

            padding: 11px 15px;

            background: var(--sp-success-bg);

            color: var(--sp-success-tx);

        }



        /* ── Form fields ──────────────────────────────────── */

        .sp-field {
            margin-bottom: 16px;
        }



        .sp-label {

            display: block;

            font-size: 13px;

            font-weight: 700;

            color: var(--sp-dark);

            margin-bottom: 8px;

        }



        .sp-input-wrap {
            position: relative;
        }



        .sp-input-icon-box {

            position: absolute;

            left: 8px;

            top: 50%;

            transform: translateY(-50%);

            width: 38px;

            height: 38px;

            border-radius: 11px;

            background: var(--sp-icon-grad);

            display: flex;

            align-items: center;

            justify-content: center;

            color: #FFFFFF;

            pointer-events: none;

            z-index: 2;

            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.30);

        }



        .sp-eye-btn {

            position: absolute;

            right: 16px;

            top: 50%;

            transform: translateY(-50%);

            background: none;

            border: none;

            color: var(--sp-mist);

            cursor: pointer;

            display: flex;

            align-items: center;

            padding: 4px;

            transition: color 0.18s ease;

            z-index: 2;

        }



        .sp-eye-btn:hover {
            color: var(--sp-purple);
        }



        .hidden {
            display: none !important;
        }



        .sp-input {

            width: 100%;

            display: block;

            height: 56px;

            border: 1.5px solid var(--sp-hairline);

            background: #FFFFFF;

            border-radius: 14px;

            padding: 0 16px 0 58px;

            font-size: 14px;

            font-family: var(--sp-font);

            font-weight: 500;

            color: var(--sp-dark);

            transition: border-color 0.18s ease, box-shadow 0.18s ease;

        }



        .sp-input--pwd {
            padding-right: 48px;
        }



        .sp-input::placeholder {

            color: var(--sp-mist);

            font-weight: 400;

        }



        .sp-input:focus {

            outline: none;

            border-color: var(--sp-purple);

            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.10);

        }



        .sp-error {

            margin-top: 5px;

            font-size: 12px;

            font-weight: 600;

            color: var(--sp-danger);

        }



        .sp-row-between {

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 24px;

            margin-top: 6px;

        }



        .sp-remember {

            display: flex;

            align-items: center;

            gap: 8px;

            cursor: pointer;

            user-select: none;

        }



        .sp-checkbox {

            width: 17px;

            height: 17px;

            border-radius: 5px;

            accent-color: var(--sp-purple);

            cursor: pointer;

            flex-shrink: 0;

        }



        .sp-remember-text {

            font-size: 13px;

            font-weight: 600;

            color: var(--sp-slate);

        }



        .sp-forgot {

            font-size: 13px;

            font-weight: 700;

            color: var(--sp-purple);

            text-decoration: none;

            transition: opacity 0.15s ease;

        }



        .sp-forgot:hover {
            opacity: 0.75;
        }



        .sp-btn-primary {

            width: 100%;

            height: 54px;

            display: flex;

            align-items: center;

            justify-content: center;

            border: none;

            border-radius: 14px;

            background: var(--sp-grad);

            color: #fff;

            font-size: 15.5px;

            font-family: var(--sp-font);

            font-weight: 700;

            letter-spacing: -0.01em;

            cursor: pointer;

            box-shadow: 0 10px 32px -6px rgba(109, 40, 217, 0.55);

            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;

            margin-bottom: 28px;

            position: relative;

            z-index: 12;

        }



        .sp-btn-primary:hover {

            transform: translateY(-2px);

            box-shadow: 0 16px 40px -6px rgba(109, 40, 217, 0.65);

            filter: brightness(1.05);

        }



        .sp-btn-primary:active {

            transform: translateY(0);

            box-shadow: 0 6px 18px -4px rgba(109, 40, 217, 0.45);

        }



        /* ── Card bottom decoration ───────────────────────── */

        .sp-card-decor {

            position: relative;

            min-height: 170px;

            margin: 0 -38px;

            overflow: hidden;

            border-radius: 0 0 32px 32px;

            background: linear-gradient(180deg,

                    rgba(255, 255, 255, 0) 0%,

                    rgba(243, 240, 255, 0.50) 20%,

                    rgba(233, 226, 255, 0.75) 45%,

                    rgba(221, 214, 254, 0.90) 70%,

                    rgba(196, 181, 253, 0.50) 100%);

        }



        .sp-card-decor::before {

            content: '';

            position: absolute;

            inset: 0;

            background: linear-gradient(180deg,

                    rgba(255, 255, 255, 1) 0%,

                    rgba(255, 255, 255, 0.85) 10%,

                    rgba(255, 255, 255, 0.40) 25%,

                    rgba(255, 255, 255, 0) 50%);

            pointer-events: none;

            z-index: 1;

        }



        .sp-tools-img {

            position: absolute;

            right: 8px;

            bottom: -4px;

            width: 248px;

            object-fit: contain;

            pointer-events: none;

            z-index: 2;

            filter: drop-shadow(0 12px 28px rgba(109, 40, 217, 0.25));

            -webkit-mask-image: linear-gradient(180deg,

                    rgba(0, 0, 0, 0) 0%,

                    rgba(0, 0, 0, 0.30) 12%,

                    rgba(0, 0, 0, 1) 30%,

                    rgba(0, 0, 0, 1) 100%);

            mask-image: linear-gradient(180deg,

                    rgba(0, 0, 0, 0) 0%,

                    rgba(0, 0, 0, 0.30) 12%,

                    rgba(0, 0, 0, 1) 30%,

                    rgba(0, 0, 0, 1) 100%);

        }



        /* ── Responsive ───────────────────────────────────── */

        @media (max-width: 1100px) {

            .sp-inner {
                padding: 36px 32px;
                gap: 36px;
            }

            .sp-card {
                width: 400px;
            }

            .sp-neon-arc {
                display: none;
            }

        }



        @media (max-width: 900px) {

            .sp-inner {

                flex-direction: column;

                justify-content: center;

                gap: 32px;

                padding: 48px 24px 40px;

            }



            .sp-left {

                max-width: 100%;

                padding-right: 0;

                text-align: center;

            }



            .sp-logo {
                margin-bottom: 28px;
            }

            .sp-hero-sub {
                margin-left: auto;
                margin-right: auto;
            }

            .sp-feat {
                justify-content: center;
            }



            .sp-card-wrap {

                width: 100%;

                max-width: 440px;

                align-items: center;

            }



            .sp-card {
                width: 100%;
            }

            .sp-tools-img {
                width: 200px;
            }

        }



        @media (max-width: 560px) {

            .sp-features {
                display: none;
            }

            .sp-hero-sub {
                display: none;
            }

            .sp-hero-h1 {
                font-size: 32px;
            }

            .sp-card {
                padding: 30px 24px 0;
                border-radius: 26px;
            }

            .sp-card-decor {
                margin: 0 -24px;
                min-height: 120px;
                border-radius: 0 0 26px 26px;
            }
            .sp-tools-img {
                width: 160px;
                right: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sp-bg" aria-hidden="true"></div>
    <div class="sp-bg-overlay" aria-hidden="true"></div>
    <div class="sp-neon-arc" aria-hidden="true">
        <svg viewBox="0 0 180 520" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M160 20 C 60 80, 20 200, 40 340 C 55 440, 120 490, 160 500" stroke="url(#neonGrad)"
                stroke-width="3" fill="none" stroke-linecap="round" filter="url(#neonGlow)" />
            <defs>
                <linearGradient id="neonGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#A855F7" stop-opacity="0.9" />
                    <stop offset="50%" stop-color="#D946EF" stop-opacity="1" />
                    <stop offset="100%" stop-color="#7C3AED" stop-opacity="0.6" />
                </linearGradient>
                <filter id="neonGlow" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur stdDeviation="6" result="blur" />
                    <feMerge>
                        <feMergeNode in="blur" />
                        <feMergeNode in="SourceGraphic" />
                    </feMerge>
                </filter>
            </defs>
        </svg>
    </div>

    <div class="sp-page">
        <div class="sp-inner">
            <div class="sp-left">
                <a href="/" class="sp-logo">
                    <div class="sp-logo-mark">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <circle cx="6.5" cy="6.5" r="2.8" stroke="white" stroke-width="1.8" />
                            <circle cx="6.5" cy="17.5" r="2.8" stroke="white" stroke-width="1.8" />
                            <line x1="9" y1="8.1" x2="19.5" y2="19.5" stroke="white" stroke-width="1.8"
                                stroke-linecap="round" />
                            <line x1="9" y1="15.9" x2="14.5" y2="10.5" stroke="white" stroke-width="1.8"
                                stroke-linecap="round" />
                        </svg>
                    </div>
                    <div class="sp-logo-text">
                        <span class="sp-logo-name">SalonPro</span>
                        <span class="sp-logo-tag">Management System</span>
                    </div>
                </a>
                <div class="sp-hero">
                    <h1 class="sp-hero-h1">
                        Manage your <br><span class="sp-grad-text">Salon</span>
                    </h1>
                    <p class="sp-hero-sub">
                        All-in-one solution to manage appointments, staff,<br>
                        customers and services — and grow your salon<br>
                        business effortlessly.
                    </p>
                </div>
                <div class="sp-features">
                    <div class="sp-feat">
                        <div class="sp-feat-icon sp-feat-icon--purple">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <rect x="3" y="4" width="18" height="17" rx="3" stroke="white" stroke-width="1.8" />
                                <path d="M3 9h18M8 2v4M16 2v4" stroke="white" stroke-width="1.8"
                                    stroke-linecap="round" />
                                <path d="M8 14h2M14 14h2M8 18h2" stroke="white" stroke-width="1.8"
                                    stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="sp-feat-body">
                            <p class="sp-feat-title">Smart Scheduling</p>
                            <p class="sp-feat-desc">Automated booking &amp; calendar management</p>
                        </div>
                    </div>
                    <div class="sp-feat">
                        <div class="sp-feat-icon sp-feat-icon--pink">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <circle cx="9" cy="8" r="3.5" stroke="white" stroke-width="1.8" />
                                <circle cx="17" cy="9" r="2.5" stroke="white" stroke-width="1.8" />
                                <path d="M3 20c0-3.3 2.7-5 6-5s6 1.7 6 5" stroke="white" stroke-width="1.8"
                                    stroke-linecap="round" />
                                <path d="M15 16.5c2.2 0 4 1.1 4.5 3.5" stroke="white" stroke-width="1.8"
                                    stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="sp-feat-body">
                            <p class="sp-feat-title">Staff Management</p>
                            <p class="sp-feat-desc">Track performance &amp; manage your team</p>
                        </div>
                    </div>
                    <div class="sp-feat">
                        <div class="sp-feat-icon sp-feat-icon--amber">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <path d="M4 19V5M4 19h16M8 15l3-4 3 2 4-6" stroke="white" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="sp-feat-body">
                            <p class="sp-feat-title">Analytics &amp; Reports</p>
                            <p class="sp-feat-desc">Real-time insights to grow your business</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sp-card">
                {{ $slot }}
                <div class="sp-card-decor">
                    <img src="{{ asset('images/auth/820b7ce4-6162-4c88-af47-d75cc3f21dba.png') }}" alt=""
                        class="sp-tools-img" aria-hidden="true">
               </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        window.spTogglePwd = function (inputId) {
            const f = document.getElementById(inputId);
            if (!f) return;
            f.type = f.type === 'password' ? 'text' : 'password';
            const wrap = f.closest('.sp-input-wrap');
            if (wrap) {
                wrap.querySelector('.sp-eye-open')?.classList.toggle('hidden');
                wrap.querySelector('.sp-eye-closed')?.classList.toggle('hidden');
            }
        };
    </script>
</body>

</html>