<?php

namespace Tests\Feature;

use \App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use  DatabaseTransactions;

    public function testRequiredFieldsForRegistration()
    {
        $this->json('POST', 'api/register', ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJson([
            "message" => "The given data was invalid.",
            "errors" => [
                "name" => ["The name field is required."],
                "email" => ["The email field is required."],
                "password" => ["The password field is required."],
                "isManager"=> ["The is manager field is required."] 
            ]
        ]);
     }

    public function testRepeatPassword()
    {
        $userData = [
            "name" => "Eurico Ferreira",
            "email" => "eurico@example.com",
            "password" => "random",
            "isManager" => false
         ];

        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "password" => ["The password confirmation does not match."]
                ]
        ]);
    }
    
    public function testMustEnterEmailAndPassword()
    {
        $this->json('POST', 'api/login')
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    'email' => ["The email field is required."],
                    'password' => ["The password field is required."],
                ]
        ]);
    }

    public function testSuccessfulLogin() 
    {
        $user = factory(User::class)->create([
            'email' => 'eurico@example.com',
            'password' => bcrypt('random'),
         ]);
 
 
         $loginData = ['email' => 'eurico@example.com', 'password' => 'random'];
 
         $this->json('POST', 'api/login', $loginData, ['Accept' => 'application/json'])
             ->assertStatus(200)
             ->assertJsonStructure([
                "user" => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
                 "access_token",
             ]);
 
         $this->assertAuthenticated();
    }

}
