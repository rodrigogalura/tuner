<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\AllColumnsAreProjectableModel;

/**
 * @template TModel of \Workbench\App\Models\AllColumnsAreProjectableModel
 *
 * @extends Factory<TModel>
 */
class AllColumnsAreProjectableModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = AllColumnsAreProjectableModel::class;

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
