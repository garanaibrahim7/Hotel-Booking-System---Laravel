<?php

namespace Database\Seeders;

use App\Models\RoomDetail;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->count(20)->create([
            'password' => \Illuminate\Support\Facades\Hash::make('123'),
        ]);

        // User::factory()->create([
        //     'name' => 'Admin',
        //     'email' => 'admin@example.com',
        //     'role' => 'admin',
        //     'password' => Hash::make('123'),
        // ]);
        // User::factory()->create([
        //     'name' => 'Manager 1',
        //     'email' => 'manager@example.com',
        //     'role' => 'manager',
        //     'password' => Hash::make('123'),
        // ]);
        // User::factory()->create([
        //     'name' => 'Customer 1',
        //     'email' => 'customer@example.com',
        //     'role' => 'customer',
        //     'password' => Hash::make('123'),
        // ]);

        // RoomDetail::create([
        //     'hotel_id' => 7,
        //     'type' => 'double',
        //     'description' => 'One of the Best Rooms',
        //     'category' => 'suite',
        //     'qty' => '2',
        //     'price' => 2500,
        // ]);
    }
}
