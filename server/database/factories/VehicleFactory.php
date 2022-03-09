<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->firstName(),
            'price_setting' => $this->faker->randomElement(['disabled', 'optional', 'required']),
            'photo_setting' => $this->faker->randomElement(['disabled', 'optional', 'required']),
            'fuel' => $this->faker->randomElement(['petrol', 'diesel', 'LPG', 'CNG', 'hydrogen', 'electricity', 'other'])
        ];
    }
}
