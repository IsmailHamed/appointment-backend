<?php


namespace Tests\Feature\Expert\WorkHour;


use App\Models\User;
use App\Models\WorkHour;
use Tests\Feature\APITestCase;

class DeleteTest extends APITestCase
{
    public function test_delete_work_hours_to_expert_by_user_not_authorized()
    {
        $workHour = WorkHour::factory()->create();
        $response = $this->deleteJson('experts/' . $workHour->expert_id . ' /workHours/' . $workHour->id);
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized', 'status_code' => 401]);
    }

    public function test_delete_work_hours_to_expert_by_user_not_administrator()
    {
        $user = User::factory()->create();
        $workHour = WorkHour::factory()->create();
        $response = $this->actingAs($user)->deleteJson('experts/' . $workHour->expert_id . ' /workHours/' . $workHour->id);
        $response->assertStatus(403)
            ->assertJson(['message' => 'This action is unauthorized.', 'status_code' => 403]);
    }

    public function test_delete_work_hours_to_expert()
    {
        $admin = $this->getAdministratorUser();
        $workHour = WorkHour::factory()->create();
        $response = $this->actingAs($admin)
            ->deleteJson('experts/' . $workHour->expert_id . '/workHours/' . $workHour->id);
        $this->assertCount(0, WorkHour::all());
        $response->assertJson(['message' => "The expert's work hour deleted successfully.", 'status_code' => 200]);
    }


}
