<?php

namespace App\Models;

use App\Http\Traits\HasPivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

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
     * The users who have access to this vehicle.
     */
    public function users(): BelongsToMany
    {
        $max_order = DB::table('user_vehicle')
            ->where('vehicle_id', '=', $this->id)
            ->get()
            ->max('order');
        return $this->belongsToMany(User::class)->withPivot('can_add', 'can_edit', 'can_delete', 'order');
    }
}
