<?php

use Phpmig\Migration\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

use Cartalyst\Sentinel\Native\Facades\Sentinel;

require __DIR__ . '/../vendor/autoload.php';

class UserTable extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Capsule::schema()->create('activations', function($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('code');
            $table->boolean('completed')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
        Capsule::schema()->create('persistences', function($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('code');
            $table->timestamps();
            $table->engine = 'InnoDB';
            $table->unique('code');
        });
        Capsule::schema()->create('reminders', function($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('code');
            $table->boolean('completed')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
        Capsule::schema()->create('roles', function($table) {
            $table->increments('id');
            $table->string('slug');
            $table->string('name');
            $table->text('permissions')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
            $table->unique('slug');
        });
        Capsule::schema()->create('role_users', function($table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->nullableTimestamps();
            $table->engine = 'InnoDB';
            $table->primary(['user_id', 'role_id']);
        });
        Capsule::schema()->create('throttle', function($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('type');
            $table->string('ip')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
            $table->index('user_id');
        });
        Capsule::schema()->create('users', function($table) {
            $table->increments('id');
            $table->string('email');
            $table->string('password');
            $table->text('permissions')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
            $table->unique('email');
        });


    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Capsule::statement('SET FOREIGN_KEY_CHECKS=0;');
        Capsule::schema()->drop('activations');
        Capsule::schema()->drop('persistences');
        Capsule::schema()->drop('reminders');
        Capsule::schema()->drop('roles');
        Capsule::schema()->drop('role_users');
        Capsule::schema()->drop('throttle');
        Capsule::schema()->drop('users');
     Capsule::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}