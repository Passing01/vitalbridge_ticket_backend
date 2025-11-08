<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\User;
use App\Policies\QueuePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Appointment::class => QueuePolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        
        // Définir les portes d'autorisation
        Gate::define('viewQueue', [QueuePolicy::class, 'viewQueue']);
        Gate::define('callNext', [QueuePolicy::class, 'callNext']);
    }
}
