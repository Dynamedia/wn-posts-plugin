<?php namespace Dynamedia\Posts\Updates;

use Schema;
use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

/**
 * CreatePostTranslationsTable Migration
 */
class CreatePostTranslationsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('dynamedia_posts_post_translations')) return;

        Schema::create('dynamedia_posts_post_translations', function (Blueprint $table) {
            $table->increments('id');
            // Keep nullable initially
            $table->integer('native_id')->unsigned()->nullable()->index();
            // Winter translate
            $table->integer('locale_id')->unsigned()->nullable()->index();
            $table->string('slug')->index();
            $table->string('title');
            $table->json('images')->nullable()->default(null);
            $table->text('excerpt')->nullable()->default(null);
            $table->json('body_document')->nullable()->default(null);
            // searchable representation of the body document without having a cross-db solution for generating columns
            $table->longText('body_text')->nullable()->default(null);
            $table->json('seo')->nullable()->default(null);
            $table->boolean('show_contents')->default(true);
            $table->string('cms_layout')->default('__inherit__');
            $table->boolean('is_published')->index()->default(false);
            $table->timestamps();

            $table->foreign('native_id')->references('id')->on('dynamedia_posts_posts')
                ->onDelete('cascade');
            $table->foreign('locale_id')->references('id')->on('winter_translate_locales')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('dynamedia_posts_post_translations', function (Blueprint $table) {
            $table->dropForeign('dynamedia_posts_post_translations_native_id_foreign');
            $table->dropForeign('dynamedia_posts_post_translations_locale_id_foreign');
        });

        Schema::dropIfExists('dynamedia_posts_post_translations');
    }
}
