<?php namespace Dynamedia\Posts\Updates;

use Schema;
use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('dynamedia_posts_settings')) return;

        Schema::create('dynamedia_posts_settings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dynamedia_posts_settings');
    }
}
