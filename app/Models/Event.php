<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'live_token',
        'start_time',
        'end_time',
    ];
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }
    /**
     * @return BelongsToMany
     */
    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_registrations')
            ->withPivot(['is_attended', 'attended_at'])
            ->withTimestamps();
    }
}
