<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try{

            $this->call([
                RoleSeeder::class,
            ]);

            // Retrieve roles
            $adminRole = Role::where('name', 'admin')->firstOrFail();
            $studentRole = Role::where('name', 'student')->firstOrFail();

            // Admin Account
            $adminUser = User::firstOrCreate(
                ['email' => 'admin@bkn.go.id'],
                [
                    'name' => 'Admin PortaBKN',
                    'password' => Hash::make('admin123'),
                    'nik_encrypted' => Crypt::encryptString('1234567890'),
                    'role_id' => $adminRole->id,
                ]
            );

            // Create dummy events and users
            $events = Event::factory(17)->create();
            $users = User::factory(50)->create([
                'role_id' => $studentRole->id,
            ]);

            // Register users to random events
            foreach ($users as $user) {
                $randomEvents = $events->random(random_int(1, 3));
                $eventIds = $randomEvents->pluck('id')->toArray();
                $user->registeredEvents()->attach($eventIds);
            }

            $this->command->info('Database berhasil diisi dengan data dummy.');

        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat seeding database: ' . $e->getMessage());
            $this->command->error('Terjadi kesalahan saat seeding database. Silakan periksa log untuk detailnya.');
        }
    }
}