<?php

namespace Database\Factories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Application::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'              => explode('.', $this->faker->domainName)[0] . '_app',
            'uuid'               => $this->faker->uuid,
            'event_endpoint_url' => route('subscription-changed'),
            'market_credentials' => [
                'ios'     => [
                    'username' => $this->faker->userName,
                    'password' => $this->faker->password(8, 15)
                ],
                'android' => [
                    'username' => $this->faker->userName,
                    'password' => $this->faker->password(8, 15)
                ]
            ]
        ];
    }
}
