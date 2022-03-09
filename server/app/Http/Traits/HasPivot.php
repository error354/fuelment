<?php

namespace App\Http\Traits;

use Nuwave\Lighthouse\Exceptions\GenericException;

trait HasPivot
{
  public function pivotDataResolver($data, $args)
  {
    $fieldName = $args['directive'][0];
    if (!$data->pivot) {
      throw new GenericException("Field {$fieldName} can be requested only in a nested query");
      return null;
    }
    $res = $data->pivot[$fieldName];
    return $res;
  }
}
