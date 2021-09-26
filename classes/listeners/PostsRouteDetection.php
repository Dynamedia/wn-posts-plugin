<?php namespace Dynamedia\Posts\Classes\Listeners;

use App;
use Cms\Classes\Controller;
use Dynamedia\Posts\Classes\Rss\RssAll;
use Dynamedia\Posts\Classes\Rss\RssCategory;
use Dynamedia\Posts\Classes\Rss\RssTag;
use Dynamedia\Posts\Classes\Sitemap\SitemapAll;
use Dynamedia\Posts\Classes\Sitemap\SitemapCategory;
use Dynamedia\Posts\Classes\Sitemap\SitemapTag;
use Event;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use Dynamedia\Posts\Models\Category;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Tag;
use Dynamedia\Posts\Models\Settings;


class PostsRouteDetection
{
    private $slug;
    private $suffix;

    private $allowedSuffix = [
            'rss',
            'sitemap.xml'
        ];

    public function subscribe($event)
    {
        // Having completely semantic URLs is nice, but it causes post and category display routes to clash
        // We can deal with that by checking whether the provided slug is either a Post or a Category
        // It can't be both, as we don't allow it through validation. We will force render the relevant page.

        // If we're trying to match at the root URL, all non-existent routes will match for post-display or category-display
        // The display components will handle 404's if there is no post or category found

        // TODO some refactoring needed here, but lets get it working

        $event->listen('cms.page.beforeDisplay', function ($controller, $url, $page) {

            if (!$page) return;

            // todo move this - Set webpage in pge load cycle and then set post stuff if we have one
            $graph = App::make('dynamedia.posts.graph');
            $graph->getWebpage()
                ->setProperty("@id", Page::url($page->fileName) . "#wepbage")
                ->url(Page::url($page->fileName))
                ->title($page->title)
                ->description($page->meta_description);

            $controller->vars['schema'] = $graph;


            // Get info for potential clashing pages and the page the router actually matched
            $params = $controller->getRouter()->getParameters();


            $activeThemeCode = Theme::getActiveThemeCode();

            $this->parseSlugParams($controller);

            // Process slugs matching our cms pages
            $postPage = [
                'page' => $pg = Settings::get('postPage'),
                'url'  => $pg ? Page::url($pg, $params) : null
            ];
            $categoryPage = [
                'page' => $pg = Settings::get('categoryPage'),
                'url'  => $pg ? Page::url($pg, $params) : null
            ];
            $tagPage = [
                'page' => $pg = Settings::get('tagPage'),
                'url'  => $pg ? Page::url($pg, $params) : null
            ];
            $matchedPage = [
                'page' => $pg = $page->getFileNameParts()[0],
                'url' => $pg ? Page::url($pg, $params) : null
            ];

            // Logic attempts to avoid querying both Posts and Categories if at all possible
            // But no way to avoid if the post and category pages do clash

            // Post Page
            if ($matchedPage['url'] == $postPage['url']) {
                Event::fire('dynamedia.posts.matchedPostRoute');
                $post = Post::getPost(['optionsSlug' => $this->slug]);
            }

            // Category Page
            if ($matchedPage['url'] == $categoryPage['url']) {
                Event::fire('dynamedia.posts.matchedCategoryRoute');
                $category = Category::getCategory(['optionsSlug' => $this->slug]);
            }

            // Tag Page
            if ($matchedPage['url'] == $tagPage['url']) {
                Event::fire('dynamedia.posts.matchedTagRoute');
                $tag = Tag::getTag($this->slug);
            }

            if (!empty($post)) {
                $newPage = Page::loadCached($activeThemeCode, $postPage['page']);
                $post->getCmsLayout() ? $newPage->layout = $post->getCmsLayout() : null;
                App::instance('dynamedia.posts.post', $post);
                return $newPage;
            }

            // todo refactor rss/sitemap logic
            if (!empty($category)) {
                $newPage = Page::loadCached($activeThemeCode,$categoryPage['page']);
                $category->getCmsLayout() ? $newPage->layout = $category->getCmsLayout() : null;
                App::instance('dynamedia.posts.category', $category);
                if ($this->suffix === "rss") {
                    $feed = new RssCategory($category);
                    return $feed->makeViewResponse();
                }
                if ($this->suffix === 'sitemap.xml') {
                    $feed = new SitemapCategory($category);
                    return $feed->makeViewResponse();
                }

                return $newPage;
            }

            // Tags can't clash with Posts and Categories so can share the same name
            if (!empty($tag)) {
                $tag->getCmsLayout() ? $page->layout = $tag->getCmsLayout() : null;
                App::instance('dynamedia.posts.tag', $tag);
                if ($this->suffix === "rss") {
                    $feed = new RssTag($tag);
                    return $feed->makeViewResponse();
                }
                if ($this->suffix === 'sitemap.xml') {
                    $feed = new SitemapTag($tag);
                    return $feed->makeViewResponse();
                }
            }
        });
    }

    /**
     * Check for a relevant url slug parameter
     * @param $controller
     * @return string|null
     */
    private function parseSlugParams($controller)
    {

        $slug = false;

        if ($controller->param('postsPostSlug')) {
            $slug = $controller->param('postsPostSlug');
        } elseif ($controller->param('postsCategorySlug')) {
            $slug = $controller->param('postsCategorySlug');
        } elseif ($controller->param('postsFullPath')) {
            $slug = basename($controller->param('postsFullPath'));
        } elseif ($controller->param('postsCategoryPath')) {
            $slug = basename($controller->param('postsCategoryPath'));
        } elseif ($controller->param('postsTagSlug')) {
            $slug = $controller->param('postsTagSlug');
        }

        $this->setSlug($slug);
    }

    /**
     * Parse the slug to check for allowed suffix
     *
     * @param $slug
     */
    private function setSlug($slug)
    {
        // a default
        $this->slug = $slug;

        $slugArray = explode(".", $slug, 2);
        if (count($slugArray) > 1) {
            $suffix = array_pop($slugArray);
            // Only re-set the slug if it's in the allowed list
            if (in_array($suffix, $this->allowedSuffix)) {
                $this->suffix = $suffix;
                $this->slug = $slugArray[0];
            }
        }
    }
}
