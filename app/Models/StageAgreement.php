<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StageAgreement extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'mentor_approved_at' => 'datetime',
            'student_signed_at' => 'datetime',
            'docent_signed_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(StageApplication::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Werkt de status bij op basis van de handtekeningen: pas wanneer zowel de
     * student als de docent getekend heeft, gaat de overeenkomst naar 'ingediend'
     * (klaar voor de stagecommissie). Een al bevestigde overeenkomst blijft staan.
     */
    public function syncSignatureStatus(): void
    {
        if ($this->status === 'bevestigd') {
            return;
        }

        $this->status = ($this->student_signature && $this->docent_signature)
            ? 'ingediend'
            : 'te_ondertekenen';

        $this->save();
    }
}
