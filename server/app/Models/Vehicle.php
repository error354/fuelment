<?php

namespace App\Models;

use App\Http\Traits\HasPivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

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

    /**
     * Get vehicle's first fueling
     */
    public function getFirstFueling($fuelings = null)
    {
        $fuelings ??= $this->fuelings;
        if (!$fuelings) return null;
        return $fuelings->sortBy('mileage')->first();
    }

    /**
     * Get vehicle's last fueling
     */
    public function getLastFueling($fuelings = null)
    {
        $fuelings ??= $this->fuelings;
        if (!$fuelings) return null;
        return $fuelings->sortByDesc('mileage')->first();
    }

    /**
     * Get the vehicle's fuelings in a date range
     */
    private function getFuelingsBetweenDates($dateFrom = null, $dateTo = null) {
        $fuelings = $this->fuelings;
        if ($dateFrom) {
            $mileageFrom = $fuelings->sortBy('mileage')->where('date', '>=', $dateFrom)->first()?->mileage;
            if ($mileageFrom === null) return [];
            $fuelings = $fuelings->where('mileage', '>=', $mileageFrom);
        }
        if ($dateTo) {
            $mileageTo = $fuelings->sortByDesc('mileage')->where('date', '<=', $dateTo)->first()?->mileage;
            $fuelings = $fuelings->where('mileage', '<=', $mileageTo);
        }
        return $fuelings;
    }

    /**
     * Get average fuel consumption of the vehicle 
     */
    public function getAverageFuelConsumption($dateFrom = null, $dateTo = null)
    {
        $totalAmount = 0;
        $totalDistance = 0;
        $fuelings = $this->getFuelingsBetweenDates($dateFrom, $dateTo);
        foreach ($fuelings as $key => $fueling) {
            $prev_fueling = $fueling->getPrevFueling();
            if ($prev_fueling) {
                $totalDistance += ($fueling->mileage - $prev_fueling->mileage);
                $totalAmount += $fueling->amount;
            }
        }
        if ($totalDistance === 0) return 0;
        return round(($totalAmount / $totalDistance * 100), 2);
    }

    /**
     * Get distance driven be the vehicle
     */
    public function getDistance($dateFrom = null, $dateTo = null)
    {
        $fuelings = $this->getFuelingsBetweenDates($dateFrom, $dateTo);
        return $this->getLastFueling($fuelings)?->mileage - $this->getFirstFueling($fuelings)?->mileage;
    }

    /**
     * Get total amount of fuel used by this vehicle
     */
    public function getTotalFuelAmount($dateFrom = null, $dateTo = null)
    {
        $totalAmount = 0;
        $fuelings = $this->getFuelingsBetweenDates($dateFrom, $dateTo);
        foreach ($fuelings as $key => $fueling) {
            $totalAmount += $fueling->amount;
        }
        return round($totalAmount, 2);
    }

    /**
     * Get total amount of fuel used by this vehicle
     */
    public function getTotalCost($dateFrom = null, $dateTo = null)
    {
        $fuelings = $this->getFuelingsBetweenDates($dateFrom, $dateTo);
        $totalCost = 0;
        foreach ($fuelings as $key => $fueling) {
            $totalCost += $fueling->price;
        }
        return round($totalCost, 2);
    }

    /**
     * Get average cost of driving one kilometer of the vehicle
     */
    public function getKilometerCost($dateFrom = null, $dateTo = null)
    {
        $totalCost = 0;
        $fuelings = $this->getFuelingsBetweenDates($dateFrom, $dateTo);
        $count = count($fuelings);
        foreach ($fuelings as $key => $fueling) {
            if (--$count <= 0) break;
            $totalCost += $fueling->price;
        }
        $distance = $this->getDistance($dateFrom, $dateTo);
        if (!$distance) {
            return 0;
        }
        return round(($totalCost / $distance), 2);
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
