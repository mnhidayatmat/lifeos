@php $stats = \App\Models\User::STATS; @endphp

<div class="space-y-4">
    {{-- Name --}}
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
        <input type="text" name="name" id="name" value="{{ old('name', $area?->name) }}"
               class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Color --}}
    <div>
        <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
        <div class="flex items-center gap-2">
            <input type="color" name="color" id="color" value="{{ old('color', $area?->color ?? '#6366f1') }}"
                   class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer p-0.5">
            <span class="text-xs text-gray-500">Pick a color for this area</span>
        </div>
    </div>

    {{-- Primary Stat --}}
    <div>
        <label for="primary_stat" class="block text-sm font-medium text-gray-700 mb-1">Primary Stat (70% XP)</label>
        <select name="primary_stat" id="primary_stat"
                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            @foreach($stats as $stat)
                <option value="{{ $stat }}" @selected(old('primary_stat', $area?->primary_stat) === $stat)>
                    {{ ucfirst($stat) }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Secondary Stat --}}
    <div>
        <label for="secondary_stat" class="block text-sm font-medium text-gray-700 mb-1">Secondary Stat (30% XP)</label>
        <select name="secondary_stat" id="secondary_stat"
                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            @foreach($stats as $stat)
                <option value="{{ $stat }}" @selected(old('secondary_stat', $area?->secondary_stat) === $stat)>
                    {{ ucfirst($stat) }}
                </option>
            @endforeach
        </select>
    </div>
</div>
