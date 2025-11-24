<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ReservationAccessTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_view_all_reservations()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $reservation = Reservation::factory()->create();

        $response = $this->actingAs($admin)->getJson('/api/reservations');

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $reservation->id]);
    }

    #[Test]
    public function user_can_view_own_reservations()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson('/api/reservations');

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $reservation->id]);
    }

    #[Test]
    public function user_cannot_view_others_reservation()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $reservation = Reservation::factory()->for($otherUser)->create();

        $response = $this->actingAs($user)->getJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(403);
    }

    #[Test]
    public function user_can_update_own_reservation()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->for($user)->create();

        $updateData = ['note' => 'Frissített megjegyzés'];

        $response = $this->actingAs($user)->putJson("/api/reservations/{$reservation->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['note' => 'Frissített megjegyzés']);
    }

    #[Test]
    public function user_cannot_update_others_reservation()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $reservation = Reservation::factory()->for($otherUser)->create();

        $updateData = ['note' => 'Tiltott frissítés'];

        $response = $this->actingAs($user)->putJson("/api/reservations/{$reservation->id}", $updateData);

        $response->assertStatus(403);
    }

    #[Test]
    public function user_can_delete_own_reservation()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->for($user)->create();

        $response = $this->actingAs($user)->deleteJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Foglalás törölve.']);
    }

    #[Test]
    public function user_cannot_delete_others_reservation()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $reservation = Reservation::factory()->for($otherUser)->create();

        $response = $this->actingAs($user)->deleteJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(403);
    }
}