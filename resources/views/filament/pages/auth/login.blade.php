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
   NEXUS ERP 2.0 — LOGIN V2
   Premium Enterprise / Modern Industrial
   Authentication markup remains unchanged.
   ========================================================== */

.fi-simple-header { display: none !important; }

.fi-simple-page,
.fi-simple-layout,
.fi-simple-main-ctn,
.fi-simple-main {
    width: 100% !important;
    min-height: 100vh !important;
    margin: 0 !important;
    padding: 0 !important;
}

.fi-simple-page { background: #f4f8fd !important; }

.fi-simple-layout,
.fi-simple-main-ctn { align-items: stretch !important; }

.fi-simple-main {
    max-width: none !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    overflow: hidden !important;
}

/* ==========================================================
   MAIN
   ========================================================== */

.nexus-login-page {
    position: relative;
    display: grid;
    grid-template-columns: 52% 48%;
    width: 100%;
    min-height: 100vh;
    overflow: hidden;
    background: #f7faff;
}

/* ==========================================================
   LEFT VISUAL
   ========================================================== */

.nexus-login-visual {
    position: relative;
    min-height: 100vh;
    overflow: hidden;
    isolation: isolate;
    color: #fff;
    background: #061b33;
}

.nexus-login-visual-bg {
    position: absolute;
    inset: -1%;
    z-index: -6;
    background-image:
        linear-gradient(
            90deg,
            rgba(2, 22, 43, .10) 0%,
            rgba(2, 22, 43, .03) 53%,
            rgba(2, 22, 43, .18) 74%,
            rgba(229, 238, 248, .72) 100%
        ),
        linear-gradient(
            180deg,
            rgba(2, 17, 35, .18) 0%,
            transparent 38%,
            rgba(2, 17, 35, .70) 100%
        ),
        url('{{ asset('images/bms-2.png') }}');
    background-size: cover;
    background-position: center center;
    transform: scale(1.01);
}

/* richer blue / gold cinematic treatment */
.nexus-login-visual::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -5;
    pointer-events: none;
    background:
        radial-gradient(
            ellipse at 21% 17%,
            rgba(29, 116, 216, .28),
            transparent 31%
        ),
        radial-gradient(
            ellipse at 72% 37%,
            rgba(245, 158, 11, .14),
            transparent 27%
        ),
        linear-gradient(
            128deg,
            rgba(3, 25, 50, .30),
            transparent 45%,
            rgba(1, 17, 35, .16) 72%,
            rgba(255,255,255,.12) 100%
        );
}

/* clean, thin transition instead of a large white wedge */
.nexus-login-visual::after {
    content: "";
    position: absolute;
    z-index: 5;
    top: -5%;
    right: -1px;
    width: 18%;
    height: 110%;
    pointer-events: none;
    background:
        linear-gradient(
            104deg,
            transparent 0 54%,
            rgba(255,255,255,.72) 54.7% 56.1%,
            rgba(255,255,255,.18) 56.5% 58%,
            transparent 58.5%
        );
    opacity: .82;
}

/* soft fade only at the extreme boundary */
.nexus-login-visual-overlay {
    position: absolute;
    inset: 0;
    z-index: -2;
    pointer-events: none;
    background:
        linear-gradient(
            90deg,
            transparent 0%,
            transparent 69%,
            rgba(235,244,252,.10) 83%,
            rgba(244,248,252,.42) 100%
        ),
        linear-gradient(
            180deg,
            rgba(0,0,0,.02),
            transparent 46%,
            rgba(0,12,28,.28)
        );
}

/* ==========================================================
   BRAND
   ========================================================== */

.nexus-login-brand {
    position: relative;
    z-index: 10;
    padding: 34px 42px 0;
}

.nexus-login-brand-logo {
    display: block;
    width: 285px;
    max-width: 58%;
    height: auto;
    object-fit: contain;
    filter: brightness(0) invert(1)
            drop-shadow(0 4px 13px rgba(0,0,0,.18));
}

.nexus-login-brand-subtitle {
    margin-top: 5px;
    padding-left: 4px;
    color: rgba(255,255,255,.94);
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .17em;
    text-shadow: 0 2px 8px rgba(0,0,0,.28);
}

/* ==========================================================
   MESSAGE
   ========================================================== */

