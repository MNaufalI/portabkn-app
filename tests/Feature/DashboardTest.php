<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

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
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_dashboard_with_events(): void
    {
        $event = Event::factory()->create([
            'title' => 'Sosialisasi Manajemen ASN BKN 2026',
            'capacity' => 100,
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Sosialisasi Manajemen ASN BKN 2026');
    }

    public function test_user_can_search_events(): void
    {
        Event::factory()->create([
            'title' => 'Webinar Kepegawaian Khusus',
        ]);
        Event::factory()->create([
            'title' => 'Workshop Teknis SKB',
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard?search=Kepegawaian');

        $response->assertOk();
        $response->assertSee('Webinar Kepegawaian Khusus');
        $response->assertDontSee('Workshop Teknis SKB');
    }

    public function test_user_can_register_for_an_event(): void
    {
        $event = Event::factory()->create([
            'capacity' => 50,
            'start_time' => now()->addDays(2),
            'end_time' => now()->addDays(2)->addHours(2),
        ]);

        $response = $this->actingAs($this->user)->post('/events/register', [
            'event_id' => $event->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('event_registrations', [
            'user_id' => $this->user->id,
            'event_id' => $event->id,
        ]);
    }

    public function test_user_cannot_register_twice(): void
    {
        $event = Event::factory()->create([
            'capacity' => 50,
            'start_time' => now()->addDays(2),
            'end_time' => now()->addDays(2)->addHours(2),
        ]);

        $this->actingAs($this->user)->post('/events/register', [
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($this->user)->post('/events/register', [
            'event_id' => $event->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('warning');
    }

    public function test_user_cannot_register_when_quota_is_full(): void
    {
        $event = Event::factory()->create([
            'capacity' => 1,
            'start_time' => now()->addDays(2),
            'end_time' => now()->addDays(2)->addHours(2),
        ]);

        // Fill the quota with another user
        $otherUser = User::factory()->create();
        $event->registeredUsers()->attach($otherUser->id);

        $response = $this->actingAs($this->user)->post('/events/register', [
            'event_id' => $event->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Mohon maaf, kuota acara ini sudah penuh.');
    }
}

