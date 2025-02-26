<x-guest-layout>
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Crie sua Conta no TFG Fiscal</h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Nome -->
        <div>
            <x-input-label for="name" :value="__('Name')" class="text-gray-700" />
            <x-text-input id="name" class="block mt-1 w-full border border-gray-300 rounded-md p-2" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-600" />
        </div>

        <!-- Username -->
        <div class="mt-4">
            <x-input-label for="username" :value="__('Username')" class="text-gray-700" />
            <x-text-input id="username" class="block mt-1 w-full border border-gray-300 rounded-md p-2" type="text" name="username" :value="old('username')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2 text-red-600" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" class="text-gray-700" />
            <x-text-input id="email" class="block mt-1 w-full border border-gray-300 rounded-md p-2" type="email" name="email" :value="old('email')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600" />
        </div>

        <!-- Senha -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-gray-700" />
            <x-text-input id="password" class="block mt-1 w-full border border-gray-300 rounded-md p-2"
                          type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600" />
        </div>

        <!-- Confirmar Senha -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-700" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full border border-gray-300 rounded-md p-2"
                          type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-600" />
        </div>

        <!-- Ações -->
        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-blue-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <x-primary-button class="ml-4 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
