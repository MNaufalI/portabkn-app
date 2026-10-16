<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     * Ini penting untuk keamanan agar tidak ada user usil yang mencoba
     * menyuntikkan data ke kolom yang tidak seharusnya.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'user_id',
        'body',
        'is_answered',
    ];

    /**
     * Konversi tipe data otomatis (Casting).
     * Memastikan 'is_answered' selalu bernilai boolean (true/false) di PHP,
     * bukan sekadar angka 0 atau 1 dari database.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_answered' => 'boolean',
        ];
    }

    /**
     * Relasi: Pertanyaan ini milik siapa?
     * Mengembalikan relasi ke model User (O(1) lookup jika digabung eager loading).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Pertanyaan ini untuk acara/event yang mana?
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}