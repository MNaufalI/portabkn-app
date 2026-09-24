<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try{
            $events = Event::factory(10)->create();
            $users = User::factory(50)->create();


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
