{{-- Desktop Sidebar --}}
<aside class="fixed inset-y-0 left-0 z-40 w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col transition-transform duration-200 ease-in-out"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

    {{-- Logo --}}
    <div class="h-16 flex items-center px-6 border-b border-gray-100 dark:border-gray-800">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">L</span>
            </div>
            <span class="text-lg font-semibold text-gray-900 dark:text-white tracking-tight">LifeOS</span>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3">
        <div class="space-y-1">
            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                Dashboard
            </x-sidebar-link>

            <x-sidebar-link :href="route('life-areas.index')" :active="request()->routeIs('life-areas.*')" icon="grid">
                Life Areas
            </x-sidebar-link>

            <x-sidebar-link :href="route('goals.index')" :active="request()->routeIs('goals.*')" icon="target">
                Goals
            </x-sidebar-link>

            <x-sidebar-link :href="route('projects.index')" :active="request()->routeIs('projects.*')" icon="folder">
                Projects
            </x-sidebar-link>

            <x-sidebar-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')" icon="check-square">
                Tasks
            </x-sidebar-link>

            <x-sidebar-link :href="route('habits.index')" :active="request()->routeIs('habits.*')" icon="target">
                Habits
            </x-sidebar-link>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-800">
            <p class="px-3 mb-2 text-xs font-medium text-gray-400 dark:text-gray-600 uppercase tracking-wider">Growth</p>
            <div class="space-y-1">
                <x-sidebar-link :href="route('reviews.daily')" :active="request()->routeIs('reviews.*')" icon="book-open">
                    Reviews
                </x-sidebar-link>

                <x-sidebar-link :href="route('vision.index')" :active="request()->routeIs('vision.*')" icon="user">
                    Vision
                </x-sidebar-link>

                <x-sidebar-link :href="route('resources.index')" :active="request()->routeIs('resources.*')" icon="book-open">
                    Library
                </x-sidebar-link>

                <x-sidebar-link :href="route('analytics.index')" :active="request()->routeIs('analytics.*')" icon="grid">
                    Analytics
                </x-sidebar-link>

                <x-sidebar-link :href="route('progression.index')" :active="request()->routeIs('progression.*')" icon="trophy">
                    Profile
                </x-sidebar-link>
            </div>
        </div>
    </nav>

    {{-- User Section --}}
    <div class="border-t border-gray-100 dark:border-gray-800 p-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center">
                <span class="text-sm font-medium text-indigo-700 dark:text-indigo-400">{{ substr(Auth::user()->name, 0, 1) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Level {{ Auth::user()->level ?? 1 }}</p>
            </div>
            <x-ui.theme-toggle class="!p-1.5" />
        </div>
    </div>
</aside>
