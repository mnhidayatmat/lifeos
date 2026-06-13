<div class="space-y-4">
    {{-- Name --}}
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
        <input type="text" name="name" id="name" value="{{ old('name', $area?->name) }}"
               class="w-full rounded-lg border-gray-300 text-sm focus:border-teal-500 focus:ring-teal-500" required>
        @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Color --}}
    <div>
        <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
        <div class="flex items-center gap-2">
            <input type="color" name="color" id="color" value="{{ old('color', $area?->color ?? '#14b8a6') }}"
                   class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer p-0.5">
            <span class="text-xs text-gray-500">Pick a color for this area</span>
        </div>
    </div>
</div>
