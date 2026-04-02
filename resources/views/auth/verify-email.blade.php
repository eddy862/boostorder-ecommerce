<x-guest-layout title="Verify Email">
    <div class="mb-6">
        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-orange-400">{{ __('Almost there') }}</p>
        <h2 class="mt-2 text-3xl font-black text-gray-800">{{ __('Verify your email address') }}</h2>
        <p class="mt-2 text-sm leading-6 text-gray-500">
            {{ __('We sent a verification link to your email. Please open it before you continue shopping and checking out.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="rounded-2xl border border-orange-100 bg-orange-50 p-5">
        <p class="text-sm leading-6 text-gray-600">
            {{ __('Didn\'t get the email? You can request another verification link, or log out and use a different account.') }}
        </p>
    </div>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button>
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm font-semibold text-orange-500 hover:text-orange-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-400">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
