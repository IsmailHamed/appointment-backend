<?php


namespace App\Repositories\Expert;


use App\Interfaces\Expert\ExpertBookingInterface;
use App\Interfaces\Expert\ExpertWorkHourInterface;
use App\Models\Booking;
use App\Models\Expert;
use App\Models\WorkHour;
use App\Traits\ApiResponse;
use App\Transformers\ExpertTransformer;
use App\Transformers\WorkHourTransformer;

class ExpertWorkHourRepository implements ExpertWorkHourInterface
{
    use ApiResponse;

    protected $model;

    public function __construct(Expert $model)
    {
        $this->model = $model;
    }

    public function all(Expert $expert)
    {
        $workHours = $expert->workHours();
        return $this->showAll($workHours, new WorkHourTransformer());
    }

    public function store(Expert $expert)
    {
        $requestData = request()->all();
        $workHours = [];
        foreach ($requestData as $data) {
            $workHour = new WorkHour([
                'day' => $data['day'],
                'from' => $data['from'],
                'to' => $data['to'],
            ]);
            array_push($workHours, $workHour);
        }
        $expert->workHours()->saveMany($workHours);
        return $this->successMessage("The expert's work hours added successfully.");
    }


    public function update(WorkHour $workHour)
    {
        $workHour->fill(request()->all());
        $workHour->day = (string)request('day');
        $workHour->save();
        return $this->successMessage("The expert's work hour updated successfully.");
    }

    public function delete(WorkHour $workHour)
    {
        $workHour->delete();
        return $this->successMessage("The expert's work hour deleted successfully.");
    }
}
