<?php

declare(strict_types=1);

namespace Elastik\Statistics\Providers;

use Illuminate\Routing\Router;
use Elastik\Statistics\Models\Path;
use Elastik\Statistics\Models\Agent;
use Elastik\Statistics\Models\Datum;
use Elastik\Statistics\Models\Geoip;
use Elastik\Statistics\Models\Route;
use Elastik\Statistics\Models\Device;
use Elastik\Statistics\Models\Request;
use Elastik\Statistics\Models\Platform;
use Illuminate\Support\ServiceProvider;
use Rinvex\Support\Traits\ConsoleTools;
use Elastik\Statistics\Console\Commands\MigrateCommand;
use Elastik\Statistics\Console\Commands\PublishCommand;
use Elastik\Statistics\Http\Middleware\TrackStatistics;
use Elastik\Statistics\Console\Commands\RollbackCommand;

class StatisticsServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MigrateCommand::class => 'command.elastik.statistics.migrate',
        PublishCommand::class => 'command.elastik.statistics.publish',
        RollbackCommand::class => 'command.elastik.statistics.rollback',
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'elastik.statistics');

        // Bind eloquent models to IoC container
        $this->registerModels([
            'elastik.statistics.datum' => Datum::class,
            'elastik.statistics.request' => Request::class,
            'elastik.statistics.agent' => Agent::class,
            'elastik.statistics.geoip' => Geoip::class,
            'elastik.statistics.route' => Route::class,
            'elastik.statistics.device' => Device::class,
            'elastik.statistics.platform' => Platform::class,
            'elastik.statistics.path' => Path::class,
        ]);

        // Register console commands
        $this->registerCommands($this->commands);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Router $router)
    {
        // Publish Resources
        $this->publishesConfig('elastik/laravel-statistics');
        $this->publishesMigrations('elastik/laravel-statistics');
        ! $this->autoloadMigrations('elastik/laravel-statistics') || $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Push middleware to web group
        $router->pushMiddlewareToGroup('web', TrackStatistics::class);
    }
}
