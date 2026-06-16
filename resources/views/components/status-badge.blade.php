@props(['status'])

@php
    $color = match ($status) {
        'goedgekeurd', 'gevalideerd', 'approved' => 'green',
        'ingediend', 'submitted' => 'blue',
        'aanpassing', 'aanpassing_gevraagd' => 'yellow',
        'afgekeurd', 'rejected' => 'red',
        'pending' => 'yellow',
        default => 'zinc',
    };
    $label = match ($status) {
        'draft' => 'concept',
        'aanpassing_gevraagd' => 'aanpassing',
        'approved' => 'goedgekeurd',
        'rejected' => 'afgekeurd',
        'pending' => 'in behandeling',
        'submitted' => 'ingediend',
        default => ucfirst($status),
    };
@endphp

<flux:badge size="sm" :color="$color">{{ $label }}</flux:badge>
