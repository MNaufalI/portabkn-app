<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;;
use Throwable;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Create roles
            $roles = [
                ['name' => 'admin', 'description' => 'Administrator Sistem BKN'],
                ['name' => 'dosen', 'description' => 'Pembawa Acara / Materi'],
                ['name' => 'student', 'description' => 'Peserta Acara'],
            ];

            foreach ($roles as $role) {
                Role::firstOrCreate(
                    ['name' => $role['name']],
                    ['description' => $role['description']]
                );
            }

            $this->command->info('Table roles succcessfully seeded with default roles.');
            
        } catch (Throwable $e) {
            Log::error('RoleSeeder Failed: ' . $e->getMessage());
            $this->command->error('Terjadi kesalahan saat seeding roles. Silakan periksa log untuk detailnya.');
        }
    }
}
