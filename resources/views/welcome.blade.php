<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'LifeOS') }} — Organize your life, in one place</title>
    <meta name="description" content="A clean productivity platform that connects your life areas, goals, projects, and tasks — with reviews and analytics to keep you on track.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @include('layouts.pwa-head')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        /* Subtle gradient background */
        .hero-gradient {
            background: radial-gradient(ellipse 80% 60% at 50% -20%, rgba(20, 184, 166, 0.08) 0%, transparent 70%);
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
        .glow-teal {
            box-shadow: 0 0 24px -4px rgba(20, 184, 166, 0.35);
        }

        /* Subtle shimmer accent */
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
                    <img src="{{ asset('icons/icon-192.png') }}?v=2" alt="LifeOS logo" class="w-9 h-9 rounded-xl shadow-sm">
                    <span class="text-xl font-semibold text-gray-900 tracking-tight">LifeOS</span>
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-sm text-gray-500 hover:text-gray-900 transition">Features</a>
                    <a href="#insights" class="text-sm text-gray-500 hover:text-gray-900 transition">Insights</a>
                    <a href="#templates" class="text-sm text-gray-500 hover:text-gray-900 transition">Templates</a>
                </div>

                {{-- Auth Buttons --}}
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 transition shadow-sm">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition px-3 py-2">
                            Sign in
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 transition shadow-sm">
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
                <a href="#insights" class="block text-sm text-gray-600 hover:text-gray-900">Insights</a>
                <a href="#templates" class="block text-sm text-gray-600 hover:text-gray-900">Templates</a>
                <div class="pt-3 border-t border-gray-100 flex gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-white bg-teal-600 rounded-lg">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg">Sign in</a>
                        <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-white bg-teal-600 rounded-lg">Get Started</a>
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
                {{-- Logo --}}
                <img src="{{ asset('icons/icon-192.png') }}?v=2" alt="LifeOS" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl mx-auto mb-6 shadow-lg glow-teal">

                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-teal-50 border border-teal-100 mb-8">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                    <span class="text-xs font-medium text-teal-700">Your personal growth operating system</span>
                </div>

                {{-- Headline --}}
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight leading-[1.1]">
                    Organize your life.
                    <span class="block mt-2 bg-gradient-to-r from-teal-600 to-violet-600 bg-clip-text text-transparent">See real progress.</span>
                </h1>

                {{-- Subheadline --}}
                <p class="mt-6 text-lg sm:text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed">
                    LifeOS connects your life areas, goals, projects, and daily tasks into one clear system — with reviews and analytics that show real progress.
                </p>

                {{-- CTA --}}
                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold text-white bg-teal-600 rounded-xl hover:bg-teal-700 transition shadow-lg glow-teal">
                            Go to Dashboard
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold text-white bg-teal-600 rounded-xl hover:bg-teal-700 transition shadow-lg glow-teal">
                            Get started free
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
                            {{-- This Week Card --}}
                            <div class="bg-white rounded-xl border border-gray-200 p-4 animate-float">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">This Week</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">28</p>
                                <div class="mt-2 w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-teal-600 h-1.5 rounded-full animate-stat-fill" style="width: 75%"></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">tasks completed</p>
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
                                    <div class="flex-1 h-1 rounded-full bg-teal-500"></div>
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
                                        <div class="flex-1 h-5 rounded-sm {{ $i < 5 ? 'bg-teal-500' : 'bg-gray-100' }}"></div>
                                    @endfor
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Best: 21 days</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Decorative gradient behind --}}
                <div class="absolute -inset-4 -z-10 bg-gradient-to-b from-teal-50/50 to-transparent rounded-3xl blur-xl"></div>
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
                <div class="group p-6 rounded-2xl border border-gray-200 hover:border-teal-200 hover:shadow-lg hover:shadow-teal-50 transition-all duration-300">
                    <div class="w-11 h-11 rounded-xl bg-teal-50 flex items-center justify-center mb-5">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
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
    {{-- INSIGHTS SECTION --}}
    {{-- ============================================= --}}
    <section id="insights" class="py-20 lg:py-28 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                {{-- Left: Content --}}
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-teal-50 border border-teal-100 mb-6">
                        <span class="text-xs font-medium text-teal-700">Insights</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">Know exactly where<br>your time goes</h2>
                    <p class="mt-5 text-lg text-gray-500 leading-relaxed">Every completed task rolls up into clear analytics — completion trends, life-area balance, and consistency — so you can see what's working and adjust.</p>

                    <div class="mt-8 space-y-5">
                        {{-- Completion trends --}}
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 14l4-4 3 3 5-6"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Completion trends</h3>
                                <p class="text-sm text-gray-500 mt-0.5">See tasks completed over time and your weekly completion rate at a glance.</p>
                            </div>
                        </div>

                        {{-- Life-area balance --}}
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Life-area balance</h3>
                                <p class="text-sm text-gray-500 mt-0.5">Make sure no part of your life is being neglected — work, health, learning, and more.</p>
                            </div>
                        </div>

                        {{-- Milestones --}}
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Milestones</h3>
                                <p class="text-sm text-gray-500 mt-0.5">Mark meaningful moments like your first completed goal or 100 tasks done.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Analytics Visual --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6 sm:p-8">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-semibold text-gray-900">This month</h3>
                        <span class="text-xs text-gray-400">Tasks by life area</span>
                    </div>

                    {{-- Life-area bars --}}
                    <div class="space-y-4">
                        @php
                            $demoAreas = [
                                ['name' => 'Work', 'count' => 42, 'width' => 92, 'color' => 'bg-teal-500'],
                                ['name' => 'Health', 'count' => 31, 'width' => 68, 'color' => 'bg-emerald-500'],
                                ['name' => 'Learning', 'count' => 24, 'width' => 53, 'color' => 'bg-blue-500'],
                                ['name' => 'Finance', 'count' => 12, 'width' => 26, 'color' => 'bg-amber-500'],
                            ];
                        @endphp
                        @foreach ($demoAreas as $area)
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-medium text-gray-600">{{ $area['name'] }}</span>
                                    <span class="text-xs text-gray-400">{{ $area['count'] }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="{{ $area['color'] }} h-2 rounded-full animate-stat-fill" style="width: {{ $area['width'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Milestones row --}}
                    <div class="mt-6 pt-5 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-sm text-gray-500">Milestones reached</span>
                        <span class="text-sm font-semibold text-teal-600">6 / 8</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================= --}}
    {{-- TEMPLATES SECTION --}}
    {{-- ============================================= --}}
    <section id="templates" class="py-20 lg:py-28 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">Start with a template</h2>
                <p class="mt-4 text-lg text-gray-500">Pick a starting template that matches your focus. Each one pre-configures your life areas and a starter goal — change anything later.</p>
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
                        <div class="w-9 h-9 rounded-lg bg-teal-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
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
                            <span class="ml-auto text-xs font-medium text-gray-400">Medium</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-emerald-50 border border-emerald-100">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-sm text-gray-700">30-minute workout</span>
                            <span class="ml-auto text-xs font-medium text-gray-400">Large</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 border border-gray-100">
                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex-shrink-0"></div>
                            <span class="text-sm text-gray-500">Read 20 pages</span>
                            <span class="ml-auto text-xs text-gray-400">Small</span>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs text-gray-400">Completed today</span>
                        <span class="text-sm font-semibold text-teal-600">2 of 3</span>
                    </div>
                </div>

                {{-- Content --}}
                <div class="order-1 lg:order-2">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">Reflect, review,<br>and grow</h2>
                    <p class="mt-5 text-lg text-gray-500 leading-relaxed">Daily and weekly reviews help you stay aligned with your goals. See what you accomplished, reflect on what mattered, and plan what's next.</p>

                    <div class="mt-8 space-y-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-teal-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-sm text-gray-600">Daily reviews to check off completed tasks</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-teal-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-sm text-gray-600">Weekly reviews to reflect on progress and plan ahead</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-teal-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
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
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">Ready to get organized?</h2>
            <p class="mt-4 text-lg text-gray-500">Bring your goals, projects, and tasks into one place — and finally see them through.</p>
            <div class="mt-10">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-8 py-3.5 text-base font-semibold text-white bg-teal-600 rounded-xl hover:bg-teal-700 transition shadow-lg glow-teal">
                        Go to Dashboard
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3.5 text-base font-semibold text-white bg-teal-600 rounded-xl hover:bg-teal-700 transition shadow-lg glow-teal">
                        Get started — it's free
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
                    <img src="{{ asset('icons/icon-192.png') }}?v=2" alt="LifeOS logo" class="w-7 h-7 rounded-lg">
                    <span class="text-sm font-semibold text-gray-900">LifeOS</span>
                </div>
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} LifeOS. Built for ambitious people.</p>
            </div>
        </div>
    </footer>

</body>
</html>
