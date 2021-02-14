<?php


namespace Tests\Feature\Me;


use App\Models\User;
use App\Traits\FileManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\APITestCase;

class MeTest extends APITestCase
{
    use RefreshDatabase, FileManagement;

    public function test_get_authenticated_user()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->getJson('auth/me');
        $response->assertStatus(200);
        $user = User::find($user->id);
        $response->assertJson(['data' => $this->dataResponse($user)]);
    }



    private function dataResponse(User $user)
    {
        return [
            'identifier' => (int)$user->id,
            'firstName' => ucfirst($user->first_name),
            'lastName' => ucfirst($user->last_name),
            'email' => (string)$user->email,
            'status' => (int)$user->status,
            'imageLink' => isset($user->image_name) ? Storage::url(User::$ImagesDir . '/' . $user->image_name) : null,
            'emailVerified' => $user->hasVerifiedEmail(),
            'creationDate' => (string)$user->created_at,
            'lastChange' => (string)$user->updated_at,
            'deleteDate' => isset($user->deleted_at) ? (string)$user->deleted_at : null,];
    }

}
