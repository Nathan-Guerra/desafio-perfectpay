<?php

namespace Database\Seeders;

use App\Models\Gateway;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Gateway::query()
            ->insert([
                ['name' => 'Asaas', 'slug' => 'asaas', 'created_at' => now(), 'updated_at' => now()],
            ]);
    }
}
