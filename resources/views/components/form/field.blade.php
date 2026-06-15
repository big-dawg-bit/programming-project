@props(['label' => null, 'name', 'type' => 'text'])

<div class="space-y-1">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
            {{ $label }}
        </label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $name }}"
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-red-500 focus:ring-red-500 dark:border-neutral-700 dark:bg-neutral-800']) }}
    />

    @error($name)
    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
