<?php

namespace Ollieread\Articles\Routes;

use Illuminate\Routing\Router;
use Ollieread\Articles\Actions;
use Ollieread\Core\Support\Contracts\Routes;

class ArticleRoutes implements Routes
{
    public function __invoke(Router $router)
    {
        $router->get('/')->name('index')->uses(Actions\Listing::class);
        $router->get('/schedule')->name('schedule')->uses(Actions\Schedule::class);
        $router->get('/versions')->name('versions')->uses(Actions\Versions::class);
        $router->get('/topics')->name('topics')->uses(Actions\Topics::class);
        $router->get('/category/{categorySlug}')->name('category')->uses(Actions\Category::class);
        $router->get('/filter/{filterString?}')->name('filter')->uses(Actions\Filter::class);
        $router->get('/series/{seriesSlug}')->name('series')->uses(Actions\Series::class);
        $router->get('/version/{versionSlug}')->name('version')->uses(Actions\Version::class);
        $router->get('/topic/{topicSlug}')->name('topic')->uses(Actions\Topic::class);
        $router->get('/{articleSlug}/comments')->name('article.comments')->uses(Actions\Comments::class);
        $router->post('/{articleSlug}/comment')->name('article.comments.create')->uses(Actions\Comments\Create::class);
        $router->get('/{articleSlug}')->name('article')->uses(Actions\Article::class);
        $router->get('/{seriesSlug}/{articleSlug}')->name('series.article')->uses(Actions\Article::class);
    }

    public function name(): ?string
    {
        return 'articles:';
    }

    public function prefix(): ?string
    {
        return '/articles';
    }
}
