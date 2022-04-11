<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForgotPasswordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forgot_passwords', function (Blueprint $table) {
            $expired = strtotime("+1 Hours");

            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('email');
            $table->string('token');
            $table->smallInteger('is_used')->default(0);
            $table->smallInteger('is_verified')->default(0);
            $table->smallInteger('is_valid')->default(1);
            $table->dateTime('expired_at');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forgot_passwords');
    }
}
