<x-guest-layout>

    <div class="sp-card-head">
        <h1 class="sp-card-title">Welcome Back! 👋</h1>
        <p class="sp-card-sub">Login to access your SalonPro dashboard</p>
    </div>

    @if (session('status'))
        <div class="sp-status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="sp-field">
            <label for="email" class="sp-label">Email Address</label>
            <div class="sp-input-wrap">
                <span class="sp-input-icon-box">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                        <path
                            d="M3 6.5A2.5 2.5 0 0 1 5.5 4h13A2.5 2.5 0 0 1 21 6.5v11A2.5 2.5 0 0 1 18.5 20h-13A2.5 2.5 0 0 1 3 17.5v-11Z"
                            stroke="currentColor" stroke-width="1.8" />
                        <path d="m4 6.5 8 6 8-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    autocomplete="username" placeholder="you@salonpro.com" class="sp-input">
            </div>
            @error('email')
                <p class="sp-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="sp-field">
            <label for="password" class="sp-label">Password</label>
            <div class="sp-input-wrap">
                <span class="sp-input-icon-box">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                        <rect x="5" y="10.5" width="14" height="9.5" rx="2.5" stroke="currentColor"
                            stroke-width="1.8" />
                        <path d="M8 10.5V7.5a4 4 0 1 1 8 0v3" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" />
                    </svg>
                </span>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    placeholder="••••••••" class="sp-input sp-input--pwd">
                <button type="button" class="sp-eye-btn" onclick="spTogglePwd('password')"
                    aria-label="Toggle password visibility">
                    <svg class="sp-eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M1.5 12S5 5 12 5s10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12Z" stroke="currentColor"
                            stroke-width="1.6" />
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6" />
                    </svg>
                    <svg class="sp-eye-closed hidden" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path
                            d="M3 3l18 18M10.6 10.7a3 3 0 0 0 4.2 4.2M7.4 7.5C4.7 9 2.5 12 2.5 12S6 19 12 19c1.7 0 3.2-.4 4.5-1.1M17.4 15.6C19.9 13.8 21.5 12 21.5 12S18 5 12 5c-.9 0-1.8.1-2.6.4"
                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="sp-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="sp-row-between">
            <label for="remember_me" class="sp-remember">
                <input id="remember_me" type="checkbox" name="remember" class="sp-checkbox">
                <span class="sp-remember-text">Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="sp-forgot">Forgot Password?</a>
            @endif
        </div>

        <button type="submit" class="sp-btn-primary">
            Sign In &rarr;
        </button>
    </form>

</x-guest-layout>