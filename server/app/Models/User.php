<?php

namespace App\Models;

use App\Http\Traits\HasPivot;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasPivot, Notifiable, HasRoles, CanResetPassword, HasBelongsToManyEvents;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The vehicles that belong to this user.
     */
    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class)->withPivot('can_add', 'can_edit', 'can_delete', 'order')->orderByPivot('order');
    }

    protected static function boot()
    {
        parent::boot();

        $max_order = 0;
        static::belongsToManySyncing(function ($relation, $parent, $ids, $attributes) use (&$max_order) {
            if ($relation === "vehicles") {
                $max_order = $parent->vehicles->max('pivot.order');
            }
        });

        static::belongsToManySynced(function ($relation, $parent, $ids, $connections) use (&$max_order) {
            if ($relation === "vehicles") {
                $user = User::find($parent->id);
                foreach ($connections as $key => $connection) {
                    if (array_key_exists("order", $connection)) {
                        if ($connection["order"] > $max_order) {
                            $connection["order"] = $max_order;
                            $user->vehicles()->updateExistingPivot($key, ["order" => $max_order]);
                        }
                        $new_order = 0;
                        foreach ($parent->vehicles as $vehicle) {
                            if ($new_order === $connection["order"]) {
                                $new_order++;
                            }
                            if ($vehicle->id !== $key) {
                                $user->vehicles()->updateExistingPivot($vehicle->id, ["order" => $new_order]);
                                $new_order++;
                            }
                        }
                    }
                }
            }
        });
    }
}
