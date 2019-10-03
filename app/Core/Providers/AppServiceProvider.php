<?php

namespace Ollieread\Core\Providers;

use BotMan\BotMan\BotMan;
use Illuminate\Auth\SessionGuard;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Ollieread\Articles\Observers\CategoryObserver;
use Ollieread\Models\Project;
use Ollieread\Models\Series;
use Ollieread\Observers\ProjectObserver;
use Ollieread\Observers\SeriesObserver;
use Ollieread\Users\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        //$viewFactory = app(Factory::class);
        //$viewFactory->composer('article.partials.sidebar.categories', CategoryMenuComposer::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register the action handler
        Router::macro('action', function (string $prefix, string $name, string $actionClass) {
            return Route::group(['prefix' => $prefix], static function (Router $router) use ($actionClass, $name) {
                $router->get('/', '\\' . ltrim($actionClass, '\\') . '@get')->name(sprintf('%s.create', $name));
                $router->post('/', '\\' . ltrim($actionClass, '\\') . '@post')->name(sprintf('%s.store', $name));
            });
        });

        $this->app->bind(SessionGuard::class, function () {
            return $this->app['auth']->guard('user');
        });

        $this->app->bind(User::class, function () {
            return $this->app['auth']->guard('user')->user();
        });
    }
}
