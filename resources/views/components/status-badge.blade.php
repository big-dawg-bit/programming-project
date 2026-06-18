@props(['status'])

@php
    $map = [
        'submitted' => ['label' => 'Ingediend', 'color' => 'blue'],
        'approved' => ['label' => 'Goedgekeurd', 'color' => 'green'],
        'rejected' => ['label' => 'Afgekeurd', 'color' => 'red'],
        'changes_requested' => ['label' => 'Aanpassingen vereist', 'color' => 'amber'],
        'active' => ['label' => 'Actief', 'color' => 'green'],
        'pending' => ['label' => 'In afwachting', 'color' => 'zinc'],
    ];

    $badge = $map[$status] ?? ['label' => ucfirst(str_replace('_', ' ', $status)), 'color' => 'zinc'];
@endphp

<flux:badge :color="$badge['color']" size="sm">{{ $badge['label'] }}</flux:badge>
