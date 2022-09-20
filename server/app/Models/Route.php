<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use function PHPUnit\Framework\isNull;

class Route extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * Fuelings during the route
     */
    public function fuelings(): HasMany
    {
        return $this->hasMany(Fueling::class);
    }

    /**
     * Vehicle that made the route
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * Get route's first fueling
     */
    public function getFirstFueling($route = null)
    {
        $route ??= $this;
        if (!$route->fuelings) return null;
        return $route->fuelings->sortBy('mileage')->first();
    }

    /**
     * Get route's last fueling
     */
    public function getLastFueling($route = null)
    {
        $route ??= $this;
        if (!$route->fuelings) return null;
        return $route->fuelings->sortByDesc('mileage')->first();
    }

    /**
     * Get next route
     */
    public function getNextRoute($route = null)
    {
        $route ??= $this;
        return $route->vehicle->fuelings->where('mileage', '>', $route->getLastFueling()?->mileage)->sortBy('mileage')->first()?->route;
    }

    /**
     * Get previous route
     */
    public function getPrevRoute($route = null)
    {
        $route ??= $this;
        return $route->vehicle->fuelings->where('mileage', '<', $route->getFirstFueling()?->mileage)->sortByDesc('mileage')->first()?->route;
    }

    /**
     * Get distance
     */
    public function getDistance($route = null)
    {
        $route ??= $this;
        if (isNull($route->getNextRoute($route))) {
            return $route->getLastFueling()?->mileage - $route->getFirstFueling()?->mileage;
        }
        $distance = $route->getNextRoute($route)->getFirstFueling()?->mileage - $route->getFirstFueling()?->mileage;
        return $distance;
    }

    /**
     * Get average fuel consumption
     */
    public function getAverageFuelConsumption($route = null)
    {
        $route ??= $this;
        $totalAmount = 0;
        $count = count($route->fuelings);
        foreach ($route->fuelings as $key => $fueling) {
            if (--$count <= 0 && !$route->getNextRoute($route)) break;
            $totalAmount += $fueling->amount;
        }
        $distance = $route->getDistance($route);
        if (!$distance) {
            return null;
        }
        return round(($totalAmount / $distance * 100), 2);
    }

    /**
     * Get average cost of driving one kilometer of the route
     */
    public function getKilometerCost($route = null)
    {
        $route ??= $this;
        $totalCost = 0;
        $count = count($route->fuelings);
        foreach ($route->fuelings as $key => $fueling) {
            if (--$count <= 0 && !$route->getNextRoute($route)) break;
            $totalCost += $fueling->price;
        }
        $distance = $route->getDistance($route);
        if (!$distance) {
            return null;
        }
        return round(($totalCost / $distance), 2);
    }
}
