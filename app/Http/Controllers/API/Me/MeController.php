<?php


namespace App\Http\Controllers\API\Me;


use App\Http\Controllers\Controller;
use App\Interfaces\Me\MeInterface;

/**
 * @group Me
 *  APIs for getting current user information
 */
class MeController extends Controller
{
    protected $me;

    public function __construct(MeInterface $me)
    {
        $this->middleware('auth:api');
        $this->me = $me;
    }

    /**
     * Me
     * This endpoint lets you to get basic information to authenticated user
     * @responseFile storage/responses/me/me.json
     * @return mixed
     */

    public function me()
    {
        return $this->me->me();

    }

}

