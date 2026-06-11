<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    protected $guarded = [];

    public function mentors(): HasMany
    {
        return $this->hasMany(Mentor::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(StageApplication::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class);
    }

}
