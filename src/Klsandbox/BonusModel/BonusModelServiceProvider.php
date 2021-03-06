<?php

namespace Klsandbox\BonusModel;

use Illuminate\Support\ServiceProvider;
use Klsandbox\SiteConfig\Services\SiteConfig;

class BonusModelServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../../database/migrations/' => database_path('/migrations'),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../../../config/' => config_path(),
        ], 'config');

        SiteConfig::macro('has_bonus', function () {
            return config('bonus') != null;
        });

        \Blade::extend(function ($view, $compiler) {
            $pattern = "/(?<!\w)(\s*)@blink\(\s*(.*?)\)/";

            return preg_replace($pattern, '$1'
                . '<?php if($auth->admin || $auth->id == $2->user->id || $auth->id == $2->user->referral_id|| $auth->id == $2->user->new_referral_id) {?>' . PHP_EOL
                . '<a href="/bonus-management/view/<?php echo $2->id ?>">' . PHP_EOL
                . '#<?php echo (1024 + $2->id) ?>' . PHP_EOL
                . '</a>' . PHP_EOL
                . '<?php } else { ?>' . PHP_EOL
                . '<?php echo (1024 + $2->id) ?>' . PHP_EOL
                . '<?php }?>', $view);
        });
    }
}
