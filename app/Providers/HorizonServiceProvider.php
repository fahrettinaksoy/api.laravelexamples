<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        Horizon::routeMailNotificationsTo('admin@laravelexamples.com');
    }

    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user = null) {
            return app()->environment('local');
        });
    }
}
