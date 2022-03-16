<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Arr;

class VehiclePolicy
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
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Vehicle $vehicle)
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
        $vehicle_id = $request['id'];
        $vehicle = $user->vehicles()->find($vehicle_id);
        $can_add = $vehicle->pivot->can_add;
        if (!$vehicle) {
            return Response::deny('You have no permission to modify this vehicle.');
        }

        $special_input_fields = ['id', 'fuelings', 'users'];
        $standard_fields_input = Arr::except($request, $special_input_fields);
        $standard_fields_input_count = count($standard_fields_input);
        if ($standard_fields_input_count && !$user->can('vehicles.edit')) {
            return Response::deny("You have no permission to modify vehicle's data.");
        }

        if (array_key_exists('fuelings', $request) && !$can_add) {
            return Response::deny('You have no permission to add fuelings to this vehicle.');
        }

        if (
            array_key_exists('users', $request) &&
            (!$user->can('vehicles.edit') || !$user->can('users.edit'))
        ) {
            return Response::deny("You have no permission to connect users and vehicles.");
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Vehicle $vehicle)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Vehicle $vehicle)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Vehicle $vehicle)
    {
        //
    }
}
