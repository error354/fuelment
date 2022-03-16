<?php

namespace App\GraphQL\Validators;

use App\Models\Fueling;
use Nuwave\Lighthouse\Validation\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateFuelingInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        $query = request()['query'];
        $fueling_id = Str::betweenFirst($query, 'id: ', ',');
        if (!is_numeric($fueling_id)) {
            $fueling_id = Str::betweenFirst($query, 'id: ', ')');
        }
        $fueling = Fueling::find($fueling_id);
        $vehicle_id = $fueling->vehicle->id;

        $new_date = $this->arg('date');
        $date_now = date("Y-m-d");
        if ($date_now < $new_date) {
            throw ValidationException::withMessages(["Fueling's date cannot be in future."]);
        }
        $new_mileage = $this->arg('mileage');
        $prev_date = null;
        $prev_mileage = null;
        $next_date = null;
        $next_mileage = null;

        if ($new_date && $new_mileage) {
            $prev_fueling = $fueling->getPrevFueling($new_date);
            $next_fueling = $fueling->getNextFueling($new_date);
            if ($prev_fueling) {
                $prev_date = $prev_fueling->date;
                $prev_mileage = $prev_fueling->mileage;
                if ($prev_mileage >= $new_mileage) {
                    throw ValidationException::withMessages(["Fueling's mileage cannot be lower than mileage of earlier fueling."]);
                }
            }
            if ($next_fueling) {
                $next_date = $next_fueling->date;
                $next_mileage = $next_fueling->mileage;
                if ($next_mileage <= $new_mileage) {
                    throw ValidationException::withMessages(["Fueling's mileage cannot be higher than mileage of later fueling."]);
                }
            }
        } elseif ($new_date) {
            $prev_fueling = $fueling->getPrevFueling($new_date);
            $next_fueling = $fueling->getNextFueling($new_date);
            if ($prev_fueling) {
                $prev_date = $prev_fueling->date;
                $prev_mileage = $prev_fueling->mileage;
                if ($prev_mileage >= $fueling->mileage) {
                    throw ValidationException::withMessages(["Fueling's mileage cannot be lower than mileage of earlier fueling."]);
                }
            }
            if ($next_fueling) {
                $next_date = $next_fueling->date;
                $next_mileage = $next_fueling->mileage;
                if ($next_mileage <= $fueling->mileage) {
                    throw ValidationException::withMessages(["Fueling's mileage cannot be higher than mileage of later fueling."]);
                }
            }
        } elseif ($new_mileage) {
            $prev_fueling = $fueling->getPrevFueling($fueling->date);
            $next_fueling = $fueling->getNextFueling($fueling->date);
            if ($prev_fueling) {
                $prev_date = $prev_fueling->date;
                $prev_mileage = $prev_fueling->mileage;
            }
            if ($next_fueling) {
                $next_date = $next_fueling->date;
                $next_mileage = $next_fueling->mileage;
            }
        }

        if ($new_mileage && !$next_fueling) {
            $next_mileage = $new_mileage + 1;
        }
        if ($new_mileage && !$prev_fueling) {
            $prev_fueling = -1;
        }

        if ($new_date && !$next_date) {
            $next_date = $new_date->copy()->addDay();
        }
        if ($new_date && !$prev_date) {
            $prev_date = $new_date->copy()->subDay();
        }

        return [
            'date' => [
                Rule::unique('fuelings', 'date')
                    ->where('vehicle_id', $vehicle_id)
                    ->ignore($fueling_id),
                'date',
                'after:' . $prev_date,
                'before:' . $next_date
            ],
            'mileage' => [
                'numeric',
                'min:' . $prev_mileage + 0.01,
                'max:' . $next_mileage - 0.01
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'date.unique' => 'There can be only one fueling of a vehicle per day.',
            'mileage.min' => "Fueling's mileage cannot be smaller than previous fueling's mileage.",
            'mileage.max' => "Fueling's mileage cannot be greater than next fueling's mileage.",
        ];
    }
}
