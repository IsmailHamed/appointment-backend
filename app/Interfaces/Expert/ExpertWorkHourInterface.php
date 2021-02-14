<?php


namespace App\Interfaces\Expert;


use App\Models\Expert;
use App\Models\WorkHour;

interface ExpertWorkHourInterface
{
    public function all(Expert $expert);

    public function store(Expert $expert);

    public function update(WorkHour $workHour);

    public function delete(WorkHour $workHour);


}
