<?php namespace Dynamedia\Posts\Updates;

use DB;
use Schema;
use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

/**
 * The version history has to be manipulated as October compatible "v" prefixed keys are not working well with winter
 * and PHP v7. This will allow updates to run and not show a duplicated history
 */
class NormalizePluginHistory extends Migration
{
    public function up()
    {
        DB::table('system_plugin_history')
            ->where('code', 'Dynamedia.Posts')
            ->where('version', 'like', "v%")
            ->delete();
    }

    public function down()
    {
        // Do nothing
    }
}
