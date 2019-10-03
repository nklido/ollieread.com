<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Ollieread\Articles\Models\Article;
use Ollieread\Articles\Operations\GetCategory;
use Ollieread\Articles\Operations\GetOrCreateTags;
use Ollieread\Articles\Operations\GetTopics;
use Ollieread\Articles\Operations\GetVersions;
use Ollieread\Core\Services\Redirects;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = require __DIR__ . '/data/articles.php';

        foreach ($data as $row) {
            $article = (Article::query()->where('slug', '=', $row['slug'])->first() ?? new Article)
                ->fill(Arr::except($row, ['category', 'topics', 'versions', 'tags', 'redirects']));

            if ($row['category']) {
                $category = (new GetCategory)->setSlug($row['category'])->perform();
                $article->category()->associate($category);
            }

            $article->content = file_get_contents(__DIR__ . '/data/articles/' . $row['slug'] . '.md');

            if ($article->save()) {
                if ($row['topics']) {
                    $topics = (new GetTopics)->setSlugs($row['topics'])->perform();
                    $article->topics()->sync($topics->pluck('id'));
                }

                if ($row['versions']) {
                    $versions = (new GetVersions)->setSlugs($row['versions'])->perform();
                    $article->versions()->sync($versions->pluck('id'));
                }

                if ($row['tags']) {
                    $tags = (new GetOrCreateTags)->setNames($row['tags'])->perform();
                    $article->tags()->sync($tags->pluck('id'));
                }

                if ($row['redirects']) {
                    foreach ($row['redirects'] as $redirect) {
                        Redirects::create($redirect, route('articles:article', $article->slug));
                    }
                }

                $this->command->info(sprintf('Article %s added/updated', $article->name));
            }
        }
    }
}
