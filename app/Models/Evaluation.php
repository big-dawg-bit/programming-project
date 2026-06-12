<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    protected $guarded = [];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function framework(): BelongsTo
    {
        return $this->belongsTo(CompetencyFramework::class, 'framework_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(EvaluationScore::class, 'evaluation_id');
    }
}
