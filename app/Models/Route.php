<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Models\RouteLink;
use Models\StopPoint;

class Route extends Model
{
    use HasFactory;

    public function routelinks(): HasMany
    {
        return $this->hasMany(RouteLink::class, 'routeid','routeid');
    }

    public function stopRefFrom(): BelongsTo
    {
        return $this->belongsTo(StopPoint::class, 'atcocode','routeid');
    }

    public function stopRefTo(): BelongsTo
    {
        return $this->belongsTo(StopPoint::class, 'atcocode','routeid');
    }
}

