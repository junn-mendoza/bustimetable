<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Models\Route;

class StopPoint extends Model
{
    use HasFactory;

    public function routesRefFrom(): HasMany 
    {
        return $this->hasMany(Route::class, 'from_stopref', 'atcocode');
    }

    public function routesRefTo(): HasMany 
    {
        return $this->hasMany(Route::class, 'to_stopref', 'atcocode');
    }

} 
