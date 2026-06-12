<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklogComment extends Model
{
    protected $guarded = [];

    public function weeklog(): BelongsTo
    {
        return $this->belongsTo(Weeklog::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
