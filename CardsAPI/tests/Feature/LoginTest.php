<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use App\Http\Helpers\ResponseGenerator;

class LoginTest extends TestCase
{
    /** @test */
    public function test_user_not_found_at_login(){

        //Usuario no encontrado
        $notFoundUser = $this->postJson('/api/users/login', ["username"=>"aaa","password"=>"1234"]);
        $notFoundUser
            ->assertStatus(200)
            ->assertJson([
                'code' => 404,
                'message' => "Login incorrecto, usuario erróneo"
            ]);

    }
    /** @test */
    public function test_login_incorrect(){
        //Usuario y contraseña no coincidentes correctos
        $loginCorrect = $this->postJson('/api/users/login', ["username"=>"Juan","password"=>"1234567"]);
        $this->assertDatabaseHas('users', ["username"=>"Juan"]);
        $loginCorrect
            ->assertStatus(200)
            ->assertJson([
                'code' => 404,
                'message' => "Login incorrecto, comprueba la contraseña"
            ]);
        
    }
    /** @test */
    public function test_login_correct(){
        //Usuario y contraseña correctos
        $loginCorrect = $this->postJson('/api/users/login', ["username"=>"Juan","password"=>"1234"]);
        $loginCorrect
            ->assertStatus(200)
            ->assertJson([
                'code' => 200,
                'message' => "Login correcto"
            ]);
        $this->assertDatabaseHas('users', ["username"=>"Juan"]);
    }
}
