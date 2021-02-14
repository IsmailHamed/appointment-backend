<?php


namespace App\Http\Controllers\API\Expert;


use App\Http\Controllers\Controller;
use App\Http\Requests\Expert\DeleteExpertRequest;
use App\Http\Requests\Expert\GetAvailabilityTimeRequest;
use App\Http\Requests\Expert\StoreExpertRequest;
use App\Http\Requests\Expert\UpdateExpertRequest;
use App\Interfaces\Expert\ExpertInterface;
use App\Interfaces\Me\MeInterface;
use App\Models\Expert;
use App\Transformers\ExpertTransformer;

/**
 * @group Expert management
 *  APIs for CRUD expert
 */
class ExpertController extends Controller
{
    protected $expert;

    public function __construct(ExpertInterface $expert)
    {
        $this->middleware('auth:api');
        $this->middleware('transform.input:' . ExpertTransformer::class)->except(['delete']);
        $this->expert = $expert;
    }

    /**
     * Get experts
     * This endpoint lets you to get basic information to experts
     * @responseFile storage/responses/expert/experts.json
     * @return mixed
     */

    public function index()
    {
        return $this->expert->all();

    }

    /**
     * Get specific expert
     * This endpoint lets you to get basic information to specific expert by identifier
     * @responseFile storage/responses/expert/index.json
     * @return mixed
     */

    public function show(Expert $expert)
    {

        return $this->expert->show($expert);
    }

    /**
     * Create expert
     * This endpoint lets you to create new expert
     * @responseFile storage/responses/expert/store.json
     * @return mixed
     */
    public function store(StoreExpertRequest $request)
    {
        return $this->expert->store();
    }

    /**
     * Update expert
     * This endpoint lets you to update existing expert by identifier
     * @responseFile storage/responses/expert/update.json
     * @return mixed
     */
    public function update(UpdateExpertRequest $request, Expert $expert)
    {
        return $this->expert->update($expert);
    }

    /**
     * Delete expert
     * This endpoint lets you to delete existing expert by identifier
     * @responseFile storage/responses/expert/delete.json
     * @return mixed
     */
    public function destroy(DeleteExpertRequest $request, Expert $expert)
    {
        return $this->expert->delete($expert);
    }

    /**
     * Delete expert
     * This endpoint lets you to delete existing expert by identifier
     * @responseFile storage/responses/expert/delete.json
     * @return mixed
     */
    public function getAvailabilityTime(GetAvailabilityTimeRequest $request, Expert $expert)
    {
        return $this->expert->getAvailabilityTime($expert);
    }

}

