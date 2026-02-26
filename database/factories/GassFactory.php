<?php

namespace Database\Factories;

use App\Models\Gass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gass>
 */
class GassFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Gass::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'program_project_activity' => $this->faker->sentence(4),
            'record_type' => $this->faker->randomElement(['program', 'project', 'activity', 'sub_activity']),
            'output_indicators' => $this->faker->sentence(6),
            'office' => $this->faker->randomElement(['RO', 'ABRA', 'APAYAO', 'BENGUET', 'IFUGAO', 'KALINGA', 'MT.PROVINCE', 'CAGAYAN', 'ISABELA', 'QUIRINO']),
            'universe' => $this->faker->numberBetween(100, 10000),
            'baseline' => $this->faker->numberBetween(50, 5000),
            'accomplishment' => $this->faker->numberBetween(0, 8000),
            'target_2024' => $this->faker->numberBetween(100, 2000),
            'target_2025' => $this->faker->numberBetween(100, 2000),
            'target_2026' => $this->faker->numberBetween(100, 2000),
            'target_2027' => $this->faker->numberBetween(100, 2000),
            'target_2028' => $this->faker->numberBetween(100, 2000),
            'remarks' => $this->faker->sentence(3),
            'parent_id' => null,
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }

    /**
     * Create a program record.
     */
    public function program(): static
    {
        return $this->state(fn (array $attributes) => [
            'record_type' => 'program',
            'parent_id' => null,
        ]);
    }

    /**
     * Create a project record.
     */
    public function project(): static
    {
        return $this->state(fn (array $attributes) => [
            'record_type' => 'project',
        ]);
    }

    /**
     * Create an activity record.
     */
    public function activity(): static
    {
        return $this->state(fn (array $attributes) => [
            'record_type' => 'activity',
        ]);
    }

    /**
     * Create a sub-activity record.
     */
    public function subActivity(): static
    {
        return $this->state(fn (array $attributes) => [
            'record_type' => 'sub_activity',
        ]);
    }

    /**
     * Create a child record with the given parent.
     */
    public function childOf(Gass $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'sort_order' => Gass::where('parent_id', $parent->id)->max('sort_order') + 1,
        ]);
    }
}
