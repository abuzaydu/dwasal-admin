<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Visitor;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visitor>
 */
class VisitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
     protected $model = Visitor::class;
    public function definition(): array
    {
        
        return [
            'shop_id' => 1, // change or use random if you have multiple shops
            'user_id' => 1, // change or random
            'host_id' => 2, // change or random
            'department_id' => null, // or $this->faker->numberBetween(1,5)
            'name' => $this->faker->name,
            'mobile' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'address' => $this->faker->address,
            'id_type' => $this->faker->randomElement(['Passport', 'National ID', 'Driver License']),
            'id_number' => $this->faker->bothify('??#######'),
            'visitor_photo' => null,
            'badge_no' => $this->faker->bothify('B###'),
            'purpose' => $this->faker->randomElement(['Meeting', 'Delivery', 'Inspection']),
            'status' => 'Awaiting Host permission',
            'is_granted' => false,
            'time_in' => null,
            'time_out' => null,
        ];
    }
}
