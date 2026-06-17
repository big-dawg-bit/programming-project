<?php

use Illuminate\Support\Facades\Blade;

function badgeText(string $status): string
{
    $html = Blade::render('<x-status-badge :status="$status" />', ['status' => $status]);

    // haal alle HTML-tags weg en plet witruimte tot losse spaties
    return trim(preg_replace('/\s+/', ' ', strip_tags($html)));
}

it('toont het juiste label per status', function () {
    expect(badgeText('submitted'))->toBe('Ingediend');
    expect(badgeText('approved'))->toBe('Goedgekeurd');
    expect(badgeText('rejected'))->toBe('Afgekeurd');
    expect(badgeText('changes_requested'))->toBe('Aanpassingen vereist');
});
