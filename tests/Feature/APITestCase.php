<?php

namespace Tests\Feature;

use App\Traits\ApiResponse;
use App\Traits\TimeHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helper;
use Tests\TestCase;

abstract class APITestCase extends TestCase
{
    use RefreshDatabase, WithFaker, ApiResponse, Helper, TimeHelper;

    protected function prepareUrlForRequest($uri): string
    {
        return 'api/' . $uri;
    }

}
