<?php


namespace Tests\Feature\Expert\WorkHour;


use App\Models\Expert;
use App\Models\WorkHour;
use App\Transformers\ExpertTransformer;
use App\Transformers\WorkHourTransformer;
use Tests\Feature\APITestCase;

class IndexTest extends APITestCase
{
    public function test_get_work_hours_to_expert()
    {
        $expert = Expert::factory()->create([
            'id' => 1
        ]);
        WorkHour::factory()->count(15)->create([
            "expert_id" => $expert->id
        ]);
        $response = $this->getJson('experts/' . $expert->id . '/workHours?page=2&per_page=5');
        $workHour = $expert->workHours->get(6);
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment($this->dataResponse($workHour))
            ->assertJson($this->dataResponsePagination());
    }

    private function dataResponse($workHour)
    {
        $response=$this->transformData($workHour, new WorkHourTransformer());
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
                                'previous' => 'http://localhost/api/experts/1/workHours?page=1',
                                'next' => 'http://localhost/api/experts/1/workHours?page=3'
                            ]
                    ]
                ]
            ];
    }

}
