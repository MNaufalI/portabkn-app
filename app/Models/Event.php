<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'capacity',
        'live_token',
        'start_token',
        'end_token',
        'attendance_threshold_minutes',
        'youtube_url',
        'material_path',
        'start_time',
        'end_time',
    ];
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'attendance_threshold_minutes' => 'integer',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    protected function isFinished(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): bool => 
                isset($attributes['end_time']) && Carbon::parse($attributes['end_time'])->isPast()
        );
    }

    /**
     * Mengubah URL YouTube menjadi format Embed yang aman untuk iframe.
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (empty($this->youtube_url)) {
            return null;
        }

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $this->youtube_url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        return $this->youtube_url;
    }

    /**
     * @return BelongsToMany
     */
    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_speaker');
    }

    /**
     * @return BelongsToMany
     */
    public function registeredUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_registrations')
                    ->withPivot(['is_attended', 'attended_at', 'checked_in_at', 'checked_out_at'])
                    ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_registrations')
            ->withPivot(['is_attended', 'attended_at', 'checked_in_at', 'checked_out_at'])
            ->withTimestamps();
    }

    public function tickets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
 