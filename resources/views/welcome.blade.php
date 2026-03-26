<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'LifeOS') }} — Level Up Your Life</title>
    <meta name="description" content="A premium productivity platform that connects your Life Areas, Goals, Projects, and Tasks with an RPG-inspired progression system.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        /* Subtle gradient background */
        .hero-gradient {
            background: radial-gradient(ellipse 80% 60% at 50% -20%, rgba(99, 102, 241, 0.08) 0%, transparent 70%);
        }

        /* Animated stat bars */
        @keyframes stat-fill {
            from { width: 0; }
        }
        .animate-stat-fill {
            animation: stat-fill 1.5s ease-out forwards;
        }

        /* Floating animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
        .animate-float-delayed {
            animation: float 4s ease-in-out 1s infinite;
        }

        /* Glow effect for CTA */
        .glow-indigo {
            box-shadow: 0 0 24px -4px rgba(99, 102, 241, 0.35);
        }

        /* Rank badge shimmer */
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .shimmer {
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.15) 50%, transparent 100%);
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased bg-white" x-data="{ mobileMenu: false }">

    {{-- ============================================= --}}
    {{-- NAVIGATION --}}
    {{-- ============================================= --}}
    <nav class="fixed top-0 inset-x-0 z-50 bg-white/80 backdrop-blur-xl border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="/" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-base">L</span>
                    </div>
                    <span class="text-xl font-semibold text-gray-900 tracking-tight">LifeOS</span>
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-sm text-gray-500 hover:text-gray-900 transition">Features</a>
                    <a href="#progression" class="text-sm text-gray-500 hover:text-gray-900 transition">Progression</a>
                    <a href="#archetypes" class="text-sm text-gray-500 hover:text-gray-900 transition">Archetypes</a>
                </div>

                {{-- Auth Buttons --}}
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition px-3 py-2">
                            Sign in
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                            Get Started
                        </a>
                    @endauth
                </div>

                {{-- Mobile menu toggle --}}
                <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenu" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenu" x-cloak x-transition class="md:hidden border-t border-gray-100 bg-white">
            <div class="px-4 py-4 space-y-3">
                <a href="#features" class="block text-sm text-gray-600 hover:text-gray-900">Features</a>
                <a href="#progression" class="block text-sm text-gray-600 hover:text-gray-900">Progression</a>
                <a href="#archetypes" class="block text-sm text-gray-600 hover:text-gray-900">Archetypes</a>
                <div class="pt-3 border-t border-gray-100 flex gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg">Sign in</a>
                        <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ============================================= --}}
    {{-- HERO SECTION --}}
    {{-- ============================================= --}}
    <section class="hero-gradient pt-32 pb-20 lg:pt-40 lg:pb-28">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 mb-8">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                    <span class="text-xs font-medium text-indigo-700">Your personal growth operating system</span>
                </div>

                {{-- Headline --}}
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight leading-[1.1]">
                    Organize your life.
                    <span class="block mt-2 bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">Level up for real.</span>
                </h1>

                {{-- Subheadline --}}
                <p class="mt-6 text-lg sm:text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed">
                    LifeOS connects your goals, projects, and daily tasks into one system — then rewards your progress with XP, stats, ranks, and achievements.
                </p>

                {{-- CTA --}}
                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition shadow-lg glow-indigo">
                            Go to Dashboard
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition shadow-lg glow-indigo">
                            Start Your Journey
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 text-base font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition">
                            Sign in
                        </a>
                    @endauth
                </div>

                {{-- Quick stats --}}
                <p class="mt-8 text-sm text-gray-400">Free to use. No credit card required.</p>
            </div>

            {{-- Hero Visual: Dashboard Preview --}}
            <div class="mt-16 lg:mt-20 relative">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-2xl shadow-gray-200/60 overflow-hidden">
                    {{-- Mock Browser Bar --}}
                    <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                        <div class="flex gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-gray-200"></div>
                            <div class="w-3 h-3 rounded-full bg-gray-200"></div>
                            <div class="w-3 h-3 rounded-full bg-gray-200"></div>
                        </div>
                        <div class="flex-1 mx-8">
                            <div class="bg-white rounded-md border border-gray-200 px-3 py-1.5 text-xs text-gray-400 text-center">lifeos.app/dashboard</div>
                        </div>
                    </div>

                    {{-- Mock Dashboard Content --}}
                    <div class="p-6 sm:p-8 bg-gray-50">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            {{-- XP Card --}}
                            <div class="bg-white rounded-xl border border-gray-200 p-4 animate-float">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Level</span>
                                    <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Expert</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">24</p>
                                <div class="mt-2 w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-indigo-600 h-1.5 rounded-full animate-stat-fill" style="width: 68%"></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">1,340 / 2,000 XP</p>
                            </div>

                            {{-- Tasks Card --}}
                            <div class="bg-white rounded-xl border border-gray-200 p-4 animate-float-delayed">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Today's Tasks</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">5 <span class="text-base font-normal text-gray-400">/ 8</span></p>
                                <div class="mt-2 w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-emerald-500 h-1.5 rounded-full animate-stat-fill" style="width: 62%"></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">3 remaining</p>
                            </div>

                            {{-- Goals Card --}}
                            <div class="bg-white rounded-xl border border-gray-200 p-4 animate-float">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Active Goals</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">4</p>
                                <div class="mt-3 flex gap-1">
                                    <div class="flex-1 h-1 rounded-full bg-indigo-500"></div>
                                    <div class="flex-1 h-1 rounded-full bg-emerald-500"></div>
                                    <div class="flex-1 h-1 rounded-full bg-amber-500"></div>
                                    <div class="flex-1 h-1 rounded-full bg-rose-400"></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Across 3 life areas</p>
                            </div>

                            {{-- Streak Card --}}
                            <div class="bg-white rounded-xl border border-gray-200 p-4 animate-float-delayed">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Streak</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">12 <span class="text-base font-normal text-gray-400">days</span></p>
                                <div class="mt-2 flex gap-0.5">
                                    @for ($i = 0; $i < 7; $i++)
                                        <div class="flex-1 h-5 rounded-sm {{ $i < 5 ? 'bg-indigo-500' : 'bg-gray-100' }}"></div>
                                    @endfor
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Best: 21 days</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Decorative gradient behind --}}
                <div class="absolute -inset-4 -z-10 bg-gradient-to-b from-indigo-50/50 to-transparent rounded-3xl blur-xl"></div>
            </div>
        </div>
    </section>

    {{-- ============================================= --}}
    {{-- FEATURES SECTION --}}
    {{-- ============================================= --}}
    <section id="features" class="py-20 lg:py-28 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">Everything connects</h2>
                <p class="mt-4 text-lg text-gray-500">A structured hierarchy that turns big ambitions into daily action — without the chaos of scattered tools.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                {{-- Feature 1: Life Areas --}}
                <div class="group p-6 rounded-2xl border border-gray-200 hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-50 transition-all duration-300">
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center mb-5">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Life Areas</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Define the pillars of your life — Career, Health, Learning, Finance. Every goal and task maps back to what matters most.</p>
                </div>

                {{-- Feature 2: Goals & Projects --}}
                <div class="group p-6 rounded-2xl border border-gray-200 hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-50 transition-all duration-300">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center mb-5">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Goals & Projects</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Break down ambitions into goals, organize work into projects, and track progress with clear milestones and deadlines.</p>
                </div>

                {{-- Feature 3: Tasks --}}
                <div class="group p-6 rounded-2xl border border-gray-200 hover:border-amber-200 hover:shadow-lg hover:shadow-amber-50 transition-all duration-300">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center mb-5">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Smart Tasks</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Tasks with effort sizing, priorities, subtasks, and due dates. Standalone or linked to goals — your workflow, your rules.</p>
                </div>
            </div>

            {{-- Hierarchy Visual --}}
            <div class="mt-16 p-6 sm:p-8 bg-gray-50 rounded-2xl border border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 text-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-900">Life Areas</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <svg class="w-5 h-5 text-gray-300 sm:hidden rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-900">Goals</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <svg class="w-5 h-5 text-gray-300 sm:hidden rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-900">Projects</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <svg class="w-5 h-5 text-gray-300 sm:hidden rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-900">Tasks</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================= --}}
    {{-- PROGRESSION SECTION --}}
    {{-- ============================================= --}}
    <section id="progression" class="py-20 lg:py-28 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                {{-- Left: Content --}}
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-violet-50 border border-violet-100 mb-6">
                        <span class="text-xs font-medium text-violet-700">RPG-Inspired</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">Every task completed<br>makes you stronger</h2>
                    <p class="mt-5 text-lg text-gray-500 leading-relaxed">Complete tasks to earn XP, raise your stats, climb the ranks, and unlock achievements. Your real-life effort translates into visible growth.</p>

                    <div class="mt-8 space-y-5">
                        {{-- XP System --}}
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Experience Points</h3>
                                <p class="text-sm text-gray-500 mt-0.5">Earn 5-30 XP per task based on effort. Level up and watch your character grow.</p>
                            </div>
                        </div>

                        {{-- Stats --}}
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">8 Character Stats</h3>
                                <p class="text-sm text-gray-500 mt-0.5">Discipline, Focus, Knowledge, Strength, Wealth, Creativity, Influence, Wisdom.</p>
                            </div>
                        </div>

                        {{-- Ranks --}}
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Ranks & Achievements</h3>
                                <p class="text-sm text-gray-500 mt-0.5">Rise from Initiate to Legend. Unlock achievements for milestones and consistency.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Stats Visual --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6 sm:p-8">
                    {{-- Profile Header --}}
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center">
                            <span class="text-xl font-bold text-white">E</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Eddie</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Expert</span>
                                <span class="text-xs text-gray-400">Level 24</span>
                            </div>
                        </div>
                    </div>

                    {{-- Stat Bars --}}
                    <div class="space-y-3">
                        @php
                            $demoStats = [
                                ['name' => 'Discipline', 'value' => 78, 'color' => 'bg-amber-500'],
                                ['name' => 'Focus', 'value' => 65, 'color' => 'bg-indigo-500'],
                                ['name' => 'Knowledge', 'value' => 82, 'color' => 'bg-blue-500'],
                                ['name' => 'Strength', 'value' => 45, 'color' => 'bg-red-500'],
                                ['name' => 'Wealth', 'value' => 58, 'color' => 'bg-emerald-500'],
                                ['name' => 'Creativity', 'value' => 71, 'color' => 'bg-pink-500'],
                                ['name' => 'Influence', 'value' => 39, 'color' => 'bg-purple-500'],
                                ['name' => 'Wisdom', 'value' => 60, 'color' => 'bg-teal-500'],
                            ];
                        @endphp
                        @foreach ($demoStats as $stat)
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-medium text-gray-600">{{ $stat['name'] }}</span>
                                    <span class="text-xs text-gray-400">{{ $stat['value'] }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="{{ $stat['color'] }} h-2 rounded-full animate-stat-fill" style="width: {{ $stat['value'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Achievement Badges --}}
                    <div class="mt-6 pt-5 border-t border-gray-100">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-3">Recent Achievements</p>
                        <div class="flex gap-2 flex-wrap">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 border border-amber-200 text-xs font-medium text-amber-700">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z"/></svg>
                                First Goal Set
                            </div>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-indigo-50 border border-indigo-200 text-xs font-medium text-indigo-700">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/></svg>
                                7-Day Streak
                            </div>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-xs font-medium text-emerald-700">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                100 Tasks Done
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================= --}}
    {{-- ARCHETYPES SECTION --}}
    {{-- ============================================= --}}
    <section id="archetypes" class="py-20 lg:py-28 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">Choose your path</h2>
                <p class="mt-4 text-lg text-gray-500">Start with an archetype that matches your focus. Each one pre-configures your life areas and starter goals.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                {{-- Student --}}
                <div class="group p-5 rounded-2xl border border-gray-200 hover:border-blue-200 hover:shadow-md transition-all duration-300 text-center">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-blue-50 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">Student</h3>
                    <p class="text-xs text-gray-500 mt-1">Academic growth & learning</p>
                </div>

                {{-- Researcher --}}
                <div class="group p-5 rounded-2xl border border-gray-200 hover:border-purple-200 hover:shadow-md transition-all duration-300 text-center">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-purple-50 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">Researcher</h3>
                    <p class="text-xs text-gray-500 mt-1">Deep work & discovery</p>
                </div>

                {{-- Founder --}}
                <div class="group p-5 rounded-2xl border border-gray-200 hover:border-amber-200 hover:shadow-md transition-all duration-300 text-center">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-amber-50 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">Founder</h3>
                    <p class="text-xs text-gray-500 mt-1">Building & shipping</p>
                </div>

                {{-- Professional --}}
                <div class="group p-5 rounded-2xl border border-gray-200 hover:border-emerald-200 hover:shadow-md transition-all duration-300 text-center">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-emerald-50 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">Professional</h3>
                    <p class="text-xs text-gray-500 mt-1">Career advancement</p>
                </div>

                {{-- Creator --}}
                <div class="group p-5 rounded-2xl border border-gray-200 hover:border-rose-200 hover:shadow-md transition-all duration-300 text-center">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-rose-50 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">Creator</h3>
                    <p class="text-xs text-gray-500 mt-1">Art, writing & creation</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================= --}}
    {{-- REVIEWS SECTION --}}
    {{-- ============================================= --}}
    <section class="py-20 lg:py-28 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                {{-- Visual --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6 sm:p-8 order-2 lg:order-1">
                    {{-- Daily Review Mock --}}
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Daily Review</h3>
                            <p class="text-xs text-gray-400">Wednesday, Mar 26</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-emerald-50 border border-emerald-100">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-sm text-gray-700">Complete API documentation</span>
                            <span class="ml-auto text-xs font-medium text-emerald-600">+15 XP</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-emerald-50 border border-emerald-100">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-sm text-gray-700">30-minute workout</span>
                            <span class="ml-auto text-xs font-medium text-emerald-600">+30 XP</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 border border-gray-100">
                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex-shrink-0"></div>
                            <span class="text-sm text-gray-500">Read 20 pages</span>
                            <span class="ml-auto text-xs text-gray-400">+5 XP</span>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs text-gray-400">Today's XP earned</span>
                        <span class="text-sm font-semibold text-indigo-600">+45 XP</span>
                    </div>
                </div>

                {{-- Content --}}
                <div class="order-1 lg:order-2">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">Reflect, review,<br>and grow</h2>
                    <p class="mt-5 text-lg text-gray-500 leading-relaxed">Daily and weekly reviews help you stay aligned with your goals. See what you accomplished, earn your XP, and plan what's next.</p>

                    <div class="mt-8 space-y-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-indigo-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-sm text-gray-600">Daily reviews to check off completed tasks</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-indigo-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-sm text-gray-600">Weekly reviews to reflect on progress and plan ahead</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-indigo-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-sm text-gray-600">No guilt — missed days don't penalize you</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================= --}}
    {{-- FINAL CTA --}}
    {{-- ============================================= --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">Ready to level up?</h2>
            <p class="mt-4 text-lg text-gray-500">Join LifeOS and start turning your daily actions into real character growth.</p>
            <div class="mt-10">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-8 py-3.5 text-base font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition shadow-lg glow-indigo">
                        Go to Dashboard
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3.5 text-base font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition shadow-lg glow-indigo">
                        Start Your Journey — It's Free
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @endauth
            </div>
            <p class="mt-4 text-sm text-gray-400">No credit card required. Set up in 2 minutes.</p>
        </div>
    </section>

    {{-- ============================================= --}}
    {{-- FOOTER --}}
    {{-- ============================================= --}}
    <footer class="border-t border-gray-200 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xs">L</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">LifeOS</span>
                </div>
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} LifeOS. Built for ambitious people.</p>
            </div>
        </div>
    </footer>

</body>
</html>
