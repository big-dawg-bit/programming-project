<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Competency extends Model
{
    protected $guarded = [];

    public function framework(): BelongsTo
    {
        return $this->belongsTo(CompetencyFramework::class, 'framework_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(EvaluationScore::class, 'competency_id');
    }

}
