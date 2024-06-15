<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Action;
use App\Models\Cafe;
use App\Policies\ActionPolicy;
use App\Policies\CafePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Cafe::class => CafePolicy::class,  // TODO
        Action::class => ActionPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
//        Passport::routes();  // 新版本Passport不要手动注册路由了
    }
}
