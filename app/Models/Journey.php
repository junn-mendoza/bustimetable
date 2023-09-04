<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Models\StopPoint;

class Journey extends Model
{
    use HasFactory;
    protected $table = 'journey';

    public function stopRefFrom(): BelongsTo
    {
        return $this->belongsTo(StopPoint::class, 'atcocode','from_StopPointRef');
    }

    public function stopRefTo(): BelongsTo
    {
        return $this->belongsTo(StopPoint::class, 'atcocode','to_StopPointRef');
    }
}

