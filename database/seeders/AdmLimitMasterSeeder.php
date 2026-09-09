<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AdmLimitMaster;
use Illuminate\Database\Seeder;

class AdmLimitMasterSeeder extends Seeder
{
    public function run(): void
    {
        AdmLimitMaster::query()
            ->updateOrCreate(
                [
                    'is_active' => true,
                ],
                [
                    'limit_amount' => 1000000.00,
                    'description' =>
                        'Maximum Assignment Direct Market amount.',
                ],
            );
    }
}