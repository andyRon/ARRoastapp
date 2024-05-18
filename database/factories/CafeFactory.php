<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cafe>
 */
class CafeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fake = fake();
        return [
            'name' => $fake->company,
            'address' => $fake->address,
            'city' => $fake->city,
            'state' => $fake->country,
            'zip' => $fake->postcode,
            'latitude' => $fake->latitude,
            'longitude' => $fake->longitude
        ];
    }
}
