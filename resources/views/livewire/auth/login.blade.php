@php
    $loginLandingSetting = \Illuminate\Support\Facades\Schema::hasTable('landing_settings')
        ? \App\Models\LandingSetting::query()
            ->where('is_active', true)
            ->latest('id')
            ->first()
        : null;
    $loginLogoPath = $loginLandingSetting?->logo_path;
    $loginLogoUrl = $loginLogoPath
        ? (str_starts_with($loginLogoPath, 'http') || str_starts_with($loginLogoPath, '/') ? $loginLogoPath : asset($loginLogoPath))
        : asset('landing/assets/logo.png');
    $loginSiteName = $loginLandingSetting?->site_name ?? 'Purnama Bersantai';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('Login')])
        <style>
            :root {
                --login-orange: #f25700;
                --login-brown: #2f2e2e;
                --login-yellow: #fff700;
            }

            body {
                min-height: 100vh;
                background:
                    radial-gradient(circle at 18% 18%, rgba(255, 247, 0, 0.18), transparent 24rem),
                    radial-gradient(circle at 82% 80%, rgba(47, 46, 46, 0.22), transparent 26rem),
                    var(--login-orange);
                color: #ffffff;
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            }

            .login-page {
                display: flex;
                min-height: 100vh;
                align-items: center;
                justify-content: center;
                padding: 2rem 1rem;
            }

            .login-shell {
                width: min(100%, 28rem);
            }

            .login-logo {
                margin: 0 auto 1.5rem;
                display: block;
                height: 5rem;
                width: 5rem;
                object-fit: contain;
            }

            .login-card {
                border: 1px solid rgba(255, 255, 255, 0.16);
                border-radius: 2rem;
                background: rgba(47, 46, 46, 0.88);
                padding: 1.5rem;
                box-shadow: 0 1.5rem 4rem rgba(47, 46, 46, 0.25);
                backdrop-filter: blur(18px);
            }

            .login-title {
                font-size: clamp(2.25rem, 9vw, 3.75rem);
                line-height: 0.95;
                font-weight: 900;
                letter-spacing: 0.06em;
                text-align: center;
                text-transform: uppercase;
            }

            .login-subtitle {
                margin-top: 0.75rem;
                text-align: center;
                color: rgba(255, 255, 255, 0.72);
                font-size: 0.95rem;
                line-height: 1.6;
            }

            .login-field {
                display: block;
            }

            .login-label {
                margin-bottom: 0.5rem;
                display: block;
                color: rgba(255, 255, 255, 0.78);
                font-size: 0.78rem;
                font-weight: 800;
                letter-spacing: 0.16em;
                text-transform: uppercase;
            }

            .login-input {
                width: 100%;
                border: 1px solid rgba(255, 255, 255, 0.16);
                border-radius: 1rem;
                background: rgba(255, 255, 255, 0.08);
                padding: 0.95rem 1rem;
                color: #ffffff;
                outline: none;
                transition: border-color 160ms ease, box-shadow 160ms ease, background 160ms ease;
            }

            .login-input::placeholder {
                color: rgba(255, 255, 255, 0.4);
            }

            .login-input:focus {
                border-color: rgba(255, 247, 0, 0.72);
                background: rgba(255, 255, 255, 0.11);
                box-shadow: 0 0 0 4px rgba(255, 247, 0, 0.14);
            }

            .login-password-wrap {
                position: relative;
            }

            .login-password-wrap .login-input {
                padding-right: 3.2rem;
            }

            .login-password-toggle {
                position: absolute;
                top: 50%;
                right: 0.8rem;
                display: inline-grid;
                height: 2.25rem;
                width: 2.25rem;
                place-items: center;
                border: 0;
                border-radius: 9999px;
                background: rgba(255, 255, 255, 0.1);
                color: rgba(255, 255, 255, 0.76);
                transform: translateY(-50%);
                transition: background 160ms ease, color 160ms ease;
            }

            .login-password-toggle:hover,
            .login-password-toggle:focus-visible {
                background: rgba(255, 247, 0, 0.16);
                color: var(--login-yellow);
                outline: none;
            }

            .login-password-toggle svg {
                height: 1.15rem;
                width: 1.15rem;
            }

            .login-password-toggle[data-visible="true"] .login-eye-open,
            .login-password-toggle[data-visible="false"] .login-eye-closed {
                display: none;
            }

            .login-error {
                margin-top: 0.45rem;
                display: block;
                color: var(--login-yellow);
                font-size: 0.82rem;
                font-weight: 700;
            }

            .login-checkbox {
                display: inline-flex;
                align-items: center;
                gap: 0.65rem;
                color: rgba(255, 255, 255, 0.78);
                font-size: 0.92rem;
                font-weight: 700;
            }

            .login-checkbox input {
                height: 1.1rem;
                width: 1.1rem;
                accent-color: var(--login-yellow);
            }

            .login-button {
                display: inline-flex;
                width: 100%;
                align-items: center;
                justify-content: center;
                gap: 0.75rem;
                border: 0;
                border-radius: 1rem;
                background: linear-gradient(135deg, var(--login-yellow), #ff9f1c);
                padding: 1rem 1.25rem;
                color: var(--login-brown);
                font-size: 1rem;
                font-weight: 900;
                letter-spacing: 0.14em;
                text-transform: uppercase;
                transition: transform 160ms ease, filter 160ms ease, opacity 160ms ease;
            }

            .login-button:hover {
                filter: brightness(1.03);
                transform: translateY(-1px);
            }

            .login-button:disabled {
                cursor: wait;
                opacity: 0.76;
                transform: none;
            }

            .login-spinner {
                display: none;
                height: 1.1rem;
                width: 1.1rem;
                border: 2px solid rgba(47, 46, 46, 0.24);
                border-top-color: var(--login-brown);
                border-radius: 9999px;
                animation: login-spin 720ms linear infinite;
            }

            .login-button[data-loading="true"] .login-spinner {
                display: inline-block;
            }

            .login-button[data-loading="true"] .login-button-text {
                display: none;
            }

            @keyframes login-spin {
                to {
                    transform: rotate(360deg);
                }
            }
        </style>
    </head>

    <body>
        <main class="login-page">
            <div class="login-shell">
                <a href="{{ route('home') }}" wire:navigate aria-label="Kembali ke landing page">
                    <img src="{{ $loginLogoUrl }}" alt="{{ $loginSiteName }}" class="login-logo">
                </a>

                <section class="login-card">
                    <h1 class="login-title">Login</h1>
                    <p class="login-subtitle">Masuk ke Purnama Panel untuk mengelola konten landing.</p>

                    @if (session('status'))
                        <div class="mt-6 rounded-2xl border border-[#fff700]/30 bg-[#fff700]/10 px-4 py-3 text-sm font-semibold text-[#fff700]">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('login.store') }}"
                        class="mt-7 space-y-5"
                        onsubmit="const button = this.querySelector('[data-login-submit]'); button.disabled = true; button.dataset.loading = 'true';"
                    >
                        @csrf

                        <label class="login-field">
                            <span class="login-label">Email atau User</span>
                            <input
                                name="email"
                                value="{{ old('email') }}"
                                type="text"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="admin@email.com atau username"
                                class="login-input"
                            >
                            @error('email')
                                <span class="login-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="login-field">
                            <span class="login-label">Password</span>
                            <span class="login-password-wrap">
                                <input
                                    name="password"
                                    type="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Masukkan password"
                                    class="login-input"
                                    data-password-input
                                >
                                <button
                                    type="button"
                                    class="login-password-toggle"
                                    aria-label="Tampilkan password"
                                    aria-pressed="false"
                                    data-visible="false"
                                    data-password-toggle
                                >
                                    <svg class="login-eye-open" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M2.25 12s3.5-6.25 9.75-6.25S21.75 12 21.75 12 18.25 18.25 12 18.25 2.25 12 2.25 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 15.25a3.25 3.25 0 1 0 0-6.5 3.25 3.25 0 0 0 0 6.5Z" stroke="currentColor" stroke-width="1.8"/>
                                    </svg>
                                    <svg class="login-eye-closed" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M10.6 5.9c.45-.1.92-.15 1.4-.15 6.25 0 9.75 6.25 9.75 6.25a18.4 18.4 0 0 1-2.72 3.39M6.34 7.42C3.75 9.2 2.25 12 2.25 12S5.75 18.25 12 18.25c1.72 0 3.22-.47 4.5-1.16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </span>
                            @error('password')
                                <span class="login-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <div class="flex items-center gap-4">
                            <label class="login-checkbox">
                                <input name="remember" type="checkbox" value="1" @checked(old('remember'))>
                                <span>Remember me</span>
                            </label>
                        </div>

                        <button type="submit" class="login-button" data-login-submit>
                            <span class="login-spinner" aria-hidden="true"></span>
                            <span class="login-button-text">Login</span>
                            <span class="sr-only">Loading</span>
                        </button>
                    </form>
                </section>
            </div>
        </main>

        @fluxScripts
        <script>
            document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
                toggle.addEventListener('click', () => {
                    const input = toggle.closest('.login-password-wrap')?.querySelector('[data-password-input]');

                    if (! input) {
                        return;
                    }

                    const isVisible = input.type === 'text';
                    input.type = isVisible ? 'password' : 'text';
                    toggle.dataset.visible = String(! isVisible);
                    toggle.setAttribute('aria-pressed', String(! isVisible));
                    toggle.setAttribute('aria-label', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
                });
            });
        </script>
    </body>
</html>
