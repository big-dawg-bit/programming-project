@props(['status'])

@php
    $color = match ($status) {
        'goedgekeurd', 'gevalideerd' => 'green',
        'ingediend' => 'blue',
        'aanpassing', 'aanpassing_gevraagd' => 'yellow',
        'afgekeurd' => 'red',
        default => 'zinc',
    };
    $label = match ($status) {
        'draft' => 'concept',
        'aanpassing_gevraagd' => 'aanpassing',
        default => $status,
    };
@endphp

<flux:badge size="sm" :color="$color">{{ $label }}</flux:badge>
