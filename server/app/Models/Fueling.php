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

    public function getPrevFueling($date)
    {
        return Fueling::where('vehicle_id', $this->vehicle_id)
            ->where('id', '<>', $this->id)
            ->where('date', '<', $date)
            ->orderBy('date', 'desc')
            ->first();
    }

    public function getNextFueling($date)
    {
        return Fueling::where('vehicle_id', $this->vehicle_id)
            ->where('id', '<>', $this->id)
            ->where('date', '>', $date)
            ->orderBy('date', 'asc')
            ->first();
    }

    public static function getFuelingBefore($date, $vehicle_id)
    {
        return Fueling::where('vehicle_id', $vehicle_id)
            ->where('date', '<', $date)
            ->orderBy('date', 'desc')
            ->first();
    }

    public static function getFuelingAfter($date, $vehicle_id)
    {
        return Fueling::where('vehicle_id', $vehicle_id)
            ->where('date', '>', $date)
            ->orderBy('date', 'asc')
            ->first();
    }

    public function calculateFuelConsumption($calculate_next = true, $save = false)
    {
        if ($this->full) {
            $prev_fueling = $this->getPrevFueling($this->date);
            if ($prev_fueling && $prev_fueling->full) {
                $mileage_difference = $this->mileage - $prev_fueling->mileage;
                $fuel_consumption = $this->amount / $mileage_difference * 100;
                $this->fuel_consumption = $fuel_consumption;
                if ($save) {
                    $this->saveQuietly();
                }
            }
            if ($calculate_next) {
                $next_fueling = $this->getNextFueling($this->date);
                if ($next_fueling && $next_fueling->full) {
                    $mileage_difference = $next_fueling->mileage - $this->mileage;
                    $fuel_consumption = $next_fueling->amount / $mileage_difference * 100;
                    $fuel_consumption = round($fuel_consumption, 2);
                    $next_fueling->fuel_consumption = $fuel_consumption;
                    $next_fueling->saveQuietly();
                }
            }
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('date', 'desc');
        });

        static::creating(function ($fueling) {
            $fueling->calculateFuelConsumption();
        });

        static::updating(function ($fueling) {
            $fueling->calculateFuelConsumption();
        });

        static::deleted(function ($fueling) {
            $next_fueling = $fueling->getNextFueling($fueling->date);
            if ($next_fueling) {
                $next_fueling->calculateFuelConsumption(false, true);
            }
        });
    }
}
