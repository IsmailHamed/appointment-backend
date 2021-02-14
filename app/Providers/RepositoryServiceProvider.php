<?php

namespace App\Providers;

use App\Interfaces\Auth\AuthInterface;
use App\Interfaces\Expert\ExpertBookingInterface;
use App\Interfaces\Expert\ExpertInterface;
use App\Interfaces\Expert\ExpertWorkHourInterface;
use App\Interfaces\Me\MeInterface;
use App\Repositories\Auth\AuthRepository;
use App\Repositories\Expert\ExpertBookingRepository;
use App\Repositories\Expert\ExpertRepository;
use App\Repositories\Expert\ExpertWorkHourRepository;
use App\Repositories\Me\MeRepository;
use Illuminate\Support\ServiceProvider;


class RepositoryServiceProvider extends ServiceProvider

{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            AuthInterface::class,
            AuthRepository::class
        );
        $this->app->bind(
            MeInterface::class,
            MeRepository::class
        );
        $this->app->bind(
            ExpertInterface::class,
            ExpertRepository::class
        );
        $this->app->bind(
            ExpertBookingInterface::class,
            ExpertBookingRepository::class
        );
        $this->app->bind(
            ExpertWorkHourInterface::class,
            ExpertWorkHourRepository::class
        );

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
//
    }

}
