<?php

namespace Database\Factories;

use App\Models\ObjectData;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ObjectDataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ObjectData::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'key' => Str::random(10),
            'value' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
