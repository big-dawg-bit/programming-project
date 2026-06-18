@props(['status'])

@php
    $map = [
        // Engelse statussen
        'submitted' => ['label' => 'Ingediend', 'color' => 'blue'],
        'approved' => ['label' => 'Goedgekeurd', 'color' => 'green'],
        'rejected' => ['label' => 'Afgekeurd', 'color' => 'red'],
        'changes_requested' => ['label' => 'Aanpassingen vereist', 'color' => 'amber'],
        'active' => ['label' => 'Actief', 'color' => 'green'],
        'pending' => ['label' => 'In afwachting', 'color' => 'zinc'],
        // Nederlandse statussen
        'ingediend' => ['label' => 'Ingediend', 'color' => 'blue'],
        'goedgekeurd' => ['label' => 'Goedgekeurd', 'color' => 'green'],
        'gevalideerd' => ['label' => 'Goedgekeurd', 'color' => 'green'],
        'afgekeurd' => ['label' => 'Afgekeurd', 'color' => 'red'],
        'aanpassing' => ['label' => 'Aanpassing', 'color' => 'amber'],
        'aanpassing_gevraagd' => ['label' => 'Aanpassing', 'color' => 'amber'],
        'concept' => ['label' => 'Concept', 'color' => 'zinc'],
        'draft' => ['label' => 'Concept', 'color' => 'zinc'],
    ];

    $badge = $map[$status] ?? ['label' => ucfirst(str_replace('_', ' ', $status)), 'color' => 'zinc'];
@endphp

<flux:badge :color="$badge['color']" size="sm">{{ $badge['label'] }}</flux:badge>
