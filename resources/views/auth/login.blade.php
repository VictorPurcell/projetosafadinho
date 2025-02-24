<x-guest-layout>
    <!-- Status da Sessão -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Acesso TFG Fiscal</h1>

    <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
        @csrf

        <!-- Campo Username -->
        <div>
            <x-input-label for="username" :value="__('Username')" class="text-gray-700" />
            <x-text-input id="username" class="block mt-1 w-full border border-gray-300 rounded-md p-2"
                          type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2 text-red-600" />
        </div>

        <!-- Campo Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-gray-700" />
            <x-text-input id="password" class="block mt-1 w-full border border-gray-300 rounded-md p-2"
                          type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600" />
        </div>

        <!-- Lembre-me -->
        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center text-gray-700">
                <input id="remember_me" type="checkbox"
                       class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                       name="remember">
                <span class="ml-2 text-sm">{{ __('Remember me') }}</span>
            </label>
        </div>

        <!-- Ações -->
        <div class="flex items-center justify-between">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                   href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Link para Registro -->
    @if (Route::has('register'))
        <div class="mt-6 text-center">
            <span class="text-gray-600 text-sm">
                {{ __("Don't have an account?") }}
            </span>
            <a class="underline text-sm text-blue-600 hover:text-blue-800 ml-1" href="{{ route('register') }}">
                {{ __('Register here') }}
            </a>
        </div>
    @endif
</x-guest-layout>
