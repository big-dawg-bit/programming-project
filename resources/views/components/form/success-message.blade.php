@props(['message' => null])

@if ($message)
    <div {{ $attributes->merge(['class' => 'rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-700 dark:bg-green-950 dark:text-green-200']) }}>
        {{ $message }}
    </div>
@endif
