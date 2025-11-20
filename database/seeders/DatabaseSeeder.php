<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'nemadmin',
            'email' => 'admin1@example.com',
            'password' => 'password123',
            'is_admin' => false,
        ]);
        //$this->call(ReservationSeeder::class);

        //1|M43covsDPMZCaqHAGxP6FkYNdugEpBSizkxGovWg900f661f
    }
}
