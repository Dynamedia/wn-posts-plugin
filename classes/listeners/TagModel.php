<?php namespace Dynamedia\Posts\Classes\Listeners;
use Dynamedia\Posts\Models\TagSlug;
use Str;
use ValidationException;

class TagModel
{
    public function subscribe($event)
    {
        // Before Validate
        $event->listen('dynamedia.posts.tag.saving', function ($tag, $user) {
            if (!TagSlug::isAvailable($tag->id, $tag->slug)) {
                throw new ValidationException(['slug' => "Slug is not available"]);
            }
        });

        // Before Save
        $event->listen('dynamedia.posts.tag.saving', function ($tag, $user) {
            if (!$tag->slug) {
                $tag->slug = Str::slug($tag->name);
            }

            $tag->slug = Str::slug($tag->slug);

        });

        // After Save
        $event->listen('dynamedia.posts.tag.saved', function ($tag, $user) {
            // Create the tagslug relationship. Required for auto redirection on change
            // Must be validated as unique per tag (Translation can share)
            $tag->tagslugs()->firstOrCreate([
                'slug' => $tag->slug,
            ]);
        });

        // Before Delete
        $event->listen('dynamedia.posts.tag.deleting', function ($tag, $user) {
            $tag->posts()->detach();
            $tag->translations()->delete();
            $tag->tagslugs()->delete();
        });

        // After Delete
        $event->listen('dynamedia.posts.tag.deleted', function ($tag, $user) {

        });
    }
}
