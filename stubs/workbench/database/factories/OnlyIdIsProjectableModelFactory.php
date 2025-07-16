<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\OnlyIdIsProjectableModel;

/**
 * @template TModel of \Workbench\App\Models\OnlyIdIsProjectableModel
 *
 * @extends Factory<TModel>
 */
class OnlyIdIsProjectableModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = OnlyIdIsProjectableModel::class;

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
