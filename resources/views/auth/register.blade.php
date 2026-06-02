<x-guest-layout>
    <h1 class="text-xl font-bold text-slate-800 text-center mb-1">Create an Account</h1>
    <p class="text-sm text-slate-500 text-center mb-7">Join us to manage your finances</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="John Doe"
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                          focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                          placeholder:text-slate-400 transition-all">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                   placeholder="you@example.com"
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                          focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                          placeholder:text-slate-400 transition-all">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
            <div class="relative" x-data="{ show: false }">
                <input :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password"
                       placeholder="••••••••"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                              focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                              placeholder:text-slate-400 transition-all pr-11">
                <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm Password</label>
            <div class="relative" x-data="{ show: false }">
                <input :type="show ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                       placeholder="••••••••"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                              focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                              placeholder:text-slate-400 transition-all pr-11">
                <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit"
                class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold
                       py-2.5 px-4 rounded-xl transition-all duration-150 shadow-sm
                       hover:shadow-md active:scale-[0.98] text-sm mt-2">
            Create Account
        </button>
    </form>

    <p class="text-center text-sm text-slate-500 mt-6">
        Already registered? <a href="{{ route('login') }}" class="text-brand-600 font-semibold hover:underline">Sign in</a>
    </p>
</x-guest-layout>
