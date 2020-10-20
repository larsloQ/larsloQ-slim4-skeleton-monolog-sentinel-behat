<?php

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
use Phpmig\Migration\Migration;

require __DIR__ . '/../vendor/autoload.php';

class UserData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sentinel = (new Sentinel());
        // Register a new user
        $credentials = [
            'email'    => 'me@larslo',
            'password' => 'APw-121309213#',
        ];

        $user = $sentinel::registerAndActivate($credentials);
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Capsule::statement('SET FOREIGN_KEY_CHECKS=0;');
        $credentials = [
            'login' => 'me@larslo',
        ];

        $user = Sentinel::findByCredentials($credentials);
        $user->delete();
        Capsule::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
