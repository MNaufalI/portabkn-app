<?php

namespace Tests\Feature;

use App\Jobs\GenerateCertificateJob;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create([
            'name' => 'student',
            'description' => 'Peserta Acara',
        ]);

        $this->user = User::factory()->create([
            'role_id' => $role->id,
            'email_verified_at' => now(),
        ]);

        $this->event = Event::factory()->create([
            'title' => 'Webinar Manajemen ASN BKN 2026',
            'live_token' => 'BKN2026X',
            'capacity' => 100,
        ]);
    }

    public function test_guest_cannot_submit_attendance(): void
    {
        $response = $this->post('/events/presence', [
            'event_id' => $this->event->id,
            'live_token' => 'BKN2026X',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_unregistered_user_cannot_submit_attendance(): void
    {
        $response = $this->actingAs($this->user)->post('/events/presence', [
            'event_id' => $this->event->id,
            'live_token' => 'BKN2026X',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_user_cannot_submit_invalid_live_token(): void
    {
        // Daftarkan user ke event terlebih dahulu
        $this->event->registeredUsers()->attach($this->user->id);

        $response = $this->actingAs($this->user)->post('/events/presence', [
            'event_id' => $this->event->id,
            'live_token' => 'TOKEN_SALAH',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Live Token presensi tidak valid. Pastikan token yang Anda masukkan sesuai dengan yang dibagikan narasumber.');
        
        $this->assertDatabaseHas('event_registrations', [
            'user_id' => $this->user->id,
            'event_id' => $this->event->id,
            'is_attended' => false,
        ]);
    }

    public function test_registered_user_can_submit_valid_live_token_and_dispatches_certificate_job(): void
    {
        Queue::fake();

        // Daftarkan user ke event
        $this->event->registeredUsers()->attach($this->user->id);

        $response = $this->actingAs($this->user)->post('/events/presence', [
            'event_id' => $this->event->id,
            'live_token' => 'BKN2026X',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Pastikan basis data mencatat is_attended = true
        $this->assertDatabaseHas('event_registrations', [
            'user_id' => $this->user->id,
            'event_id' => $this->event->id,
            'is_attended' => true,
        ]);

        // Pastikan job pembuatan sertifikat masuk antrean
        Queue::assertPushed(GenerateCertificateJob::class, function ($job) {
            return $job->user->id === $this->user->id && $job->event->id === $this->event->id;
        });
    }

    public function test_user_cannot_submit_attendance_twice(): void
    {
        // Daftarkan user dan tandai sudah presensi
        $this->event->registeredUsers()->attach($this->user->id, [
            'is_attended' => true,
            'attended_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->post('/events/presence', [
            'event_id' => $this->event->id,
            'live_token' => 'BKN2026X',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('warning');
    }

    public function test_unattended_user_cannot_download_certificate(): void
    {
        $this->event->registeredUsers()->attach($this->user->id, [
            'is_attended' => false,
        ]);

        $response = $this->actingAs($this->user)->get("/events/{$this->event->id}/certificate");
        $response->assertStatus(403);
    }
}

