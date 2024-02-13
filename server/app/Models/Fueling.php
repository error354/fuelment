<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fueling extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'amount',
        'mileage',
        'full',
        'fuel_consumption',
        'date',
        'price'
    ];

    /**
     * Vehicle which was refueled
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function isFirstOfRoute($fueling = null)
    {
        $fueling ??= $this;
        if ($fueling->route && $fueling->id === $fueling->route->getFirstFueling()->id) {
            return true;
        }
        return false;
    }

    public function isLastOfRoute($fueling = null)
    {
        $fueling ??= $this;
        if ($fueling->route && $fueling->id === $fueling->route->getLastFueling()->id) {
            return true;
        }
        return false;
    }

    public function getFuelConsumption($fueling = null)
    {
        $fueling ??= $this;
        $prev_fueling = $fueling->getPrevFueling();
        if ($prev_fueling?->full && $fueling->full) {
            $mileage_difference = $fueling->mileage - $prev_fueling->mileage;
            $fuel_consumption = round($fueling->amount / $mileage_difference * 100, 2);
            return $fuel_consumption;
        }
        return null;
    }

    public function getKilometerCost($fueling = null)
    {
        $fueling ??= $this;
        $prev_fueling = $fueling->getPrevFueling();
        if ($prev_fueling?->full) {
            $mileage_difference = $fueling->mileage - $prev_fueling->mileage;
            $cost = round($fueling->price / $mileage_difference, 2);
            return $cost;
        }
        return null;
    }

    public function getPrevFueling($mileage = null)
    {
        $mileage ??= $this->mileage;
        return Fueling::where('vehicle_id', $this->vehicle_id)
            ->where('id', '<>', $this->id)
            ->where('mileage', '<', $mileage)
            ->orderBy('mileage', 'desc')
            ->first();
    }

    public function getNextFueling($mileage = null)
    {
        $mileage ??= $this->mileage;
        return Fueling::where('vehicle_id', $this->vehicle_id)
            ->where('id', '<>', $this->id)
            ->where('mileage', '>', $mileage)
            ->orderBy('mileage', 'asc')
            ->first();
    }

    public static function getFuelingBefore($mileage, $vehicle_id)
    {
        return Fueling::where('vehicle_id', $vehicle_id)
            ->where('mileage', '<', $mileage)
            ->orderBy('mileage', 'desc')
            ->first();
    }

    public static function getFuelingAfter($mileage, $vehicle_id)
    {
        return Fueling::where('vehicle_id', $vehicle_id)
            ->where('mileage', '>', $mileage)
            ->orderBy('mileage', 'asc')
            ->first();
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('mileage', 'desc');
        });

        static::creating(function ($fueling) {
            $vehicle = Vehicle::find($fueling->vehicle_id);
            if ($fueling->newRoute || !count($vehicle->fuelings)) {
                $newRoute = new Route;
                $vehicle = Vehicle::find($fueling->vehicle->id);
                $vehicle->routes()->save($newRoute);
                $fueling->route()->associate($newRoute);
            } else {
                $route = $fueling->getPrevFueling()->route;
                $fueling->route()->associate($route);
            }
            unset($fueling->newRoute);
        });

        static::updating(function ($fueling) {
            if ($fueling->newRoute && !$fueling->isFirstOfRoute()) {
                unset($fueling->newRoute);
                $old_route_id = $fueling->route->id;
                $newRoute = new Route;
                $vehicle = Vehicle::find($fueling->vehicle->id);
                $vehicle->routes()->saveQuietly($newRoute);
                $temp_fueling = $fueling;
                while ($temp_fueling?->route?->id === $old_route_id) {
                    $temp_fueling->route()->associate($newRoute);
                    $temp_fueling->saveQuietly();
                    $temp_fueling = $temp_fueling->getNextFueling();
                }
                $old_route = Route::find($old_route_id);
                if (!count($old_route->fuelings)) {
                    $route->delete();
                }
            } 
            else if (!$fueling->newRoute && $fueling->isFirstOfRoute() && $fueling->route->getPrevRoute()) {
                unset($fueling->newRoute);
                $old_route = $fueling->route;
                $old_routes_fuelings = $old_route->fuelings;
                $prev_route = $fueling->route->getPrevRoute();
                foreach ($old_routes_fuelings as &$old_routes_fueling) {
                    $old_routes_fueling->route()->associate($prev_route);
                    $old_routes_fueling->saveQuietly();
                }
                $old_route->delete();
            }
            unset($fueling->newRoute);
        });

        static::deleted(function ($fueling) {
            $route = $fueling->route;
            if (!count($route->fuelings)) {
                $route->delete();
            }
        });
    }
}
