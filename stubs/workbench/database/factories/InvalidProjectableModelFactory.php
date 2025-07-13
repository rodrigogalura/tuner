<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\InvalidProjectableModel;

/**
 * @template TModel of \Workbench\App\Models\InvalidProjectableModel
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class InvalidProjectableModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = InvalidProjectableModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }
}
