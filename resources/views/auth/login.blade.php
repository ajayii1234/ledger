<x-guest-layout>
    {{-- Hide possible Laravel logo selectors injected by the layout --}}
    <style>
        svg[class*="logo"],
        .application-logo,
        img[alt*="Laravel"],
        .brand-logo,
        .logo {
            display: none !important;
        }
    </style>

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="w-full max-w-md space-y-8">
            {{-- Project title / hero (light-only) --}}
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold leading-tight text-gray-900">
                    Automatic Expense Tracker
                </h1>

                <p class="mt-3 text-sm sm:text-base text-gray-600 max-w-prose mx-auto">
                    Upload receipts as CSV files — the app auto-populates expense records for fast reconciliation and accurate stock price decimals.
                </p>

                <div class="mt-4 inline-flex items-center gap-2 justify-center">
                    <!-- <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100 shadow-sm">
                        In development
                    </span> -->
                    <span class="inline-block px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-800 border border-blue-100">
                        CSV upload · Auto-populate
                    </span>
                </div>
            </div>

            {{-- Card containing the form (light-only) --}}
            <div class="bg-white py-8 px-6 shadow-lg rounded-2xl border border-gray-200">
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email"
                                      class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                      type="email"
                                      name="email"
                                      :value="old('email')"
                                      required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" />

                        <x-text-input id="password"
                                      class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                      type="password"
                                      name="password"
                                      required autocomplete="current-password" />

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="block mt-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                            <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        @if (Route::has('password.request'))
                            {{-- Uncomment to enable "Forgot your password?" link --}}
                            {{-- <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a> --}}
                        @endif

                        <x-primary-button class="ms-3">
                            {{ __('Log in') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Small instructions / CSV tip --}}
            <div class="text-center text-xs text-gray-500">
                Tip: Upload CSVs with columns like <code class="bg-gray-100 px-1 py-0.5 rounded">date, merchant, amount, currency, category</code> and the system will auto-populate expense entries.
            </div>

            <!-- {{-- Small footer / credits --}}
            <div class="text-center text-xs text-gray-500">
                Built with Laravel · © {{ date('Y') }}
            </div> -->
        </div>
    </div>
</x-guest-layout>
