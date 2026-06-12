@props(['message' => null])

@if ($message)
    <div {{ $attributes->merge(['class' => 'rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-700 dark:bg-red-950 dark:text-red-200']) }}>
        {{ $message }}
    </div>
@endif
