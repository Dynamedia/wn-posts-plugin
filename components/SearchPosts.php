<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Models\Post;
use Input;

class SearchPosts extends ComponentBase
{
    public $posts;
    public $searchQuery;

    public function componentDetails()
    {
        return [
            'name'        => 'Search Posts',
            'description' => 'Display search results'
        ];
    }

    public function defineProperties()
    {
        return [
            'postsLimit' => [
                'title'             => 'Total posts',
                'description'       => 'Limit the number of posts to fetch',
                'type'              => 'string',
                'validationPattern' => '^[1-9]\d*$',
                'validationMessage' => 'Please enter a positive whole number or leave blank',
                'default'           => '',
                'showExternalParam' => false,
            ],
            'postsPerPage' => [
                'title'             => 'Posts per page',
                'description'       => 'Limit the number of posts per page',
                'type'              => 'string',
                'validationPattern' => '^[1-9]\d*$',
                'validationMessage' => 'Please enter a positive whole number',
                'default'           => '10',
                'showExternalParam' => false,
            ],
            'noPostsMessage' => [
                'title'             => 'No Posts Message',
                'description'       => 'Message to display when no posts are found',
                'type'              => 'string',
                'default'           => "No posts found",
                'showExternalParam' => false,
            ],
            'sortOrder' => [
                'title'       => 'Sort Order',
                'description' => 'Sort the fetched posts',
                'type'        => 'dropdown',
                'default'     => 'published_at desc',
                'showExternalParam' => false,
            ],
        ];
    }

    public function onRun()
    {
        $this->setSearchQuery();

        if (!$this->searchQuery) return redirect('/');

        $this->setPosts();
    }

    private function setPosts()
    {
        $options = [
            'searchQuery' => $this->searchQuery,
            'limit' => (int) $this->property('postsLimit'),
            'perPage' => (int) $this->property('postsPerPage'),
        ];

        $query = new Post();
        $this->posts = $query->getPostsList($options);
    }

    private function setSearchQuery()
    {
        $this->searchQuery = Input::get('q');
    }
}
