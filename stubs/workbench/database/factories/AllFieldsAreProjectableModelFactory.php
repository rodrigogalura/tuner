<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\AllFieldsAreProjectableModel;

/**
 * @template TModel of \Workbench\App\Models\AllFieldsAreProjectableModel
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class AllFieldsAreProjectableModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = AllFieldsAreProjectableModel::class;

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
