<?php

namespace App\Policies;

use App\Models\Fueling;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class FuelingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Fueling  $fueling
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Fueling $fueling)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  array  $request
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, array $request)
    {
        $fueling = Fueling::find($request['id']);
        $vehicle_id = $fueling->vehicle_id;
        $vehicle = $user->vehicles()->find($vehicle_id);
        if (!$vehicle) {
            return Response::deny("You have no access to the vehicle whose fuelings you are trying to modify.");
        }
        $can_edit = $vehicle->pivot->can_edit;
        if ($can_edit) {
            return Response::allow();
        }
        return Response::deny("You have no permission to modify fuelings of this vehicle.");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  array  $request
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, array $request)
    {
        foreach ($request['id'] as $i) {
            $fueling = Fueling::find($i);
            $vehicle_id = $fueling->vehicle_id;
            $vehicle = $user->vehicles()->find($vehicle_id);
            if (!$vehicle) {
                return Response::deny("You have no access to the vehicle whose fuelings you are trying to delete.");
            }
            $can_delete = $vehicle->pivot->can_delete;
            if (!$can_delete) {
                return Response::deny("You have no permission to delete fuelings of this vehicle.");
            }
        }
        return Response::allow();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Fueling  $fueling
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Fueling $fueling)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Fueling  $fueling
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Fueling $fueling)
    {
        //
    }
}
