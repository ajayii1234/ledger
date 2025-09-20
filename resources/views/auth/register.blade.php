<x-guest-layout>
    <div class="w-full max-w-md mx-auto text-center mb-6">
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-900">
            Automatic Expense Tracker
        </h1>

        <p class="mt-2 text-sm text-gray-600 max-w-prose mx-auto">
            Upload receipts as CSV files — the app auto-populates expense records for fast reconciliation and accurate stock price decimals.
        </p>

        <div class="mt-3 inline-flex items-center gap-2 justify-center">
            <!-- <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100">
                In development
            </span> -->
            <span class="inline-block px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-800 border border-blue-100">
                CSV upload · Auto-populate
            </span>
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
