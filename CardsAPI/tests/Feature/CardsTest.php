<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Helpers\ResponseGenerator;
use Tests\TestCase;
use App\Models\User;

class CardsTest extends TestCase
{
    /** @test */
    public function test_creating_cards(){

        $user = User::where('username', 'like', 'Pablo')->firstOrFail();

        //Data vacía
        $responseNoData = $this->actingAs($user)->putJson('/api/cards/create', ["name"=>"","description"=>"","collectionId"=>0]);
        $responseNoData
            ->assertStatus(200)
            ->assertJson([
                'code' => 422
            ]);
    
        //Sin collección
        $responseNoCollection = $this->actingAs($user)->putJson('/api/cards/create', ["name"=>"NuevaCarta","description"=>"Unaa carta muy nueva","collectionId"=>25]);
        $responseNoCollection
            ->assertStatus(200)
            ->assertJson([
                'code' => 422
            ]);
    
        //Todo correcto
        $responseAllGood = $this->actingAs($user)->putJson('/api/cards/create', ["name"=>"NuevaCarta","description"=>"Unaa carta muy nueva","collectionId"=>1]);
        $responseAllGood
            ->assertStatus(200)
            ->assertJson([
                'code' => 200
            ]);
    }
    
}
