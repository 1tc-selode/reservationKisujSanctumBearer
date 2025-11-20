<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    use RefreshDatabase;

    #[Test]
    public function user_can_register(){
        //arrange
        $payload = [
            'name'=>'Test User',
            'email'=>'test@example.com',
            'password'=>'password',
            'password_confirmation'=>'password',
        ];

        //act
        $response = $this->postJson('/api/register', $payload);

        //assert

        $response->assertStatus(201)->assertJsonStructure(['message','user']);
        $this ->assertDatabaseHas('users', ['email'=>'test@example.com']);
    }

    #[Test]
    public function user_can_login_and_recieve_token(){
        //arrange
        $user = User::factory()->create([
            'email'=>'teszt@example.com',
            'password'=>bcrypt('password123'),
        ]);

        $credentials = [
            'email'=>'teszt@example.com',
            'password'=>('password123'),
        ];

        //act
        $response = $this->postJson('/api/login', $credentials); 


        //assert
        $response->assertStatus(201)->assertJsonStructure(['message','user']);
    }

    // #[Test]
    // public function user_can_logout(){
    //     //arrange


    //     //act


    //     //assert
        
    // }
}
