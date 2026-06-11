<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
