<?php

namespace Tests\Feature\Expert;

use App\Models\Expert;
use App\Models\User;
use Carbon\Carbonite;

use Illuminate\Support\Facades\Config;
use Tests\Feature\APITestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteTest extends APITestCase
{

    public function test_delete_expert_information_if_user_unauthorized()
    {
        $expert = Expert::factory()->create();
        $response = $this->deleteJson('experts/' . $expert->id);
        $response
            ->assertStatus(401)
            ->assertJson(['message' => "Unauthorized", 'status_code' => 401]);
    }

    public function test_delete_expert_information_by_user_not_administrator()
    {
        $user = User::factory()->create();
        $expert = Expert::factory()->create();
        $response = $this->actingAs($user)
            ->deleteJson('experts/' . $expert->id);
        $response
            ->assertStatus(403)
            ->assertJson(['message' => "This action is unauthorized.", 'status_code' => 403]);
    }

    public function test_delete_expert_information()
    {
        $admin = $this->getAdministratorUser();
        $expert = Expert::factory()->create();
        $response = $this->actingAs($admin)
            ->deleteJson('experts/' . $expert->id);
        $response
            ->assertStatus(200)
            ->assertJson(['message' => "Expert's information deleted successfully", 'status_code' => 200]);
        $experts = Expert::all();
        $users = User::all();
        $this->assertEquals(0, $experts->count());
        // user count = 1 because we have admin user
        $this->assertEquals(1,$users->count());
    }

}
