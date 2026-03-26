<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LifeOS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <script>
            if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased">
        <div class="min-h-screen flex">
            {{-- Left Panel: Branding --}}
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-800 relative overflow-hidden">
                <div class="relative z-10 flex flex-col justify-between p-12 w-full">
                    {{-- Logo --}}
                    <a href="/" class="flex items-center gap-2.5">
                        <div class="w-9 h-9 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                            <span class="text-white font-bold text-base">L</span>
                        </div>
                        <span class="text-xl font-semibold text-white tracking-tight">LifeOS</span>
                    </a>

                    {{-- Center Content --}}
                    <div class="max-w-md">
                        <blockquote class="text-2xl font-bold text-white leading-snug">
                            Organize your life.<br>Level up for real.
                        </blockquote>
                        <p class="mt-4 text-indigo-200 leading-relaxed">
                            Connect your goals, projects, and daily tasks into one system — then watch your progress come alive with XP, stats, and achievements.
                        </p>

                        {{-- Mini Stats Preview --}}
                        <div class="mt-8 space-y-3">
                            @php
                                $previewStats = [
                                    ['name' => 'Focus', 'value' => 72, 'color' => 'bg-indigo-300'],
                                    ['name' => 'Discipline', 'value' => 85, 'color' => 'bg-amber-400'],
                                    ['name' => 'Knowledge', 'value' => 64, 'color' => 'bg-blue-400'],
                                ];
                            @endphp
                            @foreach ($previewStats as $stat)
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-medium text-indigo-200">{{ $stat['name'] }}</span>
                                        <span class="text-xs text-indigo-300">{{ $stat['value'] }}</span>
                                    </div>
                                    <div class="w-full bg-white/10 rounded-full h-1.5">
                                        <div class="{{ $stat['color'] }} h-1.5 rounded-full" style="width: {{ $stat['value'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Bottom --}}
                    <p class="text-sm text-indigo-300">&copy; {{ date('Y') }} LifeOS</p>
                </div>

                {{-- Decorative Elements --}}
                <div class="absolute top-1/4 -right-20 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-1/4 -left-20 w-60 h-60 bg-violet-500/20 rounded-full blur-3xl"></div>
            </div>

            {{-- Right Panel: Form --}}
            <div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-4 sm:px-8 bg-gray-50 dark:bg-gray-950">
                {{-- Mobile Logo (hidden on desktop) --}}
                <div class="mb-8 flex items-center gap-2.5 lg:hidden">
                    <a href="/" class="flex items-center gap-2.5">
                        <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center">
                            <span class="text-white font-bold text-base">L</span>
                        </div>
                        <span class="text-xl font-semibold text-gray-900 dark:text-white tracking-tight">LifeOS</span>
                    </a>
                </div>

                <div class="w-full max-w-md">
                    <div class="bg-white dark:bg-gray-900 px-6 py-8 sm:px-8 shadow-sm border border-gray-200 dark:border-gray-800 rounded-2xl">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
