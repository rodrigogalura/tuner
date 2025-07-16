<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\OnlyIdAndNameAreProjectableModel;

/**
 * @template TModel of \Workbench\App\Models\OnlyIdAndNameAreProjectableModel
 *
 * @extends Factory<TModel>
 */
class OnlyIdAndNameAreProjectableModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = OnlyIdAndNameAreProjectableModel::class;

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
