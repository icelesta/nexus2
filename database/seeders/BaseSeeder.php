<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

abstract class BaseSeeder extends Seeder
{
    protected Company $company;

    protected ?Branch $branch = null;

    protected User $user;

    /**
     * Bootstrap required master data.
     */
    protected function bootSeeder(): void
    {
        $this->company = Company::query()->firstOrFail();

        $this->branch = Branch::query()->first();

        $this->user = User::query()->firstOrFail();
    }

    /**
     * Generate UUID.
     */
    protected function uuid(): string
    {
        return (string) Str::uuid();
    }

    /**
     * Audit fields.
     */
    protected function audit(): array
    {
        return [

            'company_id' => $this->company->id,

            'branch_id'  => $this->branch?->id,

            'created_by' => $this->user->id,

            'updated_by' => $this->user->id,

        ];
    }
}