.nexus-login-message {
    position: absolute;
    z-index: 10;
    left: 42px;
    right: 46px;
    bottom: 38px;
    max-width: 760px;
}

.nexus-login-kicker {
    display: inline-flex;
    align-items: center;
    min-height: 23px;
    padding: 0 10px;
    border: 1px solid rgba(245,158,11,.48);
    border-radius: 999px;
    background: rgba(3,24,49,.28);
    color: #fbbf24;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .19em;
    margin-bottom: 9px;
    backdrop-filter: blur(8px);
}

.nexus-login-message h1 {
    margin: 0;
    color: #fff;
    font-size: clamp(32px, 3.15vw, 51px);
    line-height: 1.01;
    font-weight: 850;
    letter-spacing: -.035em;
    text-shadow: 0 5px 20px rgba(0,0,0,.23);
}

.nexus-login-message h1 span {
    color: #f59e0b;
    text-shadow: 0 4px 18px rgba(245,158,11,.14);
}

.nexus-login-accent-line {
    width: 72px;
    height: 3px;
    margin: 16px 0 12px;
    border-radius: 999px;
    background: linear-gradient(90deg, #f59e0b, #fbbf24);
    box-shadow: 0 2px 12px rgba(245,158,11,.32);
}

.nexus-login-message p {
    margin: 0;
    color: rgba(255,255,255,.92);
    font-size: 12px;
    line-height: 1.62;
    text-shadow: 0 2px 8px rgba(0,0,0,.22);
}

/* ==========================================================
   VALUE PILLARS
   ========================================================== */

.nexus-login-features {
    display: grid;
    grid-template-columns: repeat(4, minmax(0,1fr));
    margin-top: 23px;
    padding-top: 14px;
    border-top: 1px solid rgba(255,255,255,.28);
}

.nexus-login-feature {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
    padding: 0 12px;
    border-right: 1px solid rgba(255,255,255,.19);
}

.nexus-login-feature:first-child { padding-left: 0; }
.nexus-login-feature:last-child { border-right: 0; }

.nexus-login-feature-icon {
    display: grid;
    place-items: center;
    flex: 0 0 30px;
    width: 30px;
    height: 30px;
    border: 1px solid rgba(245,158,11,.9);
    border-radius: 50%;
    color: #fbbf24;
    background: rgba(3,24,49,.20);
    font-size: 13px;
    font-weight: 800;
}

.nexus-login-feature strong,
.nexus-login-feature span { display: block; }

.nexus-login-feature strong {
    color: #fff;
    font-size: 9px;
    line-height: 1.15;
}

.nexus-login-feature span {
    margin-top: 2px;
    color: rgba(255,255,255,.77);
    font-size: 8px;
}

/* ==========================================================
   RIGHT PANEL — less empty, more integrated
   ========================================================== */

.nexus-login-form-panel {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 44px 5vw 28px 3.8vw;
    overflow: hidden;
    background:
        radial-gradient(
            ellipse at 0% 52%,
            rgba(37,99,235,.13),
            transparent 39%
        ),
        radial-gradient(
            ellipse at 100% 9%,
            rgba(37,99,235,.085),
            transparent 30%
        ),
        radial-gradient(
            ellipse at 72% 100%,
            rgba(245,158,11,.065),
            transparent 32%
        ),
        linear-gradient(
            135deg,
            #edf5fd 0%,
            #f7faff 42%,
            #ffffff 100%
        );
}

/* blue glow connecting both panels */
.nexus-login-form-panel::before {
    content: "";
    position: absolute;
    left: -90px;
    top: 7%;
    width: 220px;
    height: 86%;
    pointer-events: none;
    background:
        linear-gradient(
            90deg,
            rgba(37,99,235,.15),
            rgba(37,99,235,.045),
            transparent
        );
    filter: blur(25px);
}

/* subtle enterprise grid */
.nexus-login-form-panel::after {
    content: "";
    position: absolute;
    right: 0;
    bottom: 0;
    width: 60%;
    height: 72%;
    opacity: .045;
    pointer-events: none;
    background:
        repeating-linear-gradient(
            90deg,
            #12385d 0 1px,
            transparent 1px 36px
        ),
        repeating-linear-gradient(
            0deg,
            #12385d 0 1px,
            transparent 1px 36px
        );
    mask-image: linear-gradient(to top right, black, transparent 78%);
}

/* ==========================================================
   LANGUAGE
   ========================================================== */

.nexus-login-language {
    position: absolute;
    right: 28px;
    top: 25px;
    z-index: 20;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 13px;
    border: 1px solid rgba(148,163,184,.30);
    border-radius: 9px;
    background: rgba(255,255,255,.88);
    color: #334155;
    font-size: 11px;
    font-weight: 700;
    box-shadow: 0 7px 24px rgba(15,23,42,.06);
    backdrop-filter: blur(12px);
}

.nexus-login-language-arrow {
    color: #64748b;
    font-size: 14px;
    line-height: 1;
}

/* ==========================================================
   LOGIN CARD — larger and more premium
   ========================================================== */

.nexus-login-card {
    position: relative;
    z-index: 5;
    width: min(100%, 600px);
    padding: 40px 47px 31px;
    border: 1px solid rgba(148,163,184,.24);
    border-radius: 20px;
    background:
        linear-gradient(
            145deg,
            rgba(255,255,255,.99),
            rgba(249,252,255,.975)
        );
    box-shadow:
        0 34px 85px rgba(15,23,42,.13),
        0 10px 30px rgba(37,99,235,.06),
        inset 0 1px 0 rgba(255,255,255,1);
    backdrop-filter: blur(18px);
}

/* top premium edge */
.nexus-login-card::before {
    content: "";
    position: absolute;
    left: 12%;
    right: 12%;
    top: 0;
    height: 2px;
    border-radius: 0 0 99px 99px;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(37,99,235,.60),
        rgba(245,158,11,.72),
        transparent
    );
}

