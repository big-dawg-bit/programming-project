<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StageApplication extends Model
{
    protected $guarded = [];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ApplicationReview::class, 'application_id');
    }

    public function agreement(): HasOne
    {
        return $this->hasOne(StageAgreement::class, 'application_id');
    }

    public function stage(): HasOne
    {
        return $this->hasOne(Stage::class, 'application_id');
    }
}
