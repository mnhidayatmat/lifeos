{{-- Mobile bottom tab bar — primary navigation in the thumb zone.
     Hidden on lg+ where the fixed sidebar handles navigation. --}}
<nav x-show="!sidebarOpen"
     class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur border-t border-gray-200 dark:border-gray-800"
     style="padding-bottom: env(safe-area-inset-bottom);">
    <div class="grid grid-cols-5">
        @php
            $tabs = [
                ['route' => 'dashboard',       'active' => 'dashboard',   'icon' => 'home',         'label' => 'Home'],
                ['route' => 'tasks.index',     'active' => 'tasks.*',     'icon' => 'check-square', 'label' => 'Tasks'],
                ['route' => 'goals.index',     'active' => 'goals.*',     'icon' => 'target',       'label' => 'Goals'],
                ['route' => 'projects.index',  'active' => 'projects.*',  'icon' => 'folder',       'label' => 'Projects'],
            ];
        @endphp
        @foreach($tabs as $tab)
            @php $isActive = request()->routeIs($tab['active']); @endphp
            <a href="{{ route($tab['route']) }}" aria-label="{{ $tab['label'] }}"
               class="flex flex-col items-center justify-center gap-0.5 h-16 transition-colors {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 active:text-gray-600 dark:active:text-gray-300' }}">
                <x-icon :name="$tab['icon']" class="w-6 h-6" />
                <span class="text-[10px] font-medium leading-none">{{ $tab['label'] }}</span>
            </a>
        @endforeach

        {{-- More → opens the full navigation drawer --}}
        <button type="button" @click="sidebarOpen = true" aria-label="More"
                class="flex flex-col items-center justify-center gap-0.5 h-16 text-gray-400 dark:text-gray-500 active:text-gray-600 dark:active:text-gray-300 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/>
            </svg>
            <span class="text-[10px] font-medium leading-none">More</span>
        </button>
    </div>
</nav>
