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
            $dosenRole = Role::where('name', 'dosen')->firstOrFail();
            $studentRole = Role::where('name', 'student')->firstOrFail();

            // Admin Account
            $adminUser = User::firstOrCreate(
                ['email' => 'admin@bkn.go.id'],
                [
                    'name' => 'Admin PortaBKN',
                    'password' => Hash::make('admin123'),
                    'nik_encrypted' => Crypt::encryptString('1234567890123456'),
                    'role_id' => $adminRole->id,
                ]
            );

            // Dosen / Pemateri BKN
            $speakers = collect([
                ['name' => 'Dr. H. Bambang Suprayitno, M.Si', 'email' => 'bambang@bkn.go.id'],
                ['name' => 'Prof. Sri Mulyani Indrawati, Ph.D', 'email' => 'sri.mulyani@bkn.go.id'],
                ['name' => 'Ir. Arya Wicaksana, M.Kom', 'email' => 'arya.wicaksana@bkn.go.id'],
                ['name' => 'Dra. Endang Rahayu, M.M', 'email' => 'endang.rahayu@bkn.go.id'],
            ])->map(function ($speakerData, $index) use ($dosenRole) {
                return User::firstOrCreate(
                    ['email' => $speakerData['email']],
                    [
                        'name' => $speakerData['name'],
                        'password' => Hash::make('password'),
                        'nik_encrypted' => Crypt::encryptString(str_pad((string)(3201000000000001 + $index), 16, '0', STR_PAD_LEFT)),
                        'role_id' => $dosenRole->id,
                    ]
                );
            });

            // Create dummy events and users
            $events = Event::factory(17)->create();
            $users = User::factory(50)->create([
                'role_id' => $studentRole->id,
            ]);

            // Attach 1-2 speakers per event
            foreach ($events as $event) {
                $eventSpeakers = $speakers->random(random_int(1, min(2, $speakers->count())));
                $event->speakers()->syncWithoutDetaching($eventSpeakers->pluck('id')->toArray());
            }

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