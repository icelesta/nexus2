<x-filament-panels::page.simple heading="">

    <div class="nexus-login-page">

        {{-- ==========================================================
        | LEFT : OIL & GAS CORPORATE VISUAL
        =========================================================== --}}

        <section class="nexus-login-visual">

            <div class="nexus-login-visual-bg"></div>
            <div class="nexus-login-visual-overlay"></div>

            <div class="nexus-login-brand">

                <img
                    src="{{ asset('images/nexus_1.png') }}"
                    alt="Nexus ERP 2.0"
                    class="nexus-login-brand-logo"
                >

                <div class="nexus-login-brand-subtitle">
                    ERP SOLUTION FOR BESMINDO GROUP
                </div>

            </div>


            <div class="nexus-login-message">

                <div class="nexus-login-kicker">
                    NEXUS ERP 2.0
                </div>

                <h1>
                    Powering Performance.<br>
                    <span>Energizing Success.</span>
                </h1>

                <div class="nexus-login-accent-line"></div>

                <p>
                    Integrated ERP Solution for the Oil &amp; Gas Industry
                    <br>
                    Delivering Reliable, Safe, and Sustainable Operations.
                </p>


                <div class="nexus-login-features">

                    <div class="nexus-login-feature">
                        <div class="nexus-login-feature-icon">✓</div>
                        <div>
                            <strong>Safe</strong>
                            <span>Operations</span>
                        </div>
                    </div>

                    <div class="nexus-login-feature">
                        <div class="nexus-login-feature-icon">↗</div>
                        <div>
                            <strong>Reliable</strong>
                            <span>Performance</span>
                        </div>
                    </div>

                    <div class="nexus-login-feature">
                        <div class="nexus-login-feature-icon">⌁</div>
                        <div>
                            <strong>Sustainable</strong>
                            <span>Growth</span>
                        </div>
                    </div>

                    <div class="nexus-login-feature">
                        <div class="nexus-login-feature-icon">⚙</div>
                        <div>
                            <strong>Integrated</strong>
                            <span>Solutions</span>
                        </div>
                    </div>

                </div>

            </div>

        </section>


        {{-- ==========================================================
        | RIGHT : LOGIN PANEL
        =========================================================== --}}

        <section class="nexus-login-form-panel">

            <div class="nexus-login-language">
                <span>◎</span>
                English
                <span class="nexus-login-language-arrow">⌄</span>
            </div>


            <div class="nexus-login-card">

                <div class="nexus-login-card-logo">

                    <img
                        src="{{ asset('images/nexus_1.png') }}"
                        alt="Nexus ERP 2.0"
                    >

                </div>


                <div class="nexus-login-heading">

                    <h2>Welcome Back!</h2>

                    <p>
                        Sign in to access your Nexus ERP system
                    </p>

                </div>


                <form
                    wire:submit="authenticate"
                    class="nexus-login-form"
                >

                    {{ $this->form }}


                    @if (filament()->hasPasswordReset())

                        <div class="nexus-login-forgot">

                            <a
                                href="{{ filament()->getRequestPasswordResetUrl() }}"
                            >
                                Forgot password?
                            </a>

                        </div>

                    @endif


                    <button
                        type="submit"
                        class="nexus-login-submit"
                        wire:loading.attr="disabled"
                        wire:target="authenticate"
                    >

                        <span class="nexus-login-submit-content">
                            <span>Sign in</span>
                        </span>

                        <span wire:loading wire:target="authenticate">
                            ...
                        </span>

                    </button>

                </form>


                <div class="nexus-login-divider">
                    <span></span>
                    <strong>NEXUS ERP</strong>
                    <span></span>
                </div>


                <div class="nexus-login-industries">

                    <div>
                        <span class="nexus-industry-icon">♜</span>
                        <small>Exploration</small>
                    </div>

                    <div>
                        <span class="nexus-industry-icon">▥</span>
                        <small>Production</small>
                    </div>

                    <div>
                        <span class="nexus-industry-icon">⌁</span>
                        <small>Pipeline</small>
                    </div>

                    <div>
                        <span class="nexus-industry-icon">⌂</span>
                        <small>Storage</small>
                    </div>

                </div>

            </div>


            <div class="nexus-login-footer">

                <div>
                    © {{ date('Y') }} Nexus ERP. Besmindo Group
                </div>

                <div>
                    Built for the Oil &amp; Gas Industry
                </div>

            </div>

        </section>

    </div>


    <style>
        /* ==========================================================
           NEXUS ERP 2.0
           OIL & GAS CORPORATE LOGIN

           Presentation only.
           Authentication logic remains unchanged.
        ========================================================== */


        /* ----------------------------------------------------------
           REMOVE FILAMENT NATIVE SIMPLE HEADER / TOP LOGO
        ---------------------------------------------------------- */

        .fi-simple-header {
            display: none !important;
        }

        .fi-simple-page {
            min-height: 100vh !important;
            padding: 0 !important;
            background: #f7f9fc !important;
        }

        .fi-simple-layout {
            min-height: 100vh !important;
            width: 100% !important;
            align-items: stretch !important;
        }

        .fi-simple-main-ctn {
            width: 100% !important;
            min-height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
            align-items: stretch !important;
        }

        .fi-simple-main {
            width: 100% !important;
            max-width: none !important;
            min-height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            overflow: hidden !important;
        }


        /* ----------------------------------------------------------
           MAIN SPLIT SCREEN
        ---------------------------------------------------------- */

        .nexus-login-page {
            display: grid;
            grid-template-columns: 49% 51%;
            min-height: 100vh;
            width: 100%;
            background: #f7f9fc;
        }


        /* ----------------------------------------------------------
           LEFT OIL & GAS PANEL
        ---------------------------------------------------------- */

        .nexus-login-visual {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            isolation: isolate;
            background: #071c34;
            color: #fff;
        }

        .nexus-login-visual-bg {
            position: absolute;
            inset: 0;
            z-index: -3;
            background-image:
                linear-gradient(
                    180deg,
                    rgba(4, 25, 48, .30) 0%,
                    rgba(4, 25, 48, .08) 42%,
                    rgba(4, 25, 48, .76) 100%
                ),
                url('/images/bms-2.png');
            background-size: cover;
            background-position: center;
        }

        .nexus-login-visual::after {
            content: "";
            position: absolute;
            z-index: 4;
            right: -1px;
            top: 0;
            width: 22%;
            height: 100%;
            background: linear-gradient(
                112deg,
                transparent 0 46%,
                rgba(255,255,255,.96) 46.5% 48%,
                transparent 48.5%
            );
            pointer-events: none;
        }

        .nexus-login-visual-overlay {
            position: absolute;
            inset: 0;
            z-index: -2;
            background:
                linear-gradient(
                    135deg,
                    rgba(5, 28, 53, .42),
                    transparent 42%,
                    rgba(0, 18, 36, .30)
                );
            pointer-events: none;
        }


        /* ----------------------------------------------------------
           LEFT BRAND
        ---------------------------------------------------------- */

        .nexus-login-brand {
            position: relative;
            z-index: 10;
            padding: 38px 42px 0;
        }

        .nexus-login-brand-logo {
            display: block;
            width: 285px;
            max-width: 62%;
            height: auto;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .nexus-login-brand-subtitle {
            margin-top: 5px;
            padding-left: 4px;
            color: rgba(255,255,255,.95);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .17em;
        }


        /* ----------------------------------------------------------
           LEFT MESSAGE
        ---------------------------------------------------------- */

        .nexus-login-message {
            position: absolute;
            z-index: 10;
            left: 42px;
            bottom: 42px;
            max-width: 690px;
        }

        .nexus-login-kicker {
            color: #f59e0b;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .18em;
            margin-bottom: 9px;
        }

        .nexus-login-message h1 {
            margin: 0;
            color: #fff;
            font-size: clamp(31px, 3.25vw, 50px);
            line-height: 1.03;
            font-weight: 800;
            letter-spacing: -.025em;
        }

        .nexus-login-message h1 span {
            color: #f59e0b;
        }

        .nexus-login-accent-line {
            width: 58px;
            height: 3px;
            margin: 18px 0 13px;
            background: #f59e0b;
        }

        .nexus-login-message p {
            margin: 0;
            color: rgba(255,255,255,.91);
            font-size: 13px;
            line-height: 1.65;
        }


        /* ----------------------------------------------------------
           VALUE PILLARS
        ---------------------------------------------------------- */

        .nexus-login-features {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-top: 27px;
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,.28);
        }

        .nexus-login-feature {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
            padding: 0 12px;
            border-right: 1px solid rgba(255,255,255,.22);
        }

        .nexus-login-feature:first-child {
            padding-left: 0;
        }

        .nexus-login-feature:last-child {
            border-right: 0;
        }

        .nexus-login-feature-icon {
            display: grid;
            place-items: center;
            flex: 0 0 31px;
            width: 31px;
            height: 31px;
            border: 1px solid rgba(245,158,11,.85);
            border-radius: 50%;
            color: #f59e0b;
            font-size: 14px;
            font-weight: 800;
        }

        .nexus-login-feature strong,
        .nexus-login-feature span {
            display: block;
        }

        .nexus-login-feature strong {
            color: #fff;
            font-size: 10px;
            line-height: 1.15;
        }

        .nexus-login-feature span {
            margin-top: 2px;
            color: rgba(255,255,255,.78);
            font-size: 9px;
        }


        /* ----------------------------------------------------------
           RIGHT PANEL
        ---------------------------------------------------------- */

        .nexus-login-form-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 42px 6vw 30px 5vw;
            background:
                radial-gradient(
                    circle at 88% 12%,
                    rgba(37,99,235,.075),
                    transparent 24%
                ),
                radial-gradient(
                    circle at 18% 92%,
                    rgba(245,158,11,.055),
                    transparent 28%
                ),
                #f8fafc;
        }

        .nexus-login-form-panel::after {
            content: "";
            position: absolute;
            right: 0;
            bottom: 0;
            width: 42%;
            height: 52%;
            opacity: .035;
            background:
                repeating-linear-gradient(
                    90deg,
                    #12385d 0 2px,
                    transparent 2px 34px
                );
            mask-image: linear-gradient(
                to top,
                black,
                transparent
            );
            pointer-events: none;
        }


        /* ----------------------------------------------------------
           LANGUAGE
        ---------------------------------------------------------- */

        .nexus-login-language {
            position: absolute;
            right: 30px;
            top: 27px;
            z-index: 20;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 13px;
            border: 1px solid #d9e0e8;
            border-radius: 8px;
            background: rgba(255,255,255,.90);
            color: #334155;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(15,23,42,.05);
        }

        .nexus-login-language-arrow {
            color: #64748b;
            font-size: 15px;
            line-height: 1;
        }


        /* ----------------------------------------------------------
           LOGIN CARD
        ---------------------------------------------------------- */

        .nexus-login-card {
            position: relative;
            z-index: 5;
            width: min(100%, 560px);
            padding: 38px 42px 31px;
            border: 1px solid rgba(15,23,42,.08);
            border-radius: 18px;
            background: rgba(255,255,255,.96);
            box-shadow:
                0 24px 60px rgba(15,23,42,.10),
                0 6px 20px rgba(15,23,42,.05);
            backdrop-filter: blur(12px);
        }

        .nexus-login-card-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .nexus-login-card-logo img {
            display: block;
            width: 245px;
            height: auto;
            max-height: 72px;
            object-fit: contain;
        }


        /* ----------------------------------------------------------
           WELCOME
        ---------------------------------------------------------- */

        .nexus-login-heading {
            text-align: center;
            margin-bottom: 24px;
        }

        .nexus-login-heading h2 {
            margin: 0;
            color: #12385d;
            font-size: 27px;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -.02em;
        }

        .nexus-login-heading p {
            margin: 7px 0 0;
            color: #64748b;
            font-size: 12px;
        }


        /* ----------------------------------------------------------
           FORM
        ---------------------------------------------------------- */

        .nexus-login-form {
            width: 100%;
        }

        .nexus-login-form .fi-fo-field-wrp {
            margin-bottom: 16px;
        }

        .nexus-login-form .fi-fo-field-wrp-label {
            color: #173b60 !important;
            font-weight: 700 !important;
        }

        .nexus-login-form input {
            min-height: 48px;
            border-color: #d5dde7 !important;
            border-radius: 9px !important;
            background: #fff !important;
            box-shadow: none !important;
        }

        .nexus-login-form input:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37,99,235,.10) !important;
        }

        .nexus-login-forgot {
            display: flex;
            justify-content: flex-end;
            margin-top: -5px;
            margin-bottom: 14px;
        }

        .nexus-login-forgot a {
            color: #2563eb;
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
        }

        .nexus-login-forgot a:hover {
            text-decoration: underline;
        }


        /* ----------------------------------------------------------
           SIGN IN
        ---------------------------------------------------------- */

        .nexus-login-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            min-height: 48px;
            margin-top: 15px;
            padding: 0 18px;
            border: 0;
            border-radius: 9px;
            background: linear-gradient(
                135deg,
                #f59e0b,
                #e89a05
            );
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(245,158,11,.22);
            transition:
                transform .18s ease,
                box-shadow .18s ease,
                filter .18s ease;
        }

        .nexus-login-submit:hover {
            filter: brightness(1.03);
            transform: translateY(-1px);
            box-shadow: 0 11px 22px rgba(245,158,11,.28);
        }

        .nexus-login-submit:disabled {
            opacity: .7;
            cursor: wait;
            transform: none;
        }

        .nexus-login-submit-icon {
            display: grid;
            place-items: center;
            width: 21px;
            height: 21px;
            border: 1px solid rgba(255,255,255,.7);
            border-radius: 50%;
            line-height: 1;
        }


        /* ----------------------------------------------------------
           NEXUS ERP INDUSTRY BAR
        ---------------------------------------------------------- */

        .nexus-login-divider {
            display: flex;
            align-items: center;
            gap: 13px;
            margin: 26px 0 18px;
            color: #64748b;
            font-size: 9px;
            letter-spacing: .08em;
        }

        .nexus-login-divider span {
            flex: 1;
            height: 1px;
            background: #dbe2ea;
        }

        .nexus-login-divider strong {
            color: #334155;
            font-weight: 800;
        }

        .nexus-login-industries {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }

        .nexus-login-industries > div {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-height: 58px;
            border-right: 1px solid #e2e8f0;
        }

        .nexus-login-industries > div:last-child {
            border-right: 0;
        }

        .nexus-industry-icon {
            color: #12385d;
            font-size: 22px;
            line-height: 1;
        }

        .nexus-login-industries small {
            color: #334155;
            font-size: 9px;
            font-weight: 700;
        }


        /* ----------------------------------------------------------
           FOOTER
        ---------------------------------------------------------- */

        .nexus-login-footer {
            position: relative;
            z-index: 5;
            margin-top: 18px;
            text-align: center;
            color: #64748b;
            font-size: 12px;
            line-height: 1.7;
        }


        /* ----------------------------------------------------------
           RESPONSIVE
        ---------------------------------------------------------- */

        @media (max-width: 1200px) {

            .nexus-login-page {
                grid-template-columns: 47% 53%;
            }

            .nexus-login-brand {
                padding-left: 30px;
            }

            .nexus-login-message {
                left: 30px;
                right: 24px;
                bottom: 30px;
            }

            .nexus-login-message h1 {
                font-size: clamp(27px, 3vw, 42px);
            }

            .nexus-login-features {
                grid-template-columns: repeat(2, 1fr);
                row-gap: 12px;
            }

            .nexus-login-feature:nth-child(2) {
                border-right: 0;
            }

            .nexus-login-form-panel {
                padding-left: 3vw;
                padding-right: 3vw;
            }
        }


        @media (max-width: 900px) {

            .nexus-login-page {
                display: block;
            }

            .nexus-login-visual {
                display: none;
            }

            .nexus-login-form-panel {
                min-height: 100vh;
                padding: 74px 22px 26px;
            }

            .nexus-login-language {
                right: 18px;
                top: 17px;
            }

            .nexus-login-card {
                width: min(100%, 550px);
                padding: 34px 30px 28px;
            }
        }


        @media (max-width: 560px) {

            .nexus-login-form-panel {
                padding-left: 12px;
                padding-right: 12px;
            }

            .nexus-login-card {
                padding: 28px 20px 24px;
                border-radius: 14px;
            }

            .nexus-login-card-logo img {
                width: 215px;
            }

            .nexus-login-heading h2 {
                font-size: 24px;
            }

            .nexus-login-industries {
                grid-template-columns: repeat(2, 1fr);
            }

            .nexus-login-industries > div:nth-child(2) {
                border-right: 0;
            }

            .nexus-login-industries > div:nth-child(-n+2) {
                border-bottom: 1px solid #e2e8f0;
            }
        }


        @media (prefers-reduced-motion: reduce) {

            .nexus-login-submit {
                transition: none;
            }

        }
    </style>

</x-filament-panels::page.simple>
