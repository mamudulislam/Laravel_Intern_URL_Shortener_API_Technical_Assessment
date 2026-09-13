<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_a_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()->assertJsonPath('success', true)->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_user_can_create_and_only_view_owned_urls(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $url = $otherUser->urls()->create(['original_url' => 'https://example.com', 'short_code' => 'private']);

        $this->actingAs($user)->postJson('/api/urls', ['url' => 'https://laravel.com'])->assertCreated();
        $this->actingAs($user)->getJson('/api/urls/'.$url->id)->assertForbidden();
    }

    public function test_redirect_increments_click_count(): void
    {
        $user = User::factory()->create();
        $user->urls()->create(['original_url' => 'https://example.com', 'short_code' => 'abc123']);

        $this->get('/abc123')->assertRedirect('https://example.com');
        $this->assertDatabaseHas('urls', ['short_code' => 'abc123', 'click_count' => 1]);
    }
}
