<?php

namespace Tests\Feature\Expert;

use App\Models\Expert;
use App\Models\User;
use App\Models\WorkHour;
use App\Transformers\ExpertTransformer;
use Carbon\Carbonite;

use Illuminate\Support\Facades\Config;
use Tests\Feature\APITestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class IndexTest extends APITestCase
{

    public function test_get_experts_if_user_unauthorized()
    {
        Expert::factory()->count(15)->create();
        $response = $this->getJson('experts?page=2&per_page=5');
        $response
            ->assertStatus(401)
            ->assertJson(['message' => "Unauthorized", 'status_code' => 401]);
    }

    public function test_get_experts()
    {
        $user = User::factory()->create();
        Expert::factory()->count(15)->create();
        $response = $this->actingAs($user)->getJson('experts?page=2&per_page=5');
        $expert = Expert::all()->get(6);
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment($this->dataResponse($expert))
            ->assertJson($this->dataResponsePagination());
    }

    public function test_get_experts_include_work_hours()
    {
        $user = User::factory()->create();
        Expert::factory()->count(15)->has(WorkHour::factory()->count(2))->create();
        $response = $this->actingAs($user)->getJson('experts?include=workHours&page=2&per_page=5');
        $expert = Expert::all()->get(6);
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment($this->dataResponse($expert))
            ->assertJson($this->dataResponsePagination());
    }

    private function dataResponse(Expert $expert)
    {
        $response = $this->transformData($expert, new ExpertTransformer());
        return $response['data'];
    }

    private function dataResponsePagination()
    {
        return
            ['meta' =>
                ['pagination' =>
                    [
                        'total' => 15,
                        'count' => 5,
                        'per_page' => 5,
                        'current_page' => 2,
                        'total_pages' => 3,
                        'links' =>
                            [
                                'previous' => 'http://localhost/api/experts?page=1',
                                'next' => 'http://localhost/api/experts?page=3'
                            ]
                    ]
                ]];
    }

}
