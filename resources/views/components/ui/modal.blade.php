@props(['name', 'maxWidth' => 'lg'])

@php
$maxWidthClass = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div x-data="{ show: false }"
     x-on:open-modal-{{ $name }}.window="show = true"
     x-on:close-modal-{{ $name }}.window="show = false"
     x-on:keydown.escape.window="show = false"
     x-show="show"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    {{-- Backdrop --}}
    <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/50 dark:bg-black/70" @click="show = false"></div>

    {{-- Modal — bottom sheet on mobile, centered dialog on desktop --}}
    <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-end sm:items-center justify-center p-0 sm:p-4">
            <div x-show="show"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                 class="bg-white dark:bg-gray-900 rounded-t-2xl sm:rounded-xl shadow-xl dark:shadow-black/30 w-full {{ $maxWidthClass }} max-h-[calc(100dvh-1.5rem)] overflow-y-auto pb-[env(safe-area-inset-bottom)] sm:pb-0">
                {{-- Drag handle (mobile only) --}}
                <div class="sm:hidden flex justify-center pt-2.5 pb-1">
                    <div class="h-1 w-9 rounded-full bg-gray-300 dark:bg-gray-700"></div>
                </div>
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
