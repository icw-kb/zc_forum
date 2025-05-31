<?php

namespace App\Providers;

use App\Services\Traits\Restrictable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class RestrictionServiceProvider extends ServiceProvider
{
    use Restrictable;
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $models =$this->registerModelHasRestrictions();
        $this->registerGates($models);
    }
    protected function registerModelHasRestrictions()
    {
        return ['forum', 'forum-group'];
    }

    protected function registerGates($models = [])
    {
        $rules = self::getRestrictableGateTypes();
        foreach ($models as $model) {
            foreach ($rules as $rule) {
                Gate::define($rule, function (User $user) use ($rule, $model) {
                    return $this->restrictableCheck($model . '-' . $rule, $user);
                });
            }
        }
    }
}
