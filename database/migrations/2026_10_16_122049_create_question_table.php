<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel questions.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel events. 
            // CascadeOnDelete: Kalau event dihapus, semua pertanyaannya ikut terhapus otomatis.
            // Kita otomatis dapat B-Tree Index karena pakai foreign ID, kueri jadi O(log N) cepat!
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            
            // Relasi ke peserta yang nanya
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Isi pertanyaannya (pakai text biar bisa nampung kalimat panjang)
            $table->text('body');
            
            // Penanda apakah pertanyaan sudah dijawab atau belum (default: false/belum)
            $table->boolean('is_answered')->default(false);
            
            // Mencatat waktu kapan pertanyaan dibuat (created_at) & diupdate (updated_at)
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi (menghapus tabel jika diperlukan).
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};