<?php

namespace App\GraphQL\Mutations;

use App\Models\Fueling;
use App\Models\Route;
use App\Models\Vehicle;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Log;
use Nuwave\Lighthouse\Exceptions\ValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class RouteMutator
{
  function create($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
  {
    $fuelingId = $args["firstFuelingId"];
    $fueling = Fueling::find($fuelingId);
    if (!$fueling) {
      throw ValidationException::withMessages([
        'firstFuelingId' => ['There is no fueling with id ' . $fuelingId . '.'],
      ]);
    }

    $currentRoute = $fueling->route;
    $newRoute = new Route;
    $vehicle = Vehicle::find($fueling->vehicle->id);
    $vehicle->routes()->save($newRoute);

    if ($currentRoute) {
      $firstFuelingOfCurrentRoute = $currentRoute->getFirstFueling();
      if ((int)$fuelingId === (int)$firstFuelingOfCurrentRoute->id) {
        throw ValidationException::withMessages([
          'firstFuelingId' => ['This fueling already is the first fueling of a route'],
        ]);
      }
      $lastFuelingOfCurrentRoute = $currentRoute->getLastFueling();
      $fuelingsToUpdate = $currentRoute->fuelings->where('mileage', '<=', $lastFuelingOfCurrentRoute->mileage);
      foreach ($fuelingsToUpdate as $fuelingToUpdate) {
        $fuelingToUpdate->route()->associate($newRoute);
        $fuelingToUpdate->save();
      }
    } else {
      $fuelingsToUpdate = $fueling->vehicle->fuelings->where('mileage', '>=', $fueling->mileage);
      foreach ($fuelingsToUpdate as $fuelingToUpdate) {
        if ($fuelingToUpdate->route) {
          continue;
        }
        $fuelingToUpdate->route()->associate($newRoute);
        $fuelingToUpdate->save();
      }
    }



    // TODO: Move validation logic to validator class?

    return $newRoute;
  }
}
