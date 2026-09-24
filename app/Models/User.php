<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use InvalidArgumentException;

class User extends Authenticatable
{
    use Notifiable;
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nik_encrypted',
    ];
    protected $hidden = [
        'password',
        'remember_token',
        'nik_encrypted',
    ];
    protected function nik(): Attribute
    {
        return Attribute::make(
            get: function(mixed $value, array $attributes): ?string {
                if (empty($attributes['nik_encrypted'])) {
                    return null;
                }
                try {
                    return Crypt::decryptString($attributes['nik_encrypted']);
                }
                catch (DecryptException $e) {
                    Log::critical('Decryption failed', ['user_id' => $attributes['id'] ?? 'unknown']);
                    return null;
                }
            },
            set: function(string $value): array {
                $cleanNik = preg_replace('/[^0-9]/', '', $value);
                if (strlen($cleanNik) !== 16) {
                    throw new InvalidArgumentException('NIK must be exactly 16 digits.');
                }
                return ['nik_encrypted' => Crypt::encryptString($cleanNik)];
            }
        );
    }
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function registeredEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_registrations')
            ->withPivot(['is_attended', 'attended_at'])
            ->withTimestamps();
    }
}