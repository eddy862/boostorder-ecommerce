<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? config('app.name', 'Laravel') . ' - ' . $title : config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-orange-50">
            <div class="mx-auto flex min-h-screen max-w-6xl items-center px-4 py-10 sm:px-6 lg:grid lg:grid-cols-[1.1fr_520px] lg:gap-10">
                <div class="hidden lg:block">
                    <a href="/" class="inline-flex items-center gap-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-400 shadow-lg shadow-orange-200/70">
                            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-orange-400 text-white shadow-sm">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M2.25 2.25a.75.75 0 0 0 0 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 0 0-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 0 0 0-1.5H5.378A2.25 2.25 0 0 1 7.5 15h11.218a.75.75 0 0 0 .674-.421 60.358 60.358 0 0 0 2.96-7.228.75.75 0 0 0-.525-.965A60.864 60.864 0 0 0 5.68 4.509l-.232-.867A1.875 1.875 0 0 0 3.636 2.25H2.25ZM3.75 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0ZM16.5 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z" />
                                </svg>
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-orange-400">Shop Smart</p>
                            <h1 class="text-4xl font-black text-gray-800">{{ config('app.name', 'Laravel') }}</h1>
                        </div>
                    </a>

                    <div class="mt-10 max-w-xl rounded-[2rem] border border-orange-100 bg-white/90 p-10 shadow-xl shadow-orange-100">
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-orange-400">Fresh picks every day</p>
                        <h2 class="mt-4 text-5xl font-black leading-tight text-gray-800">Cart-ready shopping with a bright, familiar marketplace feel.</h2>
                        <p class="mt-5 text-lg leading-8 text-gray-500">
                            Browse products, buy in a few clicks, and manage your orders in one place with the same warm storefront style across the whole app.
                        </p>

                        <div class="mt-8 grid grid-cols-3 gap-4">
                            <div class="rounded-2xl bg-orange-50 px-4 py-5 text-center">
                                <p class="text-2xl font-black text-orange-500">Fast</p>
                                <p class="mt-1 text-sm text-gray-500">Checkout flow</p>
                            </div>
                            <div class="rounded-2xl bg-orange-50 px-4 py-5 text-center">
                                <p class="text-2xl font-black text-orange-500">Easy</p>
                                <p class="mt-1 text-sm text-gray-500">Order tracking</p>
                            </div>
                            <div class="rounded-2xl bg-orange-50 px-4 py-5 text-center">
                                <p class="text-2xl font-black text-orange-500">Secure</p>
                                <p class="mt-1 text-sm text-gray-500">Account access</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full">
                    <div class="mx-auto w-full max-w-md rounded-[2rem] border border-orange-100 bg-white px-6 py-8 shadow-xl shadow-orange-100 sm:px-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