/* very soft inner glow */
.nexus-login-card::after {
    content: "";
    position: absolute;
    inset: 1px;
    border-radius: inherit;
    pointer-events: none;
    background:
        radial-gradient(
            circle at 12% 8%,
            rgba(37,99,235,.045),
            transparent 28%
        );
}

.nexus-login-card-logo {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: center;
    margin-bottom: 13px;
}

.nexus-login-card-logo img {
    display: block;
    width: 250px;
    height: auto;
    max-height: 73px;
    object-fit: contain;
}

/* ==========================================================
   HEADING
   ========================================================== */

.nexus-login-heading {
    position: relative;
    z-index: 1;
    text-align: center;
    margin-bottom: 25px;
}

.nexus-login-heading h2 {
    margin: 0;
    color: #12385d;
    font-size: 28px;
    line-height: 1.18;
    font-weight: 850;
    letter-spacing: -.025em;
}

.nexus-login-heading p {
    margin: 7px 0 0;
    color: #64748b;
    font-size: 11px;
}

/* ==========================================================
   FORM
   ========================================================== */

.nexus-login-form {
    position: relative;
    z-index: 1;
    width: 100%;
}

.nexus-login-form .fi-fo-field-wrp {
    margin-bottom: 16px;
}

.nexus-login-form .fi-fo-field-wrp-label {
    color: #173b60 !important;
    font-weight: 700 !important;
}

.nexus-login-form input:not(.fi-checkbox-input) {
    min-height: 51px;
    border-color: #d3dce7 !important;
    border-radius: 10px !important;
    background: #fff !important;
    box-shadow: 0 2px 9px rgba(15,23,42,.025) !important;
    transition: border-color .18s ease, box-shadow .18s ease;
}

.nexus-login-form input:not(.fi-checkbox-input):focus {
    border-color: #2563eb !important;
    box-shadow:
        0 0 0 3px rgba(37,99,235,.09),
        0 5px 15px rgba(37,99,235,.055) !important;
}

/* ==========================================================
   REMEMBER ME
   ========================================================== */

.nexus-login-form .fi-checkbox-input {
    appearance: none !important;
    -webkit-appearance: none !important;
    display: inline-block !important;
    width: 19px !important;
    height: 19px !important;
    min-width: 19px !important;
    min-height: 19px !important;
    margin: 0 9px 0 0 !important;
    padding: 0 !important;
    position: relative !important;
    background: #fff !important;
    border: 1.8px solid #64748b !important;
    border-radius: 5px !important;
    box-shadow: none !important;
    cursor: pointer !important;
    vertical-align: middle !important;
}

