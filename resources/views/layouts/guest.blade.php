<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NGIZAN APPAREL') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink bg-canvas antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-8 sm:pt-0 p-4">
            <div class="mb-6">
                <a href="/" class="font-display font-medium text-3xl tracking-wider text-ink uppercase hover:opacity-80 transition inline-block">
                    NGIZAN
                </a>
            </div>

            <div class="w-full sm:max-w-md bg-white border border-hairline-soft rounded-2xl p-6 sm:p-8 space-y-6">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
