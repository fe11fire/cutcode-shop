<?php

namespace App\Providers;

use App\Http\Kernel;
use Carbon\CarbonInterval;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Services\Telegram\TelegramBotApi;
use Services\Telegram\TelegramBotApiContract;
use Support\Testing\FakerImageProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Generator::class, function ($app) {
            $faker = Factory::create();
            $faker->addProvider(new FakerImageProvider($faker));
            return $faker;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Model::shouldBeStrict(!app()->isProduction());

        $this->app->bind(TelegramBotApiContract::class, TelegramBotApi::class);

        if (app()->isProduction()) {
            app(Kernel::class)->whenRequestLifecycleIsLongerThan(
                CarbonInterval::seconds(4),
                function () {
                    logger()->channel('telegram')->debug('whenRequestLifecycleIsLongerThan: ' . request()->url());
                }
            );

            DB::listen(function ($query) {
                if ($query->time > 1000) {
                    logger()->channel('telegram')->debug('query longer than 1s: ' . $query->sql, $query->bindings);
                }
            });
        }
    }
}
