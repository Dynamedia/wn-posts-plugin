<?php namespace Dynamedia\Posts\Classes\Acl;

class AccessControl
{
    public static function getAvailablePermissions()
    {
        // Publishers and developers have full access. Some restricted non-system roles are created for more control
        return [
            'dynamedia.posts.access_plugin' => [
                'tab' => 'Posts',
                'label' => 'Access Posts Plugin',
                'order' => 1000,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.create_posts' => [
                'tab' => 'Posts',
                'label' => 'Create Posts',
                'order' => 1010,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.categorize_posts' => [
                'tab' => 'Posts',
                'label' => 'Categorize Posts',
                'order' => 1020,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.tag_posts' => [
                'tab' => 'Posts',
                'label' => 'Tag Posts',
                'order' => 1030,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.set_layout' => [
                'tab' => 'Posts',
                'label' => 'Set Post Layout',
                'order' => 1040,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.publish_own_posts' => [
                'tab' => 'Posts',
                'label' => 'Publish Own Posts',
                'order' => 1050,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.unpublish_own_posts' => [
                'tab' => 'Posts',
                'label' => 'Unpublish Own Posts',
                'order' => 1060,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.edit_own_published_posts' => [
                'tab' => 'Posts',
                'label' => 'Edit Own Published Posts',
                'order' => 1070,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.delete_own_unpublished_posts' => [
                'tab' => 'Posts',
                'label' => 'Delete Own Unpublished Posts',
                'order' => 1080,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.delete_own_published_posts' => [
                'tab' => 'Posts',
                'label' => 'Delete Own Published Posts',
                'order' => 1090,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.publish_all_posts' => [
                'tab' => 'Posts',
                'label' => 'Publish All Posts',
                'order' => 1100,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.unpublish_all_posts' => [
                'tab' => 'Posts',
                'label' => 'Unpublish All Posts',
                'order' => 1110,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.edit_all_unpublished_posts' => [
                'tab' => 'Posts',
                'label' => 'Edit All Unpublished Posts',
                'order' => 1120,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.edit_all_published_posts' => [
                'tab' => 'Posts',
                'label' => 'Edit All Published Posts',
                'order' => 1130,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.delete_all_unpublished_posts' => [
                'tab' => 'Posts',
                'label' => 'Delete All Unpublished Posts',
                'order' => 1140,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.delete_all_published_posts' => [
                'tab' => 'Posts',
                'label' => 'Delete All Published Posts',
                'order' => 1150,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.assign_posts' => [
                'tab' => 'Posts',
                'label' => 'Assign Post to User',
                'order' => 1160,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.view_categories' => [
                'tab' => 'Posts',
                'label' => 'View Categories',
                'order' => 1170,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.manage_categories' => [
                'tab' => 'Posts',
                'label' => 'Manage Categories',
                'order' => 1180,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.view_tags' => [
                'tab' => 'Posts',
                'label' => 'View Tags',
                'order' => 1190,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.manage_tags' => [
                'tab' => 'Posts',
                'label' => 'Manage Tags',
                'order' => 1200,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.view_settings' => [
                'tab' => 'Posts',
                'label' => 'View Settings',
                'order' => 1210,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.manage_settings' => [
                'tab' => 'Posts',
                'label' => 'Manage Settings',
                'order' => 1220,
                'roles' => ['publisher', 'developer']
            ],
        ];
    }

    /**
     * Check if user has required permissions to edit
     * @param $user
     * @return bool
     */
    public static function userCanEditPost($post, $user)
    {   // isDirty prevents failure if setting the attribute
        if ($post->is_published && !$post->isDirty('is_published')) {
            if (!$user->hasAccess('dynamedia.posts.edit_all_published_posts')
                && !($user->hasAccess('dynamedia.posts.edit_own_published_posts')
                    && $user->id == $post->author_id)) {
                return false;
            } else {
                return true;
            }
        } else {
            if (!$user->hasAccess('dynamedia.posts.edit_all_unpublished_posts')
                && $user->id != $post->author_id) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * Check if user has required permissions to delete
     * @param $user
     * @return bool
     */
    public static function userCanDeletePost($post, $user)
    {
        if ($post->is_published) {
            if (!$user->hasAccess('dynamedia.posts.delete_all_published_posts')
                && !($user->hasAccess('dynamedia.posts.delete_own_published_posts')
                    && $user->id == $post->author_id)) {
                return false;
            } else {
                return true;
            }
        } else {
            if (!$user->hasAccess('dynamedia.posts.delete_all_unpublished_posts')
                && !($user->hasAccess('dynamedia.posts.delete_own_unpublished_posts')
                    && $user->id == $post->author_id)) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * Check if user has required permissions to publish
     * @param $user
     * @return bool
     */
    public static function userCanPublishPost($post, $user)
    {
        if (!$user->hasAccess('dynamedia.posts.publish_all_posts')
            && !($user->hasAccess('dynamedia.posts.publish_own_posts')
                && $user->id == $post->author_id)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has required permissions to unpublish
     * @param $user
     * @return bool
     */
    public static function userCanUnpublishPost($post, $user)
    {
        if (!$user->hasAccess('dynamedia.posts.unpublish_all_posts')
            && !($user->hasAccess('dynamedia.posts.unpublish_own_posts')
                && $user->id == $post->author_id)) {
            return false;
        } else {
            return true;
        }
    }
}
