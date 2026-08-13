<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\BusinessUnit;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BusinessUnit>
 */
class BusinessUnitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = BusinessUnit::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $code = strtoupper($this->faker->unique()->lexify('BU???'));

        return [

            /*
            |--------------------------------------------------------------------------
            | UUID
            |--------------------------------------------------------------------------
            */

            'uuid' => (string) Str::uuid(),

            /*
            |--------------------------------------------------------------------------
            | Organization
            |--------------------------------------------------------------------------
            */

            'company_id' => Company::query()->value('id'),

            'branch_id' => Branch::query()->value('id'),

            /*
            |--------------------------------------------------------------------------
            | General Information
            |--------------------------------------------------------------------------
            */

            'business_unit_code' => $code,

            'business_unit_name' => $this->faker->company(),

            'short_name' => substr($code, 0, 10),

            /*
            |--------------------------------------------------------------------------
            | Responsibility
            |--------------------------------------------------------------------------
            */

            'manager_id' => User::query()->value('id'),

            /*
            |--------------------------------------------------------------------------
            | Contact
            |--------------------------------------------------------------------------
            */

            'email' => $this->faker->companyEmail(),

            'phone' => $this->faker->phoneNumber(),

            'address' => $this->faker->address(),

            /*
            |--------------------------------------------------------------------------
            | Description
            |--------------------------------------------------------------------------
            */

            'remarks' => $this->faker->optional()->sentence(),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'sort_order' => $this->faker->numberBetween(1, 100),

            'is_default' => false,

            'is_active' => true,

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            'created_by' => User::query()->value('id'),

            'updated_by' => User::query()->value('id'),

        ];
    }

    /**
     * Default Business Unit.
     */
    public function default(): static
    {
        return $this->state(fn () => [

            'business_unit_code' => 'FIN',

            'business_unit_name' => 'Finance',

            'short_name' => 'Finance',

            'sort_order' => 1,

            'is_default' => true,

            'is_active' => true,

        ]);
    }

    /**
     * Inactive Business Unit.
     */
    public function inactive(): static
    {
        return $this->state(fn () => [

            'is_active' => false,

        ]);
    }
}