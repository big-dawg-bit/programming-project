<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalReport extends Model
{
    protected $guarded = [];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
