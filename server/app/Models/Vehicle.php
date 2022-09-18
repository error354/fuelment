<?php

namespace App\Models;

use App\Http\Traits\HasPivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory, HasPivot;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price_setting',
        'photo_setting',
        'fuel'
    ];

    public function getAverageFuelConsumption($vehicle = null)
    {
        $vehicle ??= $this;
        $totalAmount = 0;
        $totalDistance = 0;
        foreach ($vehicle->fuelings as $key => $fueling) {
            $prev_fueling = $fueling->getPrevFueling();
            if ($prev_fueling) {
                $totalDistance += ($fueling->mileage - $prev_fueling->mileage);
                $totalAmount += $fueling->amount;
            }
        }
        if ($totalDistance === 0) return null;
        return round(($totalAmount / $totalDistance * 100), 2);
    }

    /**
     * The users who have access to this vehicle.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('can_add', 'can_edit', 'can_delete', 'order');
    }

    /**
     * Fuelings of this vehicle
     */
    public function fuelings(): HasMany
    {
        return $this->hasMany(Fueling::class);
    }

    /**
     * Routes of this vehicle
     */
    public function routes(): HasMany
    {
        return $this->hasMany(Route::class);
    }
}
