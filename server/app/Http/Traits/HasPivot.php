<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Auth;

trait HasPivot
{
  public function pivotDataResolver($data, $args)
  {
    $fieldName = $args['directive'][0];
    if (!$data->pivot) {
      return Auth::user()?->vehicles?->find($data->id)?->pivot[$fieldName];
    }
    return $data->pivot[$fieldName];
  }
}