.nexus-login-form .fi-checkbox-input:hover {
    border-color: #2563eb !important;
}

.nexus-login-form .fi-checkbox-input:checked {
    background: #2563eb !important;
    border-color: #2563eb !important;
}

.nexus-login-form .fi-checkbox-input:checked::after {
    content: "";
    position: absolute;
    left: 5px;
    top: 1px;
    width: 5px;
    height: 10px;
    border: solid #fff;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.nexus-login-form .fi-checkbox-input:focus {
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(37,99,235,.13) !important;
}

.nexus-login-form .fi-checkbox-input + label,
.nexus-login-form .fi-fo-checkbox label {
    color: #334155 !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
}

/* ==========================================================
   FORGOT
   ========================================================== */

.nexus-login-forgot {
    display: flex;
    justify-content: flex-end;
    margin-top: -4px;
    margin-bottom: 13px;
}

.nexus-login-forgot a {
    color: #2563eb;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none;
}

.nexus-login-forgot a:hover {
    text-decoration: underline;
}

/* ==========================================================
   SIGN IN
   ========================================================== */

.nexus-login-submit {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 51px;
    margin-top: 15px;
    padding: 0 18px;
    border: 0;
    border-radius: 10px;
    background: linear-gradient(
        135deg,
        #f59e0b 0%,
        #f6a000 50%,
        #e99500 100%
    );
    color: #fff;
    font-size: 13px;
    font-weight: 850;
    cursor: pointer;
    box-shadow:
        0 10px 23px rgba(245,158,11,.23),
        inset 0 1px 0 rgba(255,255,255,.25);
    transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
}

.nexus-login-submit:hover {
    filter: brightness(1.035);
    transform: translateY(-1px);
    box-shadow:
        0 13px 28px rgba(245,158,11,.29),
        inset 0 1px 0 rgba(255,255,255,.25);
}

.nexus-login-submit:disabled {
    opacity: .7;
    cursor: wait;
    transform: none;
}

/* ==========================================================
   INDUSTRY BAR
   ========================================================== */

.nexus-login-divider {
    display: flex;
    align-items: center;
    gap: 13px;
    margin: 27px 0 17px;
    color: #64748b;
    font-size: 8px;
    letter-spacing: .10em;
}

.nexus-login-divider span {
    flex: 1;
    height: 1px;
    background: #dbe2ea;
}

.nexus-login-divider strong {
    color: #334155;
    font-weight: 850;
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
    min-height: 61px;
    border-right: 1px solid #e2e8f0;
}

.nexus-login-industries > div:last-child {
    border-right: 0;
}

.nexus-industry-icon {
    color: #12385d;
    font-size: 21px;
    line-height: 1;
}

.nexus-login-industries small {
    color: #334155;
    font-size: 8px;
    font-weight: 750;
}

/* ==========================================================
   FOOTER
   ========================================================== */

.nexus-login-footer {
    position: relative;
    z-index: 5;
    margin-top: 17px;
    text-align: center;
    color: #64748b;
    font-size: 10px;
    line-height: 1.7;
}

/* ==========================================================
   RESPONSIVE
   ========================================================== */

@media (max-width: 1250px) {
    .nexus-login-page {
        grid-template-columns: 51% 49%;
    }

    .nexus-login-brand {
        padding-left: 30px;
    }

    .nexus-login-message {
        left: 30px;
        right: 28px;
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

    .nexus-login-card {
        width: min(100%, 570px);
        padding-left: 40px;
        padding-right: 40px;
    }
}

@media (max-width: 950px) {
    .nexus-login-page {
        grid-template-columns: 1fr;
    }

    .nexus-login-visual {
        display: none;
    }

    .nexus-login-form-panel {
        min-height: 100vh;
        padding: 76px 22px 25px;
    }

    .nexus-login-language {
        right: 18px;
        top: 17px;
    }

    .nexus-login-card {
        width: min(100%, 560px);
        padding: 34px 30px 28px;
    }
}

@media (max-width: 560px) {
    .nexus-login-form-panel {
        padding-left: 12px;
        padding-right: 12px;
    }

    .nexus-login-card {
        padding: 27px 20px 23px;
        border-radius: 15px;
    }

    .nexus-login-card-logo img {
        width: 210px;
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
