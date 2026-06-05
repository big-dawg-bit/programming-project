<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Weeklog extends Model
{
    protected $guarded = [];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(WeeklogComment::class, 'weeklog_id');
    }

}
