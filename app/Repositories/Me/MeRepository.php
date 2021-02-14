<?php


namespace App\Repositories\Me;

use App\Interfaces\Me\MeInterface;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Transformers\BookingTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Auth;

class MeRepository implements MeInterface
{
    use  ApiResponse;

    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function me()
    {
        $user = Auth::user();
        return $this->showOne($user, new UserTransformer());

    }
}
