<?php

namespace App\GraphQL\Validators;

use App\Models\Fueling;
use App\Models\Vehicle;
use Nuwave\Lighthouse\Validation\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateFuelingInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        $query = request()['query'];
        $vehicle_id = Str::betweenFirst($query, 'id: ', ',');
        if (!is_numeric($vehicle_id)) {
            $vehicle_id = Str::betweenFirst($query, 'id: ', ')');
        }

        $vehicle = Vehicle::find($vehicle_id);
        $price = $this->arg('price');
        if ($vehicle->price_setting == 'disabled' && $price) {
            throw ValidationException::withMessages(["Adding prices is disabled for this vehicle."]);
        }
        if ($vehicle->price_setting == 'required' && !$price) {
            throw ValidationException::withMessages(["Price of fueling is required for this vehicle."]);
        }

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

        $prev_fueling = Fueling::getFuelingBefore($new_date, $vehicle_id);
        $next_fueling = Fueling::getFuelingAfter($new_date, $vehicle_id);

        if ($prev_fueling) {
            $prev_date = $prev_fueling->date;
            $prev_mileage = $prev_fueling->mileage;
            if ($prev_mileage >= $new_mileage) {
                throw ValidationException::withMessages(["Fueling's mileage cannot be lower than mileage of earlier fueling."]);
            }
        } else {
            $prev_fueling = -1;
            $prev_date = $new_date->copy()->subDay();
        }
        if ($next_fueling) {
            $next_date = $next_fueling->date;
            $next_mileage = $next_fueling->mileage;
            if ($next_mileage <= $new_mileage) {
                throw ValidationException::withMessages(["Fueling's mileage cannot be higher than mileage of later fueling."]);
            }
        } else {
            $next_mileage = $new_mileage + 1;
            $next_date = $new_date->copy()->addDay();
        }

        return [
            'date' => [
                Rule::unique('fuelings', 'date')
                    ->where('vehicle_id', $vehicle_id),
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
