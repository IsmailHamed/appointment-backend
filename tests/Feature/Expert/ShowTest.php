<?php

namespace Tests\Feature\Expert;

use App\Models\Expert;
use App\Models\User;
use App\Transformers\ExpertTransformer;
use Carbon\Carbonite;

use Illuminate\Support\Facades\Config;
use Tests\Feature\APITestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShowTest extends APITestCase
{
    public function test_get_expert_by_id_if_user_unauthorized()
    {
        $experts = Expert::factory()->count(2)->create();
        $response = $this->getJson('experts/' . $experts->first()->id);
        $response
            ->assertStatus(401)
            ->assertJson(['message' => "Unauthorized", 'status_code' => 401]);
    }

    public function test_get_expert_by_id()
    {
        $user = User::factory()->create();
        $experts = Expert::factory()->count(2)->create();
        $response = $this->actingAs($user)->getJson('experts/' . $experts->first()->id);
        $response->assertStatus(200)
            ->assertExactJson($this->dataResponse($experts->first()));
    }

    private function dataResponse(Expert $expert)
    {
        $response = $this->transformData($expert, new ExpertTransformer());
        return $response;
    }

}
