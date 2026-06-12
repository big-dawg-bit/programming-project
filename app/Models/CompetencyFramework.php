<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetencyFramework extends Model
{
    protected $guarded = [];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function competencies(): HasMany
    {
        return $this->hasMany(Competency::class, 'framework_id');
    }

    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class, 'framework_id');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'framework_id');
    }
}
