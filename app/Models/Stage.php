<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Stage extends Model
{
    protected $guarded = [];

    public function application(): BelongsTo
    {
        return $this->belongsTo(StageApplication::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    public function docent(): BelongsTo
    {
        return $this->belongsTo(Docent::class);
    }

    public function framework(): BelongsTo
    {
        return $this->belongsTo(CompetencyFramework::class, 'framework_id');
    }

    public function weeklogs(): HasMany
    {
        return $this->hasMany(Weeklog::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function finalReport(): HasOne
    {
        return $this->hasOne(FinalReport::class);
    }
}
