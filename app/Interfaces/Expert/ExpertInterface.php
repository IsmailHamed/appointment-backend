<?php


namespace App\Interfaces\Expert;


use App\Models\Expert;

interface ExpertInterface
{
    public function all();

    public function store();

    public function show(Expert $expert);

    public function update(Expert $expert);

    public function delete(Expert $expert);

    public function getAvailabilityTime(Expert $expert);


}
