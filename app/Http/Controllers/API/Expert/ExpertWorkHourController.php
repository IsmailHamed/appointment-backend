<?php


namespace App\Http\Controllers\API\Expert;


use App\Http\Controllers\Controller;
use App\Http\Requests\Expert\WorkHour\DeleteExpertWorkHourRequest;
use App\Http\Requests\Expert\WorkHour\StoreExpertWorkHourRequest;
use App\Http\Requests\Expert\WorkHour\UpdateExpertWorkHourRequest;
use App\Interfaces\Expert\ExpertWorkHourInterface;
use App\Models\Expert;
use App\Models\WorkHour;
use App\Transformers\WorkHourTransformer;

class ExpertWorkHourController extends Controller
{
    protected $expertWorkHour;

    public function __construct(ExpertWorkHourInterface $expertWorkHour)
    {
        //todo WorkHourTransformer
        $this->middleware('auth:api')->except('index');
//        $this->middleware('transform.input:' . WorkHourTransformer::class)->except(['delete']);
        $this->expertWorkHour = $expertWorkHour;
    }

    public function index(Expert $expert)
    {
        return $this->expertWorkHour->all($expert);
    }

    public function store(StoreExpertWorkHourRequest $request, Expert $expert)
    {
        return $this->expertWorkHour->store($expert);
    }

    public function update(UpdateExpertWorkHourRequest $request, Expert $expert, WorkHour $workHour)
    {
        return $this->expertWorkHour->update($workHour);
    }

    public function destroy(DeleteExpertWorkHourRequest $request, Expert $expert, WorkHour $workHour)
    {
        return $this->expertWorkHour->delete($workHour);
    }

}
